<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'productionBatch', 'stock'])
            ->search($request->search)
            ->paginate();

        $categories = Category::query()->get(['id', 'name']);
        $productionBatches = ProductionBatch::query()->get(['id', 'batch_number', 'production_date']);

        return Inertia::render('Admin/Products', [
            'records' => ProductResource::collection($products),
            'categories' => $categories,
            'productionBatches' => $productionBatches,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image_url' => ['nullable', 'mimes:png,jpg,jpeg,webp', 'max:10240'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'max:255'],
            'price' => ['required', 'numeric'],
            'sku' => ['required', 'string', 'max:255'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')->storePublicly('products_image', 'public');
        }

        Product::create(Arr::except($data, 'has_image_url'));

        return redirect()->back();
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'image_url' => ['nullable', 'mimes:png,jpg,jpeg', 'max:10240'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'max:255'],
            'price' => ['required', 'numeric'],
            'sku' => ['required', 'string', 'max:255'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')->storePublicly('products_image', 'public');
        } else {
            $data['image_url'] = $product->image_url;
        }

        $product->update(Arr::except($data, 'has_image_url'));

        return redirect()->back();
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->back();
    }

    public function addStock(Request $request, Product $product)
    {

        $data = $request->validate([
            'stock' => ['required', 'numeric', 'gt:0'],
        ]);

        $stock = Stock::where('product_id', $product->id)->first();

        if ($stock) {
            // Product already exists in stocks table, update the stock
            $stock->stock += data_get($data, 'stock');
            $stock->save();
        } else {
            // Product doesn't exist in stocks table, create a new entry
            $stock = new Stock;
            $stock->product_id = $product->id;
            $stock->stock = data_get($data, 'stock');
            $stock->save();
        }

        return redirect()->back();
    }
}
