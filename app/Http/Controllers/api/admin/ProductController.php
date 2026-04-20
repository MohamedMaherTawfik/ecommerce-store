<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Products;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
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

            $products = Cache::tags('products')->remember(
                "page_$page",
                $this->cacheTime,
                fn() => Products::paginate(10)
            );

            return $this->success($products, 'All Products');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load products', $e);
        }
    }

    // =========================
    // COUNT
    // =========================
    public function count()
    {
        try {
            $count = Cache::tags('products')->remember(
                'count',
                $this->cacheTime,
                fn() => Products::count()
            );

            return $this->success($count, 'Products Count');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to get products count', $e);
        }
    }

    // =========================
    // SHOW
    // =========================
    public function show()
    {
        try {
            $id = request('id');

            $product = Cache::tags('products')->remember(
                "product_$id",
                $this->cacheTime,
                fn() => Products::with('category', 'brand')->find($id)
            );

            if (!$product) {
                return $this->notFound('Product Not Found');
            }

            return $this->success($product, 'Product Details');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load product', $e);
        }
    }

    // =========================
    // CREATE
    // =========================
    public function create(ProductRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $data['slug'] = $data['name'] . '-' . time();

            $product = Products::create($data);

            Cache::tags('products')->flush();

            DB::commit();

            return $this->created($product, 'Product Created Successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError('Failed to create product', $e);
        }
    }

    // =========================
    // UPDATE
    // =========================
    public function update(ProductRequest $request)
    {
        try {
            DB::beginTransaction();

            $id = request('id');

            $product = Products::find($id);

            if (!$product) {
                return $this->notFound('Product Not Found');
            }

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $data['slug'] = $data['name'] . '-' . time();

            $product->update($data);

            Cache::tags('products')->flush();

            DB::commit();

            return $this->success($product, 'Product Updated Successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError('Failed to update product', $e);
        }
    }

    // =========================
    // DELETE
    // =========================
    public function destroy()
    {
        try {
            $id = request('id');

            $product = Products::find($id);

            if (!$product) {
                return $this->notFound('Product Not Found');
            }

            $product->delete();

            Cache::tags('products')->flush();

            return $this->success([], 'Product Deleted Successfully');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to delete product', $e);
        }
    }
}
