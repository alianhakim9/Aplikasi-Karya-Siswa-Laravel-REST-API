<?php

namespace App\Http\Controllers;

use App\Mail\SendAccountEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('is_admin');
        $akun = [
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => $request->password
        ];
        Mail::to($request->email)->send(new SendAccountEmail($akun));
        return response()->json([
            'message' => 'Akun berhasil dikirimkan'
        ], 200);
    }
}
