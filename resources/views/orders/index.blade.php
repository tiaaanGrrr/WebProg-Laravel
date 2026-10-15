@extends('layouts.app')
@section('title', 'Pesanan Saya | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page">
    <div class="section-heading"><p class="eyebrow">Riwayat Belanja</p><h1>Pesanan Saya</h1><p>Lihat rincian dan status terbaru pesanan Anda.</p></div>
    <p class="admin-notice">Riwayat sementara dari sesi browser ini, maksimal 20 pesanan terbaru. Jika sesi berakhir, daftar ini tidak tersedia; pesanan tetap tersimpan pada admin toko.</p>
    <p class="stock-note">Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan.</p>
    @forelse($orders as $order)
        <article class="customer-order-card">
            <div><h2>{{ $order->reference }}</h2><p>{{ $order->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p><p>{{ $order->items_count }} jenis produk</p></div>
            <div><span class="order-status order-status--{{ $order->status }}">{{ \App\Models\Order::STATUS_LABELS[$order->status] }}</span><p class="price"><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></p></div>
            <a class="site-button site-button--outline" href="{{ route('orders.show', $order->reference) }}">LIHAT DETAIL</a>
        </article>
    @empty
        <div class="empty-state"><h2>Belum Ada Pesanan di Sini</h2><p>Buat pesanan melalui keranjang untuk melihat riwayatnya di halaman ini.</p></div>
    @endforelse
    {{ $orders->onEachSide(1)->links('admin.pagination') }}
    <div class="admin-actions"><a class="site-button" href="{{ route('products.index') }}">LIHAT KOLEKSI</a></div>
</section>
@endsection
