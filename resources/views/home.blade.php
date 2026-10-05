@extends('layouts.app')

@section('title', 'Cahaya Sembahyang | Warisan Tradisi Leluhur')

@section('content')
{{-- Produk, ulasan, paket, dan informasi toko di beranda masih berupa contoh dari Figma. --}}
<section class="hero" aria-labelledby="hero-title">
    <div class="hero__content">
        <div class="hero__copy">
            <p class="hero__eyebrow">@include('partials.icon', ['name' => 'flower']) Warisan Tradisi Leluhur Murni</p>
            <h1 id="hero-title"><span>Mengantar Doa</span><span>Dengan Cahaya dan</span><span>Keharuman Sempurna</span></h1>
            <p class="hero__description">Menyediakan perlengkapan sembahyang premium berkualitas tinggi, dari dupa Hio alami hingga lilin bebas jelaga. Diciptakan dengan rasa hormat mendalam untuk kekhusyukan ibadah Anda.</p>
        </div>
        <div class="hero__buttons">
            <a class="site-button" href="{{ route('products.index') }}">LIHAT KOLEKSI PREMIUM</a>
            <a class="site-button site-button--outline" href="#kontak">HUBUNGI ADMIN</a>
        </div>
    </div>
    <div class="hero__visual">
        <img src="{{ asset('images/cahaya/hero.jpg') }}" alt="Dupa menyala dengan keharuman yang lembut" width="1200" height="896" fetchpriority="high">
    </div>
</section>

<section class="site-section" id="kategori" aria-labelledby="categories-title">
    <div class="site-container">
        <div class="section-heading">
            <p class="eyebrow">Eksplorasi Perlengkapan</p>
            <h2 id="categories-title">Kategori Sembahyang Pilihan</h2>
            <p>Rangkaian produk terpilih yang diproduksi secara higienis, bersih, dan sesuai tata cara ritual tradisional.</p>
        </div>
        <div class="category-grid">
            <article class="category-card">
                <img class="category-card__image" src="{{ asset('images/cahaya/category-hio.jpg') }}" alt="Dupa hio cendana di atas wadah kayu" width="1264" height="848" loading="lazy">
                <div class="category-card__body">
                    <h3>Hio &amp; Incense</h3>
                    <p>Aroma cendana &amp; gaharu alami yang menenangkan.</p>
                    <a class="category-card__link" href="{{ route('products.index', ['category' => 'hio']) }}">SELENGKAPNYA @include('partials.icon', ['name' => 'category-link'])</a>
                </div>
            </article>
            <article class="category-card">
                <img class="category-card__image" src="{{ asset('images/cahaya/category-lilin.jpg') }}" alt="Sepasang lilin merah di altar" width="1264" height="848" loading="lazy">
                <div class="category-card__body">
                    <h3>Lilin Sembahyang</h3>
                    <p>Hand-poured, bebas asap hitam &amp; menyala terang.</p>
                    <a class="category-card__link" href="{{ route('products.index', ['category' => 'lilin']) }}">SELENGKAPNYA @include('partials.icon', ['name' => 'category-link'])</a>
                </div>
            </article>
            <article class="category-card">
                <img class="category-card__image" src="{{ asset('images/cahaya/category-kertas.jpg') }}" alt="Pilihan kertas sembahyang emas dan perak" width="1264" height="848" loading="lazy">
                <div class="category-card__body">
                    <h3>Kertas Sembahyang</h3>
                    <p>Kertas emas, perak, dan Kim Cua berkualitas tinggi.</p>
                    <a class="category-card__link" href="{{ route('products.index', ['category' => 'kertas']) }}">SELENGKAPNYA @include('partials.icon', ['name' => 'category-link'])</a>
                </div>
            </article>
            <article class="category-card">
                <img class="category-card__image" src="{{ asset('images/cahaya/category-aksesoris.jpg') }}" alt="Tempat dupa kuningan di altar" width="1264" height="848" loading="lazy">
                <div class="category-card__body">
                    <h3>Aksesoris Rupang</h3>
                    <p>Rupang, altar, tempat dupa, dan perlengkapan hias.</p>
                    <a class="category-card__link" href="{{ route('products.index', ['category' => 'aksesoris']) }}">SELENGKAPNYA @include('partials.icon', ['name' => 'category-link'])</a>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="site-section site-section--bordered" id="produk-terlaris" aria-labelledby="bestsellers-title">
    <div class="site-container">
        <div class="section-heading">
            <p class="eyebrow">Rekomendasi Utama</p>
            <h2 id="bestsellers-title">Produk Terlaris Terfavorit</h2>
            <p>Produk yang paling banyak digunakan oleh umat untuk peribadatan harian maupun hari raya.</p>
        </div>
        <div class="product-grid">
            <article class="product-card" data-product-card data-product-id="demo-hio" data-product-name="Hio Cendana Australia Premium" data-product-price="125000" data-product-image="{{ asset('images/cahaya/product-hio.jpg') }}" data-product-category="Hio &amp; Incense">
                <div class="product-card__visual">
                    <img class="product-card__image" src="{{ asset('images/cahaya/product-hio.jpg') }}" alt="Hio Cendana Australia Premium" width="1024" height="1024" loading="lazy">
                    <span class="product-card__badge">Terlaris</span>
                </div>
                <div class="product-card__body">
                    <h3>Hio Cendana Australia Premium</h3>
                    <div class="rating"><span class="rating__stars" aria-label="5 dari 5 bintang">@include('partials.stars')</span><span class="rating__count">(148 ulasan)</span></div>
                    <p class="price"><strong>Rp 125.000</strong><del>Rp 150.000</del></p>
                </div>
                <button class="site-button site-button--full" type="button" data-add-to-cart>TAMBAH KE KERANJANG</button>
            </article>
            <article class="product-card" data-product-card data-product-id="demo-lilin" data-product-name="Lilin Merah Naga Ukir 24 Jam" data-product-price="185000" data-product-image="{{ asset('images/cahaya/product-lilin.jpg') }}" data-product-category="Lilin Sembahyang">
                <div class="product-card__visual">
                    <img class="product-card__image" src="{{ asset('images/cahaya/product-lilin.jpg') }}" alt="Lilin Merah Naga Ukir 24 Jam" width="1024" height="1024" loading="lazy">
                    <span class="product-card__badge">Premium</span>
                </div>
                <div class="product-card__body">
                    <h3>Lilin Merah Naga Ukir 24 Jam</h3>
                    <div class="rating"><span class="rating__stars" aria-label="5 dari 5 bintang">@include('partials.stars')</span><span class="rating__count">(92 ulasan)</span></div>
                    <p class="price"><strong>Rp 185.000</strong></p>
                </div>
                <button class="site-button site-button--full" type="button" data-add-to-cart>TAMBAH KE KERANJANG</button>
            </article>
            <article class="product-card" data-product-card data-product-id="demo-kertas" data-product-name="Kertas Emas Kim Cua Tebal (Isi 100)" data-product-price="65000" data-product-image="{{ asset('images/cahaya/product-kertas.jpg') }}" data-product-category="Kertas Sembahyang">
                <div class="product-card__visual">
                    <img class="product-card__image" src="{{ asset('images/cahaya/product-kertas.jpg') }}" alt="Kertas Emas Kim Cua Tebal (Isi 100)" width="1024" height="1024" loading="lazy">
                    <span class="product-card__badge">Hemat</span>
                </div>
                <div class="product-card__body">
                    <h3>Kertas Emas Kim Cua Tebal (Isi 100)</h3>
                    <div class="rating"><span class="rating__stars" aria-label="4 dari 5 bintang">@include('partials.stars', ['lastMuted' => true])</span><span class="rating__count">(210 ulasan)</span></div>
                    <p class="price"><strong>Rp 65.000</strong><del>Rp 75.000</del></p>
                </div>
                <button class="site-button site-button--full" type="button" data-add-to-cart>TAMBAH KE KERANJANG</button>
            </article>
            <article class="product-card" data-product-card data-product-id="demo-minyak" data-product-name="Minyak Lilin Alami Ramah Lingkungan" data-product-price="95000" data-product-image="{{ asset('images/cahaya/product-minyak.jpg') }}" data-product-category="Minyak Sembahyang">
                <div class="product-card__visual">
                    <img class="product-card__image" src="{{ asset('images/cahaya/product-minyak.jpg') }}" alt="Minyak Lilin Alami Ramah Lingkungan" width="1024" height="1024" loading="lazy">
                    <span class="product-card__badge">Baru</span>
                </div>
                <div class="product-card__body">
                    <h3>Minyak Lilin Alami Ramah Lingkungan</h3>
                    <div class="rating"><span class="rating__stars" aria-label="5 dari 5 bintang">@include('partials.stars')</span><span class="rating__count">(56 ulasan)</span></div>
                    <p class="price"><strong>Rp 95.000</strong></p>
                </div>
                <button class="site-button site-button--full" type="button" data-add-to-cart>TAMBAH KE KERANJANG</button>
            </article>
        </div>
    </div>
</section>

<section class="site-container heritage" id="tentang-kami" aria-labelledby="heritage-title">
    <div>
        <p class="eyebrow">Toko Warisan Sejak 1994</p>
        <h2 id="heritage-title">Menjaga Kemurnian Ritual Dengan Ketulusan</h2>
        <p class="heritage__intro">Cahaya Sembahyang berawal dari sebuah toko kecil di daerah Glodok Pancoran, melayani kebutuhan spiritual keluarga Tionghoa generasi demi generasi. Kami mengutamakan keaslian bahan demi mendukung niat mulia Anda dalam berdoa.</p>
        <ol class="heritage__features">
            <li class="heritage__feature"><span class="heritage__number">1</span><div><h3>Bahan Alami Pilihan</h3><p>Hio kami bebas bahan kimia berbahaya, aman untuk pernapasan seluruh keluarga dan anak-anak.</p></div></li>
            <li class="heritage__feature"><span class="heritage__number">2</span><div><h3>Standar Keagamaan Tradisional</h3><p>Seluruh lilin, dupa, dan kertas diproduksi sesuai standar tradisi Tionghoa untuk menjamin keabsahan doa.</p></div></li>
            <li class="heritage__feature"><span class="heritage__number">3</span><div><h3>Layanan Konsultasi Ritual</h3><p>Membantu mempersiapkan perlengkapan perayaan hari raya besar (Imlek, Ceng Beng, Thian Gong) secara akurat.</p></div></li>
        </ol>
    </div>
    <img class="heritage__image" src="{{ asset('images/cahaya/heritage.jpg') }}" alt="Suasana toko dan altar tradisional" width="1152" height="928" loading="lazy">
</section>

<section class="site-section site-section--bordered" id="paket" aria-labelledby="bundles-title">
    <div class="site-container">
        <div class="section-heading">
            <p class="eyebrow">Penawaran Paket</p>
            <h2 id="bundles-title">Paket Peribadatan Hemat &amp; Lengkap</h2>
            <p>Rekomendasi paket praktis yang disusun secara sistematis untuk berbagai keperluan ibadah harian maupun bulanan.</p>
        </div>
        <div class="bundle-grid">
            <article class="bundle-card" data-product-card data-product-id="demo-bundle-harian" data-product-name="Paket Sembahyang Harian Lengkap" data-product-price="245000" data-product-image="{{ asset('images/cahaya/bundle-harian.jpg') }}">
                <img class="bundle-card__image" src="{{ asset('images/cahaya/bundle-harian.jpg') }}" alt="Paket lengkap perlengkapan sembahyang harian" width="848" height="1264" loading="lazy">
                <div class="bundle-card__body">
                    <div><h3>Paket Sembahyang Harian Lengkap</h3><p>Satu paket lengkap berisi 1 dus Hio Cendana Wangi, 1 pasang Lilin Merah 8 Jam, 1 pack Kertas Sembahyang, dan 1 botol minyak doa harian.</p></div>
                    <ul class="bundle-card__list">
                        <li>@include('partials.icon', ['name' => 'check']) 1x Dupa Hio Cendana Australia</li>
                        <li>@include('partials.icon', ['name' => 'check']) 2x Lilin Ulir Merah 8 Jam</li>
                        <li>@include('partials.icon', ['name' => 'check']) 1x Pack Joss Paper Premium</li>
                        <li>@include('partials.icon', ['name' => 'check']) Minyak Pelita Murni</li>
                    </ul>
                    <div class="bundle-card__bottom"><p class="price"><strong>Rp 245.000</strong><del>Rp 295.000</del></p><button class="site-button" type="button" data-add-to-cart data-buy-now>BELI SEKARANG</button></div>
                </div>
            </article>
            <article class="bundle-card" data-product-card data-product-id="demo-bundle-imlek" data-product-name="Paket Hari Raya Imlek Agung" data-product-price="580000" data-product-image="{{ asset('images/cahaya/bundle-imlek.jpg') }}">
                <img class="bundle-card__image" src="{{ asset('images/cahaya/bundle-imlek.jpg') }}" alt="Paket perlengkapan sembahyang Imlek" width="848" height="1264" loading="lazy">
                <div class="bundle-card__body">
                    <div><h3>Paket Hari Raya Imlek Agung</h3><p>Khusus dipersiapkan untuk hari raya besar Imlek. Lilin Naga Premium jumbo, Hio Cendana wangi awet 24 Jam, dan kertas doa persembahan khusus Thian Gong.</p></div>
                    <ul class="bundle-card__list">
                        <li>@include('partials.icon', ['name' => 'check']) 1x Hio Cendana Wangi 24 Jam</li>
                        <li>@include('partials.icon', ['name' => 'check']) 2x Lilin Ukir Naga Merah Jumbo</li>
                        <li>@include('partials.icon', ['name' => 'check']) 3x Pack Kertas Thian Gong Kim</li>
                        <li>@include('partials.icon', ['name' => 'check']) Mangkuk Dupa Kuningan Tebal</li>
                    </ul>
                    <div class="bundle-card__bottom"><p class="price"><strong>Rp 580.000</strong><del>Rp 690.000</del></p><button class="site-button" type="button" data-add-to-cart data-buy-now>BELI SEKARANG</button></div>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="guarantees" aria-label="Keunggulan toko">
    <div class="site-container guarantees__grid">
        <div class="guarantee"><span class="guarantee__icon">@include('partials.icon', ['name' => 'sparkles'])</span><h3>100% Cendana Murni</h3><p>Dupa kami terbuat dari kayu cendana asli tanpa parfum kimia sintetis.</p></div>
        <div class="guarantee"><span class="guarantee__icon">@include('partials.icon', ['name' => 'shield'])</span><h3>Bebas Jelaga Hitam</h3><p>Lilin sembahyang berkualitas tinggi, aman untuk dinding kuil &amp; rumah.</p></div>
        <div class="guarantee"><span class="guarantee__icon">@include('partials.icon', ['name' => 'feather'])</span><h3>Kemurnian Ritual</h3><p>Diproses secara suci dan bersahaja demi kesempurnaan doa Anda.</p></div>
        <div class="guarantee"><span class="guarantee__icon">@include('partials.icon', ['name' => 'truck'])</span><h3>Pengiriman Aman</h3><p>Garansi pecah belah diganti baru jika rusak dalam pengiriman.</p></div>
    </div>
</section>

<section class="site-section" id="testimoni" aria-labelledby="testimonials-title">
    <div class="site-container">
        <div class="section-heading"><p class="eyebrow">Ulasan Pelanggan</p><h2 id="testimonials-title">Kepercayaan &amp; Kekhusyukan</h2><p>Testimoni nyata dari keluarga dan perwakilan kuil kelenteng Southeast Asia yang setia menggunakan layanan kami.</p></div>
        <div class="testimonial-grid">
            <article class="testimonial">
                <div class="rating__stars" aria-label="5 dari 5 bintang">@include('partials.stars')</div>
                <blockquote>“Sejak beralih menggunakan dupa cendana dari Cahaya Sembahyang, udara di vihara menjadi sangat bersih dan harum alami. Jemaat kami tidak lagi terganggu asap pedih di mata.”</blockquote>
                <div class="testimonial__person"><img class="testimonial__avatar" src="{{ asset('images/cahaya/customer-hendra.jpg') }}" alt="Hendra Wijaya" width="1024" height="1024" loading="lazy"><div><h3>Hendra Wijaya</h3><p>Pembina Yayasan Vihara Jakarta</p></div></div>
            </article>
            <article class="testimonial">
                <div class="rating__stars" aria-label="5 dari 5 bintang">@include('partials.stars')</div>
                <blockquote>“Lilin merahnya menyala bersih dan stabil sekali. Sangat membantu kekhusyukan doa harian Thian Gong di altar keluarga kami. Toko ini sangat terpercaya dalam menjaga kualitas.”</blockquote>
                <div class="testimonial__person"><img class="testimonial__avatar" src="{{ asset('images/cahaya/customer-meiling.jpg') }}" alt="Meiling Tan" width="1024" height="1024" loading="lazy"><div><h3>Meiling Tan</h3><p>Ibu Rumah Tangga, Glodok</p></div></div>
            </article>
            <article class="testimonial">
                <div class="rating__stars" aria-label="5 dari 5 bintang">@include('partials.stars')</div>
                <blockquote>“Sangat puas dengan kelengkapan paket Ceng Bengnya. Susunannya sangat rapi dan lengkap sesuai aturan tradisi murni leluhur. Pengiriman ke luar kota aman dibungkus kayu tebal.”</blockquote>
                <div class="testimonial__person"><img class="testimonial__avatar" src="{{ asset('images/cahaya/customer-kevin.jpg') }}" alt="Kevin Santoso" width="1024" height="1024" loading="lazy"><div><h3>Kevin Santoso</h3><p>Praktisi Tradisi Tionghoa, Surabaya</p></div></div>
            </article>
        </div>
    </div>
</section>
@endsection
