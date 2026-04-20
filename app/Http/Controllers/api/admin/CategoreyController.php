<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\categoreyRequest;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoreyController extends Controller
{
    use apiResponse;

    private $cacheTime = 600;

    // =========================
    // INDEX
    // =========================
    public function index()
    {
        try {
            $page = request('page', 1);

            $categories = Cache::tags('categories')->remember(
                "page_$page",
                $this->cacheTime,
                fn() => Categories::paginate(6)
            );

            return $this->success($categories, 'All Categories');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load categories', $e);
        }
    }

    // =========================
    // ALL
    // =========================
    public function all()
    {
        try {
            $categories = Cache::tags('categories')->remember(
                'all',
                $this->cacheTime,
                fn() => Categories::all()
            );

            return $this->success($categories, 'All Categories');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load categories', $e);
        }
    }

    // =========================
    // COUNT
    // =========================
    public function count()
    {
        try {
            $count = Cache::tags('categories')->remember(
                'count',
                $this->cacheTime,
                fn() => Categories::count()
            );

            return $this->success($count, 'Categories Count');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to get categories count', $e);
        }
    }

    // =========================
    // SHOW
    // =========================
    public function show()
    {
        try {
            $id = request('id');

            $category = Cache::tags('categories')->remember(
                "category_$id",
                $this->cacheTime,
                fn() => Categories::with('products')->find($id)
            );

            if (!$category) {
                return $this->notFound('Category Not Found');
            }

            return $this->success($category, 'Category Details');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load category', $e);
        }
    }

    // =========================
    // PRODUCTS
    // =========================
    public function products()
    {
        try {
            $id = request('id');

            $category = Categories::find($id);

            if (!$category) {
                return $this->notFound('Category Not Found');
            }

            return $this->success($category->products, 'Category Products');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load products', $e);
        }
    }

    // =========================
    // CREATE
    // =========================
    public function create(categoreyRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            $data['slug'] = $data['name'] . '-' . time();

            $category = Categories::create($data);

            Cache::tags('categories')->flush();

            DB::commit();

            return $this->created($category, 'Category Created Successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError('Failed to create category', $e);
        }
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request)
    {
        try {
            $id = request('id');

            $category = Categories::find($id);

            if (!$category) {
                return $this->notFound('Category Not Found');
            }

            $data = $request->all();

            $category->update($data);

            Cache::tags('categories')->flush();

            return $this->success($category, 'Category Updated Successfully');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to update category', $e);
        }
    }

    // =========================
    // DELETE
    // =========================
    public function destroy()
    {
        try {
            $id = request('id');

            $category = Categories::find($id);

            if (!$category) {
                return $this->notFound('Category Not Found');
            }

            $category->delete();

            Cache::tags('categories')->flush();

            return $this->success([], 'Category Deleted Successfully');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to delete category', $e);
        }
    }
}