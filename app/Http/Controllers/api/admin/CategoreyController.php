<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\categoreyRequest;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoreyController extends Controller
{
    use apiResponse;

    // وقت الكاش بالثواني
    private $cacheTime = 600;

    /**
     * Get paginated categories with caching
     */
    public function index()
    {
        $page = request('page', 1);
        $cacheKey = "categories_page_{$page}";

        $categories = Cache::remember($cacheKey, $this->cacheTime, function () {
            return Categories::paginate(10);
        });

        return $this->apiResponse($categories, 'All Categories');
    }

    /**
     * Get total count of categories with caching
     */
    public function count()
    {
        $cacheKey = 'categories_count';

        $count = Cache::remember($cacheKey, $this->cacheTime, function () {
            return Categories::count();
        });

        return $this->apiResponse($count, 'Categories Count');
    }

    /**
     * Show single category with caching
     */
    public function show()
    {
        $id = request('id');
        $cacheKey = "category_{$id}";

        $category = Cache::remember($cacheKey, $this->cacheTime, function () use ($id) {
            return Categories::find($id);
        });

        if (!$category) {
            return $this->apiResponse([], 'Category Not Found');
        }

        return $this->apiResponse($category, 'Category Details');
    }

    /**
     * Placeholder for products of a category
     */
    public function products()
    {
        return $this->apiResponse([], 'Products');
    }

    /**
     * Create a new category and clear relevant cache
     */
    public function create(categoreyRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $data['image'] = $file->store('Categories', 'public');
        }

        $data['slug'] = $data['name'] . '-' . time();

        $category = Categories::create($data);

        // Clear cache
        $this->clearCategoryCache($category->id);

        return $this->apiResponse($category, 'Category Created Successfully');
    }

    /**
     * Update a category and clear relevant cache
     */
    public function update(Request $request)
    {
        $id = request('id');
        $category = Categories::find($id);

        if (!$category) {
            return $this->apiResponse([], 'Category Not Found');
        }

        $data = $request->all();
        $category->update($data);

        // Clear cache
        $this->clearCategoryCache($id);

        return $this->apiResponse($category, 'Category Updated Successfully');
    }

    /**
     * Delete a category and clear relevant cache
     */
    public function destroy()
    {
        $id = request('id');
        $category = Categories::find($id);

        if (!$category) {
            return $this->apiResponse([], 'Category Not Found');
        }

        $category->delete();

        // Clear cache
        $this->clearCategoryCache($id);

        return $this->apiResponse([], 'Category Deleted Successfully');
    }

    /**
     * Helper function to clear all relevant cache keys
     */
    private function clearCategoryCache($id)
    {
        Cache::forget("category_{$id}");
        Cache::forget('categories_count');

        // Optionally, clear all cached pages
        $pages = ceil(Categories::count() / 10);
        for ($i = 1; $i <= $pages; $i++) {
            Cache::forget("categories_page_{$i}");
        }
    }
}