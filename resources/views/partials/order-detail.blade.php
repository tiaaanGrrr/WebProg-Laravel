<div class="order-summary">
    <p class="print-only">Cahaya Sembahyang - Ringkasan pesanan demo, tanpa pembayaran atau pengiriman.</p>
    <h2>Rincian Pesanan</h2>
    <dl class="order-recipient">
        <dt>Nama penerima</dt><dd>{{ $order->customer_name }}</dd>
        <dt>Nomor HP</dt><dd>{{ $order->phone }}</dd>
        <dt>Alamat</dt><dd class="order-text">{{ $order->address }}</dd>
        @if($order->notes)<dt>Catatan</dt><dd class="order-text">{{ $order->notes }}</dd>@endif
        <dt>Tanggal</dt><dd>{{ $order->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</dd>
        <dt>Status</dt><dd><span class="order-status order-status--{{ $order->status }}">{{ \App\Models\Order::STATUS_LABELS[$order->status] }}</span></dd>
    </dl>
    @foreach($order->items as $item)
        <div class="order-line"><div><h3>{{ $item->product_name }}</h3><p>{{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p></div><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></div>
    @endforeach
    <p class="cart-subtotal"><span>Total produk</span><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></p>
</div>
