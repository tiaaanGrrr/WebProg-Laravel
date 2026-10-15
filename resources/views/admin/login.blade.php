@extends('layouts.app')
@section('title', 'Login Admin | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page">
    <div class="section-heading"><p class="eyebrow">Pengelolaan Toko</p><h1>Login Admin</h1><p>Masuk untuk mengelola koleksi Cahaya Sembahyang.</p></div>
    <form class="admin-form admin-form--login" action="{{ route('admin.login') }}" method="POST">
        @csrf
        @include('admin.messages')
        <label>Email<input name="email" type="email" value="{{ old('email') }}" required autocomplete="username" autofocus></label>
        <label>Password<input name="password" type="password" required autocomplete="current-password"></label>
        <button class="site-button" type="submit">MASUK</button>
        <a href="{{ route('home') }}">Kembali ke beranda</a>
    </form>
</section>
@endsection
