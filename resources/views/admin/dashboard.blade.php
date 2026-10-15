@extends('layouts.app')
@section('title', 'Dashboard Admin | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page">
    <div class="section-heading"><p class="eyebrow">Pengelolaan Toko</p><h1>Dashboard Admin</h1><p>Ringkasan koleksi dan pesanan Cahaya Sembahyang.</p></div>
    @include('admin.navigation')
    <div class="dashboard-metrics">
        <article class="dashboard-metric"><h2>Total Produk</h2><strong>{{ number_format($productCount, 0, ',', '.') }}</strong><p>Jenis produk dalam katalog</p></article>
        <article class="dashboard-metric"><h2>Total Pesanan</h2><strong>{{ number_format($orderCount, 0, ',', '.') }}</strong><p>Seluruh status pesanan</p></article>
        <article class="dashboard-metric"><h2>Stok Menipis</h2><strong>{{ number_format($lowStockCount, 0, ',', '.') }}</strong><p>Produk dengan stok 1–5; {{ $outOfStockCount }} produk habis</p></article>
        <article class="dashboard-metric"><h2>Nilai Pesanan Selesai</h2><strong class="dashboard-metric__money">Rp {{ number_format($completedValue, 0, ',', '.') }}</strong><p>Simulasi dari pesanan berstatus selesai</p></article>
    </div>
    <section class="dashboard-statuses" aria-labelledby="order-statuses-title">
        <h2 id="order-statuses-title">Status Pesanan</h2>
        <div class="dashboard-status-grid">
            @foreach(\App\Models\Order::STATUS_LABELS as $status => $label)
                <article><span class="order-status order-status--{{ $status }}">{{ $label }}</span><strong>{{ $statusCounts[$status] }}</strong><span>pesanan</span></article>
            @endforeach
        </div>
    </section>
    <div class="dashboard-panels">
        <section class="dashboard-panel" aria-labelledby="latest-orders-title">
            <div class="dashboard-panel__heading"><h2 id="latest-orders-title">Pesanan Terbaru</h2><a href="{{ route('admin.orders.index') }}">Lihat semua</a></div>
            @forelse($latestOrders as $order)
                <a class="dashboard-row" href="{{ route('admin.orders.show', $order) }}">
                    <div><strong>{{ $order->reference }}</strong><p>{{ $order->customer_name }}</p><p>{{ $order->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p></div>
                    <div><span class="order-status order-status--{{ $order->status }}">{{ \App\Models\Order::STATUS_LABELS[$order->status] }}</span><p>Rp {{ number_format($order->total, 0, ',', '.') }}</p></div>
                </a>
            @empty<p class="dashboard-empty">Belum ada pesanan. Pesanan dari checkout akan tampil di sini.</p>@endforelse
        </section>
        <section class="dashboard-panel" aria-labelledby="low-stock-title">
            <div class="dashboard-panel__heading"><h2 id="low-stock-title">Perlu Tambah Stok</h2><a href="{{ route('admin.products.index') }}">Kelola produk</a></div>
            <p class="stock-note">Menampilkan maksimal 5 produk dengan stok 0–5, dimulai dari stok terendah.</p>
            @forelse($lowStockProducts as $product)
                <a class="dashboard-row" href="{{ route('admin.products.edit', $product) }}">
                    <div class="admin-product"><img src="{{ $product->image_url }}" alt="" width="48" height="48"><div><strong>{{ $product->name }}</strong><p>{{ $product->category }}</p></div></div>
                    <span class="order-status">{{ $product->stock === 0 ? 'Habis' : 'Sisa '.$product->stock }}</span>
                </a>
            @empty<p class="dashboard-empty">{{ $productCount ? 'Stok aman. Tidak ada produk dengan stok 5 atau kurang.' : 'Belum ada produk. Tambahkan produk untuk mulai mengelola stok.' }}</p>@endforelse
        </section>
    </div>
</section>
@endsection
