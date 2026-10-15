@extends('layouts.app')
@section('title', ($product->exists ? 'Edit' : 'Tambah').' Produk | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page admin-page">
    <div class="section-heading"><p class="eyebrow">Pengelolaan Toko</p><h1>{{ $product->exists ? 'Edit' : 'Tambah' }} Produk</h1><p>Informasi yang disimpan akan tampil pada koleksi toko.</p></div>
    <form class="admin-form" method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if($product->exists) @method('PUT') @endif
        @include('admin.messages')
        <label>Nama produk<input name="name" value="{{ old('name', $product->name) }}" required maxlength="255"></label>
        <label>Kategori<input name="category" list="product-categories" value="{{ old('category', $product->category) }}" required maxlength="255"></label>
        <datalist id="product-categories">@foreach(['Hio & Incense', 'Lilin Sembahyang', 'Kertas Sembahyang', 'Aksesoris Rupang', 'Minyak Sembahyang', 'Paket'] as $category)<option value="{{ $category }}">@endforeach</datalist>
        <p class="stock-note">Gunakan kategori Paket untuk menampilkan produk di bagian paket beranda.</p>
        <label>Deskripsi<textarea name="description" rows="5" required maxlength="5000">{{ old('description', $product->description) }}</textarea></label>
        <div class="admin-form-grid">
            <label>Harga (Rp)<input name="price" type="number" min="1" max="2147483647" step="1" value="{{ old('price', $product->price) }}" required></label>
            <label>Stok<input name="stock" type="number" min="0" max="2147483647" step="1" value="{{ old('stock', $product->stock ?? 0) }}" required></label>
        </div>
        @if($product->exists)<img class="admin-preview" src="{{ $product->image_url }}" alt="{{ $product->name }}" width="160" height="160">@endif
        <label>Gambar produk<input type="file" name="image" accept="image/jpeg,image/png,image/webp" @required(!$product->exists)></label>
        <p class="stock-note">JPG, PNG, atau WebP, maksimal 2 MB. {{ $product->exists ? 'Kosongkan untuk mempertahankan gambar saat ini.' : '' }}</p>
        <div class="admin-actions"><button class="site-button" type="submit">SIMPAN PRODUK</button><a class="site-button site-button--outline" href="{{ route('admin.products.index') }}">BATAL</a></div>
    </form>
</section>
@endsection
