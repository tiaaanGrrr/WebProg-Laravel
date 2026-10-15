@extends('layouts.app')
@section('title', 'Checkout | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page">
    <div class="section-heading"><p class="eyebrow">Lengkapi Pesanan</p><h1>Checkout</h1><p>Isi data penerima dan periksa kembali produk pilihan Anda.</p></div>
    @include('admin.messages')
    <div class="checkout-grid">
        <form class="admin-form checkout-form" action="{{ route('checkout.store') }}" method="POST" data-order-form>
            @csrf
            <input type="hidden" name="checkout_token" value="{{ $token }}">
            <h2>Data Penerima</h2>
            <label>Nama penerima<input name="customer_name" value="{{ old('customer_name') }}" autocomplete="name" maxlength="255" required></label>
            <label>Nomor HP<input name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" minlength="8" maxlength="25" required></label>
            <label>Alamat lengkap<textarea name="address" rows="4" autocomplete="street-address" minlength="10" maxlength="2000" required>{{ old('address') }}</textarea></label>
            <label>Catatan (opsional)<textarea name="notes" rows="2" maxlength="1000">{{ old('notes') }}</textarea></label>
            <p class="stock-note">Simulasi tugas kuliah. Pesanan disimpan tanpa pembayaran dan pengiriman.</p>
            <button class="site-button site-button--full" type="submit">KONFIRMASI PESANAN DEMO</button>
            <a href="{{ route('products.index') }}">Kembali ke katalog untuk mengubah keranjang</a>
        </form>
        <aside class="order-summary" aria-labelledby="summary-title">
            <h2 id="summary-title">Ringkasan Pesanan</h2>
            @foreach($lines as $line)
                <article class="order-line">
                    <img src="{{ $line['product']->image_url }}" alt="" width="64" height="64">
                    <div><h3>{{ $line['product']->name }}</h3><p>{{ $line['quantity'] }} × Rp {{ number_format($line['product']->price, 0, ',', '.') }}</p><strong>Rp {{ number_format($line['product']->price * $line['quantity'], 0, ',', '.') }}</strong></div>
                </article>
            @endforeach
            <p class="cart-subtotal"><span>Total produk</span><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></p>
            <p class="stock-note">Harga terbaru sesuai katalog toko.</p>
        </aside>
    </div>
</section>
@endsection
