<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminOrderReportTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    public function test_reports_and_exports_are_admin_only(): void
    {
        $this->get('/admin/reports/orders')->assertRedirect('/admin/login');
        $this->get('/admin/reports/orders/export')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin/reports/orders')->assertForbidden();
        $this->get('/admin/reports/orders/export')->assertForbidden();
    }

    public function test_report_includes_entire_wib_days_and_separates_completed_value(): void
    {
        $start = Order::factory()->create(['created_at' => '2026-10-14 17:00:00', 'status' => 'baru', 'total' => 100]);
        $end = Order::factory()->create(['created_at' => '2026-10-15 16:59:59', 'status' => 'selesai', 'total' => 200]);
        Order::factory()->create(['created_at' => '2026-10-15 05:00:00', 'status' => 'dibatalkan', 'total' => 300]);
        $before = Order::factory()->create(['created_at' => '2026-10-14 16:59:59', 'total' => 1000]);
        $after = Order::factory()->create(['created_at' => '2026-10-15 17:00:00', 'total' => 2000]);

        $this->actingAs($this->admin())->get('/admin/reports/orders?from=2026-10-15&to=2026-10-15')->assertOk()
            ->assertSee($start->reference)->assertSee($end->reference)->assertDontSee($before->reference)->assertDontSee($after->reference)
            ->assertViewHas('orderCount', 3)->assertViewHas('totalValue', 600)->assertViewHas('completedValue', 200)->assertViewHas('cancelledCount', 1);
        $this->get('/admin/reports/orders?from=2026-10-15&to=2026-10-15&status=selesai')->assertOk()
            ->assertSee($end->reference)->assertDontSee($start->reference)->assertViewHas('orderCount', 1)->assertViewHas('totalValue', 200)->assertViewHas('cancelledCount', 0);
    }

    public function test_default_report_uses_current_month_through_today_in_wib(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-15 08:00:00', 'Asia/Jakarta'));
        $inside = Order::factory()->create(['created_at' => '2026-09-30 17:00:00']);
        $outside = Order::factory()->create(['created_at' => '2026-09-30 16:59:59']);

        $this->actingAs($this->admin())->get('/admin/reports/orders')->assertOk()->assertSee($inside->reference)->assertDontSee($outside->reference)
            ->assertViewHas('filters', ['from' => '2026-10-01', 'to' => '2026-10-15']);
    }

    public function test_csv_exports_all_filtered_results_and_matches_report_totals(): void
    {
        $orders = Order::factory()->count(12)->create(['created_at' => '2026-10-15 05:00:00', 'status' => 'baru', 'total' => 125000]);
        OrderItem::factory()->for($orders[0])->create();
        $excluded = Order::factory()->create(['created_at' => '2026-10-15 05:00:00', 'status' => 'dibatalkan']);
        $filters = 'from=2026-10-15&to=2026-10-15&status=baru';
        $this->actingAs($this->admin())->get('/admin/reports/orders?'.$filters)->assertOk()
            ->assertViewHas('orderCount', 12)->assertViewHas('totalValue', 1500000)->assertSee('status=baru', false)->assertSee('page=2', false);

        $response = $this->get('/admin/reports/orders/export?'.$filters);

        $response->assertOk()->assertDownload('laporan-pesanan-2026-10-15-sampai-2026-10-15.csv')->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $csv = $response->streamedContent();
        $this->assertSame("\xEF\xBB\xBF", substr($csv, 0, 3));
        $rows = array_map(fn (string $line): array => str_getcsv($line, ',', '"', ''), explode("\n", trim(substr($csv, 3))));
        $this->assertCount(13, $rows);
        $this->assertSame(['Nomor Pesanan', 'Tanggal Dibuat (WIB)', 'Status', 'Jenis Produk', 'Total Produk (Rp)'], $rows[0]);
        $this->assertSame([$orders[0]->reference, '2026-10-15 12:00:00', 'Baru', '1', '125000'], $rows[12]);
        $this->assertStringNotContainsString($excluded->reference, $csv);
    }

    public function test_invalid_dates_status_and_reversed_ranges_are_rejected(): void
    {
        $this->actingAs($this->admin())->getJson('/admin/reports/orders?from=2026-10-16&to=2026-10-15')->assertUnprocessable()->assertJsonValidationErrors('to');
        $this->getJson('/admin/reports/orders/export?from=invalid&to=2026-02-30&status=invalid')->assertUnprocessable()->assertJsonValidationErrors(['from', 'to', 'status']);
        $this->getJson('/admin/reports/orders?from=&to=')->assertUnprocessable()->assertJsonValidationErrors(['from', 'to']);
    }

    public function test_empty_reports_and_csv_have_no_fabricated_orders(): void
    {
        $this->actingAs($this->admin())->get('/admin/reports/orders?from=2026-10-15&to=2026-10-15')->assertOk()
            ->assertSee('Tidak ada pesanan pada periode dan status yang dipilih.')->assertViewHas('orderCount', 0)->assertViewHas('totalValue', 0)->assertViewHas('completedValue', 0);

        $csv = $this->get('/admin/reports/orders/export?from=2026-10-15&to=2026-10-15')->assertOk()->streamedContent();
        $this->assertCount(1, explode("\n", trim($csv)));
    }
}
