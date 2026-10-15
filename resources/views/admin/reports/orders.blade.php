@extends('layouts.app')
@section('title', 'Laporan Pesanan | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page">
    <div class="section-heading"><p class="eyebrow">Rekap Toko</p><h1>Laporan Pesanan</h1><p>Ringkasan berdasarkan tanggal pesanan dibuat dalam WIB. Semua nilai berasal dari pesanan demo.</p></div>
    @include('admin.navigation')
    @include('admin.messages')
    <form class="admin-filters" action="{{ route('admin.reports.orders') }}" method="GET">
        <label>Tanggal mulai<input type="date" name="from" value="{{ $filters['from'] }}" required></label>
        <label>Tanggal akhir<input type="date" name="to" value="{{ $filters['to'] }}" required></label>
        <label>Status<select name="status"><option value="">Semua status</option>@foreach(\App\Models\Order::STATUS_LABELS as $value => $label)<option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></label>
        <button class="site-button" type="submit">TAMPILKAN</button><a class="site-button site-button--outline" href="{{ route('admin.reports.orders') }}">BULAN INI</a>
    </form>
    <div class="dashboard-metrics">
        <article class="dashboard-metric"><h2>Jumlah Pesanan</h2><strong>{{ number_format($orderCount, 0, ',', '.') }}</strong><p>Seluruh hasil filter</p></article>
        <article class="dashboard-metric"><h2>Nilai Seluruh Hasil</h2><strong class="dashboard-metric__money">Rp {{ number_format($totalValue, 0, ',', '.') }}</strong><p>Termasuk pesanan dibatalkan jika masuk filter</p></article>
        <article class="dashboard-metric"><h2>Nilai Pesanan Selesai</h2><strong class="dashboard-metric__money">Rp {{ number_format($completedValue, 0, ',', '.') }}</strong><p>Hanya pesanan berstatus selesai</p></article>
        <article class="dashboard-metric"><h2>Dibatalkan</h2><strong>{{ number_format($cancelledCount, 0, ',', '.') }}</strong><p>Jumlah pesanan dibatalkan dalam hasil filter</p></article>
    </div>
    <div class="admin-actions"><a class="site-button" href="{{ route('admin.reports.orders.export', $filters) }}">UNDUH CSV</a><p class="stock-note">CSV memuat semua hasil filter, bukan hanya halaman ini.</p></div>
    <p class="stock-note">Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan.</p>
    <div class="admin-table-wrap"><table class="admin-table">
        <caption class="sr-only">Hasil laporan pesanan</caption>
        <thead><tr><th>Nomor Pesanan</th><th>Tanggal (WIB)</th><th>Status</th><th>Jenis Produk</th><th>Total Produk</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($orders as $order)
            <tr><td>{{ $order->reference }}</td><td>{{ $order->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td><td><span class="order-status order-status--{{ $order->status }}">{{ \App\Models\Order::STATUS_LABELS[$order->status] }}</span></td><td>{{ $order->items_count }}</td><td>Rp {{ number_format($order->total, 0, ',', '.') }}</td><td><a class="site-button site-button--outline" href="{{ route('admin.orders.show', $order) }}">DETAIL</a></td></tr>
        @empty<tr><td colspan="6">Tidak ada pesanan pada periode dan status yang dipilih.</td></tr>@endforelse</tbody>
    </table></div>
    {{ $orders->onEachSide(1)->links('admin.pagination') }}
</section>
@endsection
