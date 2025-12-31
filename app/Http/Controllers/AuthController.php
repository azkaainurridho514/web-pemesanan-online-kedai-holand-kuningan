<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index(){
        return view('login');
    }

   public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'redirect' => url('/admin/dashboard')
        ], 200);
    }

}
