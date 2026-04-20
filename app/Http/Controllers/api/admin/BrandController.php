<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    use apiResponse;

    private $cacheTime = 3600;

    // =========================
    // INDEX
    // =========================
    public function index()
    {
        try {
            $page = request('page', 1);

            $brands = Cache::tags('brands')->remember(
                "index_page_$page",
                $this->cacheTime,
                fn() => brands::latest()->paginate(6)
            );

            return $this->success($brands, 'All Brands');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load brands', $e);
        }
    }

    // =========================
    // ALL
    // =========================
    public function all()
    {
        try {
            $brands = Cache::tags('brands')->remember(
                "all",
                $this->cacheTime,
                fn() => brands::all()
            );

            return $this->success($brands, 'All Brands');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load brands', $e);
        }
    }

    // =========================
    // COUNT
    // =========================
    public function count()
    {
        try {
            $count = Cache::tags('brands')->remember(
                "count",
                $this->cacheTime,
                fn() => brands::count()
            );

            return $this->success($count, 'Brands Count');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to get brands count', $e);
        }
    }

    // =========================
    // SHOW
    // =========================
    public function show()
    {
        try {
            $id = request('id');

            $brand = Cache::tags('brands')->remember(
                "brand_$id",
                $this->cacheTime,
                fn() => brands::find($id)
            );

            if (!$brand) {
                return $this->notFound('Brand Not Found');
            }

            return $this->success($brand, 'Brand Details');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load brand', $e);
        }
    }

    // =========================
    // PRODUCTS
    // =========================
    public function products()
    {
        try {
            $id = request('id');

            $brand = brands::with('products')->find($id);

            if (!$brand) {
                return $this->notFound('Brand Not Found');
            }

            return $this->success($brand->products, 'Brand Products');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load products', $e);
        }
    }

    // =========================
    // CREATE
    // =========================
    public function create(BrandRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('brands', 'public');
            }

            $data['slug'] = $data['name'] . '-' . time();

            $brand = brands::create($data);

            $this->clearBrandCache();

            DB::commit();

            return $this->created($brand, 'Brand Created Successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError('Failed to create brand', $e);
        }
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request)
    {
        try {
            $id = request('id');

            $brand = brands::find($id);

            if (!$brand) {
                return $this->notFound('Brand Not Found');
            }

            $data = $request->all();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('brands');
            }

            $brand->update($data);

            $this->clearBrandCache($id);

            return $this->success($brand, 'Brand Updated Successfully');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to update brand', $e);
        }
    }

    // =========================
    // DELETE
    // =========================
    public function destroy()
    {
        try {
            $id = request('id');

            $brand = brands::find($id);

            if (!$brand) {
                return $this->notFound('Brand Not Found');
            }

            $brand->delete();

            $this->clearBrandCache($id);

            return $this->success([], 'Brand Deleted Successfully');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to delete brand', $e);
        }
    }

    // =========================
    // CACHE CLEAR (TAG BASED)
    // =========================
    private function clearBrandCache($id = null)
    {
        try {
            // 🔥 clear all brands cache instantly
            Cache::tags('brands')->flush();

            // safety fallback
            if ($id) {
                Cache::forget("brand_$id");
            }

        } catch (\Throwable $e) {
            report($e);
        }
    }
}