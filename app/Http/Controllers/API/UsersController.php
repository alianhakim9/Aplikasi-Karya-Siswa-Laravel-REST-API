<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function login(Request $request)
    {
        $dataLogin = User::where('email', $request->email)->first();
        $this->validate($request, [
            'email' => 'required|email:dns',
            'password' => 'required'
        ]);
        if (!$dataLogin || !Hash::check($request->password, $dataLogin->password)) {
            return response()->json([
                'message' => 'Gagal login'
            ], 401);
        }
        $token = $dataLogin->createToken('token-name')->plainTextToken;
        return response()->json([
            'message' => 'Berhasil login',
            'data' => [
                'user' => $dataLogin,
                'token' => $token
            ]
        ], 200);
    }

    public function currentUser()
    {
        return Auth::user();
    }
}
