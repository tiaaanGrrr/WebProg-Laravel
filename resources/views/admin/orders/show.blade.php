@extends('layouts.app')
@section('title', 'Detail Pesanan | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page order-print">
    <div class="section-heading"><p class="eyebrow">Pesanan Masuk</p><h1>{{ $order->reference }}</h1></div>
    @include('admin.messages')
    @include('partials.order-detail', ['order' => $order])
    @if($order->nextStatuses())
        <form class="admin-form order-status-form" action="{{ route('admin.orders.update', $order) }}" method="POST" data-order-status-form>
            @csrf @method('PATCH')
            <input type="hidden" name="current_status" value="{{ $order->status }}">
            <label>Status berikutnya
                <select name="status" required>
                    <option value="">Pilih status</option>
                    @foreach($order->nextStatuses() as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                </select>
            </label>
            <p class="stock-note">Pembatalan mengembalikan stok produk yang masih ada. Pesanan selesai atau dibatalkan tidak bisa dibuka kembali.</p>
            <button class="site-button" type="submit">SIMPAN STATUS</button>
        </form>
    @else
        <p class="admin-notice">Pesanan sudah {{ strtolower(\App\Models\Order::STATUS_LABELS[$order->status]) }}. Status tidak bisa diubah lagi.</p>
    @endif
    <div class="admin-actions"><button class="site-button" type="button" data-print-order>CETAK RINGKASAN</button><a class="site-button site-button--outline" href="{{ route('admin.orders.index') }}">KEMBALI KE PESANAN</a></div>
</section>
@endsection
