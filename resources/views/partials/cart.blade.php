<dialog class="cart-dialog" id="cart-dialog" aria-labelledby="cart-title" aria-describedby="cart-caption">
    <header class="cart-dialog__header">
        <div>
            <h2 id="cart-title">Keranjang Anda</h2>
            <p class="cart-dialog__caption" id="cart-caption">Pilihan produk Anda tersimpan di browser ini.</p>
        </div>
        <form method="dialog">
            <button class="cart-close" type="submit" aria-label="Tutup keranjang">&times;</button>
        </form>
    </header>
    <p class="cart-empty" id="cart-empty">Keranjang masih kosong. Pilih perlengkapan yang Anda butuhkan.</p>
    <div class="cart-items" id="cart-items"></div>
    <footer class="cart-dialog__footer">
        <p class="cart-subtotal"><span>Subtotal</span><strong id="cart-subtotal">Rp 0</strong></p>
        <form action="{{ route('checkout.preview') }}" method="POST" data-checkout-cart>
            @csrf
            <div data-checkout-inputs></div>
            <button class="site-button site-button--full" type="submit" data-checkout-button disabled>LANJUT KE CHECKOUT</button>
        </form>
        <p class="cart-note">Pesanan demo untuk tugas kuliah, tanpa pembayaran.</p>
        <a class="cart-history-link" href="{{ route('orders.index') }}">Lihat Pesanan Saya</a>
        <noscript>Aktifkan JavaScript untuk menggunakan keranjang dan checkout.</noscript>
    </footer>
</dialog>
