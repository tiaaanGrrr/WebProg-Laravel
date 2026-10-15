<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderReportRequest;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOrderReportController extends Controller
{
    public function index(OrderReportRequest $request): View
    {
        $filters = $request->filters();
        $query = $this->orders($filters);
        $summary = (clone $query)->select('status')->selectRaw('COUNT(*) as count, SUM(total) as value')->groupBy('status')->get()->keyBy('status');

        return view('admin.reports.orders', [
            'filters' => $filters,
            'orders' => $query->withCount('items')->latest('id')->paginate(10)->appends($filters),
            'orderCount' => (int) $summary->sum('count'),
            'totalValue' => (int) $summary->sum('value'),
            'completedValue' => (int) ($summary->get('selesai')?->value ?? 0),
            'cancelledCount' => (int) ($summary->get('dibatalkan')?->count ?? 0),
        ]);
    }

    public function export(OrderReportRequest $request): StreamedResponse
    {
        $filters = $request->filters();
        $query = $this->orders($filters)->withCount('items')->latest('id');
        $filename = 'laporan-pesanan-'.$filters['from'].'-sampai-'.$filters['to'].'.csv';

        return response()->streamDownload(function () use ($query): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Nomor Pesanan', 'Tanggal Dibuat (WIB)', 'Status', 'Jenis Produk', 'Total Produk (Rp)'], ',', '"', '');
            foreach ($query->cursor() as $order) {
                fputcsv($output, [
                    $order->reference,
                    $order->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    Order::STATUS_LABELS[$order->status],
                    $order->items_count,
                    $order->total,
                ], ',', '"', '');
            }
            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'no-store']);
    }

    private function orders(array $filters): Builder
    {
        $from = CarbonImmutable::createFromFormat('!Y-m-d', $filters['from'], 'Asia/Jakarta');
        $to = CarbonImmutable::createFromFormat('!Y-m-d', $filters['to'], 'Asia/Jakarta')->addDay();
        $query = Order::where('created_at', '>=', $from->setTimezone(config('app.timezone')))
            ->where('created_at', '<', $to->setTimezone(config('app.timezone')));
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }
}
