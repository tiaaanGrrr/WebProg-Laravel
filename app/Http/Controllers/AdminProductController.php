<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:255'],
            'stock' => ['nullable', Rule::in(['tersedia', 'menipis', 'habis'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $query = Product::query();
        if (($filters['q'] ?? '') !== '') {
            $keyword = '%'.$filters['q'].'%';
            $query->where(fn (Builder $search): Builder => $search->where('name', 'like', $keyword)->orWhere('description', 'like', $keyword));
        }
        if (($filters['category'] ?? '') !== '') {
            $query->where('category', $filters['category']);
        }
        $stock = $filters['stock'] ?? '';
        if ($stock === 'tersedia') {
            $query->where('stock', '>', 0);
        } elseif ($stock === 'menipis') {
            $query->whereBetween('stock', [1, 5]);
        } elseif ($stock === 'habis') {
            $query->where('stock', 0);
        }

        return view('admin.products.index', [
            'products' => $query->latest('id')->paginate(10)->withQueryString(),
            'categories' => Product::select('category')->distinct()->orderBy('category')->pluck('category'),
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.products.form', ['product' => new Product]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['image'] = 'uploads/'.$request->file('image')->store('products', 'local');
        Product::create($data);

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Product $product): View
    {
        return view('admin.products.form', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $oldImage = $product->image;
        if ($request->hasFile('image')) {
            $data['image'] = 'uploads/'.$request->file('image')->store('products', 'local');
        }
        $product->update($data);
        if (isset($data['image'])) {
            $this->deleteUpload($oldImage);
        }

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $image = $product->image;
        $product->delete();
        $this->deleteUpload($image);

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil dihapus.');
    }

    private function deleteUpload(string $image): void
    {
        if (str_starts_with($image, 'uploads/products/')) {
            Storage::disk('local')->delete(substr($image, strlen('uploads/')));
        }
    }
}
