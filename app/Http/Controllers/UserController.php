<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Exception;

class UserController extends Controller
{
    /**
     * Get All Users
     *
     * Daftar pengguna dengan paginasi, pencarian, dan filter role.
     * 
     * @tags User Management
     *
     * @queryParam search string opsional Cari berdasarkan name. Example: budi
     * @queryParam role_id int opsional Filter berdasarkan role. Example: 2
     * @queryParam per_page int opsional Jumlah per halaman. Default: 25
     */
    public function index(Request $request)
    {
        try {
            $query = User::with('role');

            // Search berdasarkan name
            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where('name', 'like', "%{$search}%");
            }

            // Filter berdasarkan role
            if ($request->filled('role_id')) {
                $query->where('role_id', $request->query('role_id'));
            }

            $perPage = $request->query('per_page', 25);
            $result = $query->latest()->paginate($perPage);

            return response()->json([
                'message' => 'Users fetched successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create New User
     *
     * Menambahkan pengguna baru ke dalam sistem. Password akan di-hash secara otomatis.
     * 
     * @tags User Management
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);

            $result = User::create($validated);
            $result->load('role');

            return response()->json([
                'message' => 'User created successfully',
                'data' => $result,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get User Detail
     * 
     * Menampilkan detail spesifik dari satu pengguna berdasarkan ID.
     * 
     * @tags User Management
     * @urlParam id string required ID dari user. Example: 1
     */
    public function show(string $id)
    {
        try {
            $result = User::with('role')->find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'message' => 'User fetched successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update User
     * 
     * Memperbarui data pengguna. Super Admin (role_id = 1) tidak dapat di-edit melalui endpoint ini.
     * Jika password dikosongkan, password tidak akan diubah.
     * 
     * @tags User Management
     * @urlParam id string required ID dari user. Example: 2
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            $result = User::find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }

            if ($result->role_id === 1) {
                return response()->json([
                    'message' => 'Super Admin tidak dapat diedit',
                ], 403);
            }

            $validated = $request->validated();

            if (empty($validated['password'])) {
                unset($validated['password']);
            } else {
                $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
            }

            $result->update($validated);
            $result->load('role');

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete User
     * 
     * Menghapus pengguna secara soft-delete. Data terkait (rapat, assignment, dokumen) tetap tersimpan.
     * Akses API token (Sanctum) milik user akan langsung dicabut.
     * 
     * @tags User Management
     * @urlParam id string required ID dari user. Example: 2
     */
    public function destroy(string $id)
    {
        try {
            $result = User::find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }

            // Hanya super_admin yang tidak bisa dihapus
            if ($result->role_id === 1) {
                return response()->json([
                    'message' => 'Super Admin tidak dapat dihapus',
                ], 403);
            }

            // Revoke semua API tokens (Sanctum) agar tidak bisa login lagi
            $result->tokens()->delete();

            // Soft delete — data relasi tetap tersimpan agar bisa di-restore
            $result->delete();

            return response()->json([
                'message' => 'User deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get All Roles
     * 
     * Menampilkan daftar semua role yang ada di database. 
     * Output dari endpoint ini di-cache.
     * 
     * @tags User Management
     */
    public function roles()
    {
        try {
            $result = Cache::rememberForever('roles', function () {
                return Role::all();
            });
            return response()->json([
                'message' => 'Roles fetched successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching roles',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}