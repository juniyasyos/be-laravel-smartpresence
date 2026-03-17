<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Http\Requests\storeUserRequest;
use App\Http\Requests\updateUserRequest;
use Illuminate\Http\Request;
use Exception;

class UserController extends Controller
{
    /**
     * Daftar pengguna dengan search & filter.
     *
     * Query params:
     *  - search   : cari berdasarkan username
     *  - role_id  : filter berdasarkan role
     *  - per_page : jumlah per halaman (default 10)
     */
    public function index(Request $request)
    {
        try {
            $query = User::with('role');

            // Search berdasarkan username
            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where('username', 'like', "%{$search}%");
            }

            // Filter berdasarkan role
            if ($request->filled('role_id')) {
                $query->where('role_id', $request->query('role_id'));
            }

            $perPage = $request->query('per_page', 10);
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
     * Tambah pengguna baru.
     */
    public function store(storeUserRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = bcrypt($validated['password']);

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
     * Detail pengguna.
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
     * Update pengguna.
     */
    public function update(updateUserRequest $request, string $id)
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

            // Hash password jika diisi, jika tidak hapus dari update
            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
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
     * Hapus pengguna.
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

            if ($result->role_id === 1) {
                return response()->json([
                    'message' => 'Super Admin tidak dapat dihapus',
                ], 403);
            }

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
     * Daftar role untuk dropdown.
     */
    public function roles()
    {
        try {
            $result = Role::all();
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