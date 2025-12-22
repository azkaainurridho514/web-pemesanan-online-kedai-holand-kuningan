<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function userView(){
        return view('dashboard.home.user');
    }
    public function getData(Request $request)
    {
        $search = $request->search;
        $query = User::with('role')->where('role_id', 2);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $data = $query->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|regex:/^[0-9+\-\s()]*$/',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'plain_password' => $validated['password'],
            'phone' => $validated['phone'],
            'role_id' => 2,
        ]);

        return response()->json([
            'message' => 'Kasir berhasil ditambahkan',
            'data' => $user->load('role')
        ], 201);
    }

    public function show($id)
    {
        $user = User::with('role')
            ->where('role_id', 2)
            ->findOrFail($id);
            
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role_id', 2)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
            $data['plain_password'] = $validated['password']; 
        }

        $user->update($data);

        return response()->json([
            'message' => 'Kasir berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        $user = User::where('role_id', 2)->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Kasir tidak ditemukan.'
            ], 404);
        }

        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Anda tidak dapat menghapus akun sendiri.'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'Kasir berhasil dihapus.'
        ], 200);
    }
}