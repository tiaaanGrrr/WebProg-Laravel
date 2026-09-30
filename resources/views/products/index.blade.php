@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold">Katalog Alat Sembahyang</h2>
    <p class="text-muted">Pilihan dupa, lilin, rupang, dan sarana puja berkualitas.</p>
</div>

<div class="row row-cols-1 row-cols-md-3 g-4">
    @forelse($products as $product)
        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <img src="{{ $product->image }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-secondary mb-2 align-self-start">{{ $product->category }}</span>
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted small flex-grow-1">{{ $product->description }}</p>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold text-danger">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                            Stok: {{ $product->stock }}
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pt-0 pb-3">
                    <button class="btn btn-warning w-100 fw-semibold text-dark">Tambah ke Keranjang</button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted fs-5">Belum ada produk yang tersedia.</p>
        </div>
    @endforelse
</div>
@endsection
