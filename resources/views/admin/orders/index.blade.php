@extends('layouts.app')
@section('title', 'Pesanan Masuk | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page">
    <div class="section-heading"><p class="eyebrow">Pengelolaan Toko</p><h1>Pesanan Masuk</h1><p>Daftar pesanan demo yang tersimpan dari checkout.</p></div>
    @include('admin.navigation')
    @include('admin.messages')
    @include('admin.orders.filters')
    <p class="stock-note">Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan.</p>
    <div class="admin-table-wrap"><table class="admin-table">
        <caption class="sr-only">Daftar pesanan masuk</caption>
        <thead><tr><th>Nomor pesanan</th><th>Penerima</th><th>Produk</th><th>Total</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($orders as $order)
            <tr><td>{{ $order->reference }}</td><td>{{ $order->customer_name }}</td><td>{{ $order->items_count }} jenis</td><td>Rp {{ number_format($order->total, 0, ',', '.') }}</td><td><span class="order-status order-status--{{ $order->status }}">{{ \App\Models\Order::STATUS_LABELS[$order->status] }}</span></td><td>{{ $order->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td><td><a class="site-button site-button--outline" href="{{ route('admin.orders.show', $order) }}">DETAIL</a></td></tr>
        @empty<tr><td colspan="7">Tidak ada pesanan pada daftar ini. Coba kata kunci atau status lain.</td></tr>@endforelse</tbody>
    </table></div>
    {{ $orders->onEachSide(1)->links('admin.pagination') }}
</section>
@endsection
