@extends('layouts.app')
@section('title', 'Detail Pesanan '.$order->reference.' | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page order-print">
    <div class="section-heading"><p class="eyebrow">Pesanan Saya</p><h1>{{ $order->reference }}</h1><p>Ringkasan pesanan demo Cahaya Sembahyang, tanpa pembayaran atau pengiriman.</p></div>
    @include('partials.order-detail', ['order' => $order])
    <div class="admin-actions"><button class="site-button" type="button" data-print-order>CETAK RINGKASAN</button><a class="site-button site-button--outline" href="{{ route('orders.index') }}">KEMBALI KE PESANAN SAYA</a></div>
</section>
@endsection
