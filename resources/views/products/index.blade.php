@extends('layouts.app')

@section('title', 'Katalog Produk | Cahaya Sembahyang')

@section('content')
<section class="site-container catalog-page" aria-labelledby="catalog-title" data-catalog>
    <div class="section-heading">
        <p class="eyebrow">Koleksi Perlengkapan</p>
        <h1 id="catalog-title">Katalog Sembahyang</h1>
        <p>Pilihan dupa, lilin, kertas sembahyang, dan sarana puja untuk kebutuhan Anda.</p>
    </div>
    @include('admin.messages')
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
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
    <div class="empty-state" data-catalog-empty @if($products->isNotEmpty()) hidden @endif>
        <h2>Produk belum tersedia</h2>
        <p data-empty-message>Koleksi sedang diperbarui. Silakan kunjungi kembali nanti.</p>
        <button class="site-button site-button--outline" type="button" data-reset-filters>RESET PENCARIAN</button>
    </div>
</section>
@endsection
