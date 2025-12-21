<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users (kasir only)
     */
    public function index(Request $request)
    {
        $query = User::with('role')->where('role_id', 2); // Hanya kasir

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);

        return response()->json([
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * Store a newly created kasir
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 2, // Hardcode ke kasir
            ]);

            return response()->json([
                'message' => 'Kasir berhasil ditambahkan',
                'data' => $user->load('role')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan kasir: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified kasir
     */
    public function show(string $id)
    {
        try {
            $user = User::with('role')
                ->where('role_id', 2)
                ->findOrFail($id);
            return response()->json(['data' => $user]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kasir tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified kasir
     */
    public function edit(string $id)
    {
        try {
            $user = User::with('role')
                ->where('role_id', 2)
                ->findOrFail($id);
            
            return response()->json(['data' => $user]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kasir tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified kasir
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::where('role_id', 2)->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return response()->json([
                'message' => 'Kasir berhasil diperbarui',
                'data' => $user->load('role')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui kasir: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified kasir
     */
    public function destroy(string $id)
    {
        try {
            $user = User::where('role_id', 2)->findOrFail($id);
            
            // Prevent deleting own account
            if (auth()->id() === $user->id) {
                return response()->json([
                    'message' => 'Anda tidak dapat menghapus akun sendiri'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'message' => 'Kasir berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus kasir: ' . $e->getMessage()
            ], 500);
        }
    }
}