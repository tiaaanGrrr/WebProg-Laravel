<form class="admin-filters" action="{{ route('admin.products.index') }}" method="GET" role="search" aria-label="Cari produk admin">
    <label>Cari produk<input type="search" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Nama atau deskripsi produk"></label>
    <label>Kategori<select name="category"><option value="">Semua kategori</option>
        @if(($filters['category'] ?? '') !== '' && !$categories->contains($filters['category']))<option value="{{ $filters['category'] }}" selected>{{ $filters['category'] }}</option>@endif
        @foreach($categories as $category)<option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>@endforeach
    </select></label>
    <label>Kondisi stok<select name="stock"><option value="">Semua stok</option>
        @foreach(['tersedia' => 'Tersedia (stok > 0)', 'menipis' => 'Menipis (stok 1–5)', 'habis' => 'Habis (stok 0)'] as $value => $label)<option value="{{ $value }}" @selected(($filters['stock'] ?? '') === $value)>{{ $label }}</option>@endforeach
    </select></label>
    <button class="site-button" type="submit">CARI</button><a class="site-button site-button--outline" href="{{ route('admin.products.index') }}">RESET</a>
</form>
