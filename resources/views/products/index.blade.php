@extends('layouts.app')

@section('title', 'Katalog Produk | Cahaya Sembahyang')

@section('content')
<section class="site-container catalog-page" aria-labelledby="catalog-title" data-catalog>
    <div class="section-heading">
        <p class="eyebrow">Koleksi Perlengkapan</p>
        <h1 id="catalog-title">Katalog Sembahyang</h1>
        <p>Pilihan dupa, lilin, kertas sembahyang, dan sarana puja untuk kebutuhan Anda.</p>
    </div>
    <div class="catalog-controls">
        <label class="catalog-filter" for="category-filter">Kategori
            <select id="category-filter" data-category-filter>
                <option value="">Semua kategori</option>
                @foreach($products->pluck('category')->unique()->sort() as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
        </label>
        <p class="catalog-summary" data-catalog-summary aria-live="polite">{{ $products->count() }} produk tersedia</p>
    </div>
    <div class="product-grid" data-catalog-grid>
        @foreach($products as $product)
            <article class="product-card" data-product-card data-product-id="database-{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-category="{{ $product->category }}" data-product-price="{{ $product->price }}" data-product-stock="{{ $product->stock }}" data-product-image="{{ $product->image }}">
                <div class="product-card__visual">
                    <img class="product-card__image" src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy" data-product-image-fallback>
                    <span class="product-card__badge">{{ $product->category }}</span>
                </div>
                <div class="product-card__body">
                    <h3>{{ $product->name }}</h3>
                    <p class="product-card__description">{{ $product->description }}</p>
                    <p class="price"><strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong></p>
                    <p class="stock-note">Stok: {{ $product->stock }}</p>
                </div>
                <button class="site-button site-button--full" type="button" data-add-to-cart @disabled($product->stock <= 0)>TAMBAH KE KERANJANG</button>
            </article>
        @endforeach
    </div>
    <div class="empty-state" data-catalog-empty @if($products->isNotEmpty()) hidden @endif>
        <h2>Produk belum tersedia</h2>
        <p data-empty-message>Koleksi sedang diperbarui. Silakan kunjungi kembali nanti.</p>
        <button class="site-button site-button--outline" type="button" data-reset-filters>RESET PENCARIAN</button>
    </div>
</section>
@endsection
