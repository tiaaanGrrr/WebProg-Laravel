<header class="site-header">
    <div class="announcement">
        GRATIS ONGKIR SE-JABODETABEK UNTUK PEMBELIAN MINIMAL RP 500.000
    </div>

    <nav class="main-nav site-container" aria-label="Navigasi utama">
        <a class="brand" href="{{ route('home') }}" aria-label="Cahaya Sembahyang — Beranda">
            <span class="brand__mark">@include('partials.icon', ['name' => 'logo-nav'])</span>
            <span>
                <span class="brand__name">Cahaya Sembahyang</span>
                <span class="brand__tagline">Premium Spiritual Supplies</span>
            </span>
        </a>

        <div class="nav-links" id="main-menu">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Beranda</a>
            <a href="{{ route('products.index', ['category' => 'hio']) }}">Hio &amp; Incense</a>
            <a href="{{ route('products.index', ['category' => 'lilin']) }}">Lilin</a>
            <a href="{{ route('products.index', ['category' => 'kertas']) }}">Kertas Sembahyang</a>
            <a href="{{ route('products.index', ['category' => 'aksesoris']) }}">Aksesoris</a>
            <a href="{{ route('home') }}#tentang-kami">Tentang Kami</a>
        </div>

        <div class="nav-actions">
            <form class="search-form" action="{{ route('products.index') }}" method="get" role="search" data-search-form>
                <label class="sr-only" for="nav-search">Cari perlengkapan</label>
                <button type="submit" aria-label="Cari produk">@include('partials.icon', ['name' => 'search'])</button>
                <input type="search" id="nav-search" name="q" placeholder="Cari perlengkapan..." maxlength="100" autocomplete="off">
            </form>
            <span class="nav-divider" aria-hidden="true"></span>
            <button class="cart-toggle" type="button" data-open-cart aria-label="Buka keranjang">
                @include('partials.icon', ['name' => 'bag'])
                <span class="cart-count" data-cart-count>0</span>
            </button>
        </div>

        <button class="menu-toggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="main-menu" data-menu-toggle>
            <span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
        </button>
    </nav>
</header>
