@extends('layouts.app')
@section('title', 'Pesanan Tersimpan | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page order-print" data-order-reference="{{ $order->reference }}" data-purchased-items="{{ json_encode(session('purchased_items', [])) }}">
    <div class="section-heading"><p class="eyebrow">Terima Kasih</p><h1>Pesanan Berhasil Disimpan</h1><p>Nomor pesanan: <strong>{{ $order->reference }}</strong></p><p>Ini pesanan demo untuk tugas kuliah, tanpa pembayaran atau pengiriman.</p></div>
    @include('partials.order-detail', ['order' => $order])
    <div class="admin-actions"><button class="site-button" type="button" data-print-order>CETAK RINGKASAN</button><a class="site-button site-button--outline" href="{{ route('orders.index') }}">PESANAN SAYA</a><a class="site-button site-button--outline" href="{{ route('products.index') }}">KEMBALI KE KOLEKSI</a></div>
</section>
@endsection
