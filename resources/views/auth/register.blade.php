@extends('layouts.app')
@section('title', 'Daftar Pembeli | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page">
    <div class="section-heading"><p class="eyebrow">Akun Pembeli</p><h1>Daftar</h1><p>Buat akun untuk menyimpan riwayat belanja Anda.</p></div>
    <form class="admin-form admin-form--login" action="{{ route('customer.register.store') }}" method="POST">
        @csrf @include('admin.messages')
        <label>Nama lengkap<input name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" autofocus></label>
        <label>Email<input name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"></label>
        <label>Password<input name="password" type="password" required minlength="8" maxlength="72" autocomplete="new-password"></label>
        <label>Konfirmasi password<input name="password_confirmation" type="password" required minlength="8" maxlength="72" autocomplete="new-password"></label>
        <p class="stock-note">Gunakan password minimal 8 karakter.</p>
        <button class="site-button" type="submit">DAFTAR</button>
        <p>Sudah punya akun? <a class="account-link" href="{{ route('customer.login') }}">Masuk</a></p>
    </form>
</section>
@endsection
