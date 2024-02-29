<?php

namespace App\Http\Controllers;

use App\Mail\TokenPassword;
use App\Models\Guru;
use App\Models\KaryaCitra;
use App\Models\KaryaTulis;
use App\Models\Siswa;
use App\Models\TimPPDB;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class AuthController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->firstOrFail();
        return $user;
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:dns',
            'password' => 'required|min:8'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->getMessageBag()
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'message' => 'password berhasil diubah'
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if ($user->exists) {
            // $status = Password::sendResetLink($request->only('email'));
            // return response()->json([
            //     'message' => $status === Password::RESET_LINK_SENT ? 'link reset password berhasil dikirim ke alamat email' : true
            // ]);
            $token = Password::getRepository()->create($user);
            Mail::to($request->email)->send(new TokenPassword($token));
            return response()->json([
                'message' => 'link reset password berhasil dikirim ke alamat email'
            ]);
        } else {
            return response()->json([
                'message' => 'email tidak terdaftar'
            ], 404);
        }
    }

    public function resetPassword(Request $request)
    {
        return response()->json([
            'token' => $request->input('token')
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(
                [
                    'message' => 'password berhasil diubah'
                ]
            );
        } else {
            return response()->json([
                'message' => 'password gagal diubah'
            ]);
        }
    }
}
