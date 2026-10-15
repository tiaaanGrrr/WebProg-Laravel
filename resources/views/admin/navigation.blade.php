<nav class="admin-toolbar" aria-label="Navigasi admin">
    <a class="site-button {{ request()->routeIs('admin.dashboard') ? '' : 'site-button--outline' }}" href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>DASHBOARD</a>
    <a class="site-button {{ request()->routeIs('admin.products.*') ? '' : 'site-button--outline' }}" href="{{ route('admin.products.index') }}" @if(request()->routeIs('admin.products.*')) aria-current="page" @endif>KELOLA PRODUK</a>
    <a class="site-button {{ request()->routeIs('admin.orders.*') ? '' : 'site-button--outline' }}" href="{{ route('admin.orders.index') }}" @if(request()->routeIs('admin.orders.*')) aria-current="page" @endif>PESANAN MASUK</a>
    <a class="site-button site-button--outline" href="{{ route('products.index') }}">LIHAT KATALOG</a>
    <a class="site-button {{ request()->routeIs('admin.reports.*') ? '' : 'site-button--outline' }}" href="{{ route('admin.reports.orders') }}" @if(request()->routeIs('admin.reports.*')) aria-current="page" @endif>LAPORAN</a>
    <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="site-button site-button--outline" type="submit">KELUAR</button></form>
</nav>
