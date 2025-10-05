<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()
            ->where(function ($q) use ($request) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', $request->user()->id);
            })
            ->get();

        return response()->json($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create([
            'id' => (string) Str::uuid(),
            'name' => $request->name,
            'type' => $request->type,
            'icon' => $request->icon,
            'color' => $request->input('color', '#3B82F6'),
            'user_id' => $request->user()->id,
            'is_default' => false,
            'created_at' => now(),
        ]);

        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }
}



