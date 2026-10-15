<article class="product-card" data-product-card data-product-id="database-{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-category="{{ $product->category }}" data-product-price="{{ $product->price }}" data-product-stock="{{ $product->stock }}" data-product-image="{{ $product->image_url }}">
    <a class="product-card__visual" href="{{ route('products.show', $product) }}" aria-label="Lihat detail {{ $product->name }}">
        <img class="product-card__image" src="{{ $product->image_url }}" alt="{{ $product->name }}" width="1024" height="1024" loading="lazy" data-product-image-fallback>
        <span class="product-card__badge">{{ $product->category }}</span>
    </a>
    <div class="product-card__body">
        <h3><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
        <p class="product-card__description">{{ $product->description }}</p>
        <p class="price"><strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong></p>
        <p class="stock-note">Stok: {{ $product->stock }}</p>
    </div>
    <button class="site-button site-button--full" type="button" data-add-to-cart @disabled($product->stock <= 0)>TAMBAH KE KERANJANG</button>
</article>
