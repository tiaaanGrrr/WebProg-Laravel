<section class="newsletter" aria-labelledby="newsletter-title">
    <div class="site-container">
        <div class="newsletter__panel">
            <div>
                <h2 id="newsletter-title">Ikuti Kabar &amp; Kalender Lunar</h2>
                <p>Dapatkan panduan tata cara doa hari raya Imlek, Ceng Beng, Thian Gong, serta info penawaran eksklusif dupa &amp; lilin premium langsung di email Anda.</p>
            </div>
            <form data-newsletter>
                <div class="newsletter__fields">
                    <label class="sr-only" for="newsletter-email">Alamat email</label>
                    <input type="email" id="newsletter-email" name="email" placeholder="Masukkan alamat email Anda..." maxlength="254" autocomplete="email" required>
                    <button class="site-button site-button--gold" type="button" data-newsletter-button>BERLANGGANAN</button>
                </div>
                <p class="newsletter__status" role="status" data-newsletter-status hidden></p>
            </form>
        </div>
    </div>
</section>

<footer class="site-footer" id="kontak">
    <div class="site-container site-footer__grid">
        <div>
            <a class="footer-brand" href="{{ route('home') }}">
                <span class="footer-brand__mark">@include('partials.icon', ['name' => 'logo-footer'])</span>
                <span class="footer-brand__name">Cahaya Sembahyang</span>
            </a>
            <p>Warisan tradisi ritual Tionghoa murni sejak 1994. Menyediakan produk sembahyang berkualitas tinggi dan aman bagi pernapasan keluarga Anda.</p>
            <div class="footer-social" aria-label="Media sosial">
                <button type="button" aria-label="Instagram" data-demo-message="Alamat Instagram toko belum dikonfigurasi.">@include('partials.icon', ['name' => 'instagram'])</button>
                <button type="button" aria-label="Facebook" data-demo-message="Alamat Facebook toko belum dikonfigurasi.">@include('partials.icon', ['name' => 'facebook'])</button>
                <button type="button" aria-label="Chat admin" data-demo-message="Kontak admin masih memakai data contoh dari desain.">@include('partials.icon', ['name' => 'chat'])</button>
            </div>
        </div>
        <div>
            <h3>Belanja Online</h3>
            <ul class="footer-links">
                <li><a href="{{ route('products.index', ['category' => 'hio']) }}">Dupa Hio Cendana</a></li>
                <li><a href="{{ route('products.index', ['category' => 'lilin']) }}">Lilin Sembahyang</a></li>
                <li><a href="{{ route('products.index', ['category' => 'kertas']) }}">Kertas Sembahyang</a></li>
                <li><a href="{{ route('products.index', ['category' => 'aksesoris']) }}">Aksesoris Rupang</a></li>
                <li><a href="{{ route('products.index', ['category' => 'minyak']) }}">Minyak Sembahyang</a></li>
            </ul>
        </div>
        <div>
            <h3>Informasi &amp; Layanan</h3>
            <ul class="footer-links">
                <li><a href="{{ route('home') }}#tentang-kami">Tentang Kami</a></li>
                <li><button class="footer-text-button" type="button" data-demo-message="Halaman syarat dan ketentuan belum dibuat.">Syarat &amp; Ketentuan</button></li>
                <li><button class="footer-text-button" type="button" data-demo-message="Halaman kebijakan privasi belum dibuat.">Kebijakan Privasi</button></li>
                <li><a href="#kontak">FAQ Hubungi Kami</a></li>
                <li><button class="footer-text-button" type="button" data-demo-message="Panduan tata cara sembahyang belum dibuat.">Tata Cara Sembahyang</button></li>
            </ul>
        </div>
        <div>
            <h3>Butuh Bantuan?</h3>
            <ul class="footer-contact">
                <li>@include('partials.icon', ['name' => 'map'])<span>Jl. Pancoran No. 42, Glodok, Taman Sari, Jakarta Barat 11120</span></li>
                <li><span class="footer-contact__label" aria-hidden="true">Tel.</span><span>(021) 692-8472 / 0812-3456-7890</span></li>
                <li><span class="footer-contact__label" aria-hidden="true">@</span><span>halo@cahayasembahyang.com</span></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="site-container footer-bottom__inner">
            <p>&copy; {{ date('Y') }} PT Cahaya Sembahyang Indonesia. Hak Cipta Dilindungi.</p>
            <div class="payment-methods" aria-label="Metode pembayaran pada desain">
                <span>BCA</span><span>Mandiri</span><span>GoPay</span><span>OVO</span><span>Visa</span>
            </div>
        </div>
    </div>
</footer>
