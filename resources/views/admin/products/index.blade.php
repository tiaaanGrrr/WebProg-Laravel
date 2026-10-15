@extends('layouts.app')
@section('title', 'Kelola Produk | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page">
    <div class="section-heading"><p class="eyebrow">Pengelolaan Toko</p><h1>Kelola Produk</h1><p>Atur koleksi, harga, dan stok produk toko.</p></div>
    @include('admin.navigation')
    <div class="admin-actions"><a class="site-button" href="{{ route('admin.products.create') }}">TAMBAH PRODUK</a></div>
    @include('admin.messages')
    @include('admin.products.filters')
    <p class="stock-note">Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk · Produk dengan stok 0 tidak tampil di toko.</p>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <caption class="sr-only">Daftar produk toko</caption>
            <thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td><div class="admin-product"><img src="{{ $product->image_url }}" alt="" width="64" height="64"><strong>{{ $product->name }}</strong></div></td>
                    <td>{{ $product->category }}</td><td>Rp {{ number_format($product->price, 0, ',', '.') }}</td><td>{{ $product->stock }}</td>
                    <td><div class="admin-actions">
                        <a class="site-button site-button--outline" href="{{ route('admin.products.edit', $product) }}">EDIT</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-confirm-delete>
                            @csrf @method('DELETE')<button class="site-button" type="submit">HAPUS</button>
                        </form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5">Tidak ada produk pada daftar ini. Coba kata kunci atau filter lain, atau tambahkan produk baru.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $products->onEachSide(1)->links('admin.pagination') }}
</section>
@endsection
