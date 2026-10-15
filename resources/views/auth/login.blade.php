@extends('layouts.app')
@section('title', 'Masuk Pembeli | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page">
    <div class="section-heading"><p class="eyebrow">Akun Pembeli</p><h1>Masuk</h1><p>Masuk untuk checkout dan melihat riwayat pesanan Anda.</p></div>
    <form class="admin-form admin-form--login" action="{{ route('customer.login.store') }}" method="POST">
        @csrf @include('admin.messages')
        <label>Email<input name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="username" autofocus></label>
        <label>Password<input name="password" type="password" required autocomplete="current-password"></label>
        <button class="site-button" type="submit">MASUK</button>
        <p>Belum punya akun? <a class="account-link" href="{{ route('customer.register') }}">Daftar sekarang</a></p>
        <a href="{{ route('products.index') }}">Kembali ke koleksi</a>
    </form>
</section>
@endsection
