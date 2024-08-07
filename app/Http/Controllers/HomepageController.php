<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomepageController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'productionBatch', 'stock'])
            ->search($request->search)
            ->latest()
            ->limit(20)
            ->get();

        $categories = Category::query()->get(['id', 'name', 'image_url']);

        return Inertia::render('Homepage/Index', [
            'products' => ProductResource::collection($products),
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    public function productDetails(Product $product)
    {

        $products = Product::query()
            ->whereNot('id', $product->id)
            ->where('category_id', $product->category_id)
            ->with(['category', 'productionBatch', 'stock'])
            ->latest()
            ->limit(20)
            ->get();

        return Inertia::render('Homepage/ProductDetails', [
            'products' => ProductResource::collection($products),
            'product' => ProductResource::make($product->load(['category', 'stock'])),
        ]);
    }

    public function productsByCategory(Request $request, Category $category)
    {
        $productsByCategory = $category->products()
            ->search($request->search)
            ->with(['category', 'productionBatch', 'stock'])
            ->latest()->paginate(25);

        return Inertia::render('Homepage/ProductsByCategory', [
            'records' => ProductResource::collection($productsByCategory),
            'filters' => $request->only('search'),
            'category' => $category,
        ]);
    }
}
