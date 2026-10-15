@extends('layouts.app')
@section('title', $product->name.' | Cahaya Sembahyang')
@section('content')
<section class="site-container catalog-page product-detail-page">
    <nav class="product-breadcrumb" aria-label="Jejak navigasi"><a href="{{ route('home') }}">Beranda</a><span aria-hidden="true">/</span><a href="{{ route('products.index') }}">Koleksi</a><span aria-hidden="true">/</span><span aria-current="page">{{ $product->name }}</span></nav>
    <article class="product-detail" data-product-card data-product-id="database-{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-category="{{ $product->category }}" data-product-price="{{ $product->price }}" data-product-stock="{{ $product->stock }}" data-product-image="{{ $product->image_url }}">
        <div class="product-detail__visual"><img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="1024" height="1024" fetchpriority="high" data-product-image-fallback></div>
        <div class="product-detail__body">
            <a class="eyebrow" href="{{ route('products.index', ['category' => $product->category]) }}">{{ $product->category }}</a>
            <h1>{{ $product->name }}</h1>
            <p class="price"><strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong></p>
            <p class="stock-note">{{ $product->stock > 0 ? 'Stok tersedia: '.$product->stock : 'Stok habis' }}</p>
            <div class="product-detail__description"><h2>Deskripsi Produk</h2><p>{{ $product->description }}</p></div>
            @if($product->stock > 0)
                <label class="product-quantity" for="product-quantity">Jumlah
                    <input id="product-quantity" data-product-quantity type="number" value="1" min="1" max="{{ min(99, $product->stock) }}" step="1" inputmode="numeric" required aria-describedby="quantity-hint">
                </label>
                <p class="stock-note" id="quantity-hint">Maksimal {{ min(99, $product->stock) }} unit per produk dalam keranjang.</p>
                <div class="product-detail__actions"><button class="site-button" type="button" data-add-to-cart>TAMBAH KE KERANJANG</button><button class="site-button site-button--outline" type="button" data-add-to-cart data-buy-now>BELI SEKARANG</button></div>
            @else
                <button class="site-button" type="button" disabled>STOK HABIS</button>
            @endif
            <a class="product-back-link" href="{{ route('products.index') }}">Kembali ke koleksi</a>
        </div>
    </article>
    @if($relatedProducts->isNotEmpty())
        <section class="product-related" aria-labelledby="related-title">
            <div class="section-heading"><p class="eyebrow">Lengkapi Perlengkapan Anda</p><h2 id="related-title">Produk Sejenis</h2></div>
            <div class="product-grid">@foreach($relatedProducts as $relatedProduct) @include('partials.product-card', ['product' => $relatedProduct]) @endforeach</div>
        </section>
    @endif
</section>
@endsection
