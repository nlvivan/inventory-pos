<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $request->mergeIfMissing([
            'per_page' => 15,
        ]);

        $categories = Category::query()
            ->search($request->search)
            ->paginate($request->per_page);

        return Inertia::render('Admin/Categories', [
            'records' => CategoryResource::collection($categories),
            'filters' => $request->only('search'),
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')->storePublicly('categories_image', 'public');
        }

        Category::create(Arr::except($data, 'has_image_url'));

        return redirect()->back();
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')->storePublicly('categories_image', 'public');
        } else {
            $data['image_url'] = $category->image_url;
        }

        $category->update(Arr::except($data, 'has_image_url'));

        return redirect()->back();
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back();
    }
}
