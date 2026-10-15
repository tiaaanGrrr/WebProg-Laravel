<form class="admin-filters" action="{{ route('admin.orders.index') }}" method="GET" role="search" aria-label="Cari pesanan admin">
    <label>Cari pesanan<input type="search" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Nomor pesanan, nama, atau nomor HP"></label>
    <label>Status<select name="status"><option value="">Semua status</option>
        @foreach(\App\Models\Order::STATUS_LABELS as $value => $label)<option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>@endforeach
    </select></label>
    <button class="site-button" type="submit">CARI</button><a class="site-button site-button--outline" href="{{ route('admin.orders.index') }}">RESET</a>
</form>
