<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use apiResponse;

    private $cacheTime = 1200;

    // =========================
    // INDEX (LATEST USERS)
    // =========================
    public function index()
    {
        try {
            $users = Cache::tags('users')->remember(
                'users_latest_10',
                $this->cacheTime,
                fn() =>
                User::latest()
                    ->where('is_active', 1)
                    ->take(10)
                    ->get()
            );

            return $this->success($users, 'Latest Users');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load users', $e);
        }
    }

    // =========================
    // ALL (PAGINATED)
    // =========================
    public function all()
    {
        try {
            $page = request('page', 1);

            $users = Cache::tags('users')->remember(
                "users_all_page_$page",
                $this->cacheTime,
                fn() => User::latest()->paginate(20)
            );

            return $this->success($users, 'All Users');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load users', $e);
        }
    }

    // =========================
    // COUNT
    // =========================
    public function count()
    {
        try {
            $count = Cache::tags('users')->remember(
                'users_count',
                $this->cacheTime,
                fn() => User::count()
            );

            return $this->success($count, 'Users Count');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to get users count', $e);
        }
    }

    // =========================
    // SHOW
    // =========================
    public function show()
    {
        try {
            $id = request('id');

            $user = Cache::tags('users')->remember(
                "user_$id",
                $this->cacheTime,
                fn() => User::find($id)
            );

            if (!$user) {
                return $this->notFound('User Not Found');
            }

            return $this->success($user, 'User Details');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to load user', $e);
        }
    }

    // =========================
    // PRODUCTS (placeholder)
    // =========================
    public function products()
    {
        return $this->success([], 'Products');
    }

    // =========================
    // CREATE
    // =========================
    public function create(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $data['password'] = bcrypt($data['password']);
            $data['slug'] = $data['name'] . '-' . time();

            $user = User::create($data);

            Cache::tags('users')->flush();

            DB::commit();

            return $this->created($user, 'User Created Successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError('Failed to create user', $e);
        }
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request)
    {
        try {
            $id = request('id');

            $user = User::find($id);

            if (!$user) {
                return $this->notFound('User Not Found');
            }

            $data = $request->all();

            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            $user->update($data);

            Cache::tags('users')->flush();

            return $this->success($user, 'User Updated Successfully');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to update user', $e);
        }
    }

    // =========================
    // DELETE
    // =========================
    public function destroy()
    {
        try {
            $id = request('id');

            $user = User::find($id);

            if (!$user) {
                return $this->notFound('User Not Found');
            }

            $user->delete();

            Cache::tags('users')->flush();

            return $this->success([], 'User Deleted Successfully');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to delete user', $e);
        }
    }

    // =========================
    // CLEAR CACHE
    // =========================
    public function clearCache()
    {
        try {
            Cache::tags('users')->flush();

            return $this->success([], 'Users Cache Cleared Successfully');
        } catch (\Throwable $e) {
            return $this->serverError('Failed to clear cache', $e);
        }
    }
}
