<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\GuruImport;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ManajemenGuruController extends Controller
{
    private $user;
    private $guru;

    public function __construct(User $user, Guru $guru)
    {
        $this->user = $user;
        $this->guru = $guru;
    }

    public function index()
    {
        $data = $this->guru::with('user')->paginate(5);
        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $this->authorize('is_admin');

        $this->validate($request, [
            'email' => 'required|email:dns|unique:users',
            'password' => 'required',
            'password_confirm' => 'required',
            'nama_lengkap' => 'required',
            'nuptk' => 'required|unique:guru|max:16',
            'agama' => 'required',
            'ttl' => 'required',
            'alamat' => 'required',
            'jabatan' => 'required',
            'gelar' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $user = $this->user;
            $user->email = strtolower($request->email);
            $user->password = Hash::make($request->password);
            $user->role_id = 2;
            $user->save();
            $guru = $this->guru;
            $guru->nama_lengkap = ucfirst($request->nama_lengkap);
            $guru->nuptk = strtoupper($request->nuptk);
            $guru->jenis_kelamin = strtoupper($request->jenis_kelamin);
            $guru->agama =  ucfirst($request->agama);
            $guru->foto_profil = 'default.jpg';
            $guru->ttl = $request->ttl;
            $guru->alamat = strtoupper($request->alamat);
            $guru->jabatan = ucfirst($request->jabatan);
            $guru->gelar = strtoupper($request->gelar);
            $guru->user_id = $user->id;
            $guru->save();
            DB::commit();
            return response()->json($guru, 201);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json($th->getMessage(), 500);
        }
    }

    public function edit($id)
    {
        $data = $this->guru::with('user')->where('id', $id)->first();
        return response()->json($data, 200);
    }

    public function update(Request $request)
    {
        $this->authorize('is_admin');
        $dataGuru = $this->guru::with('user')->where('id', $request->id)->first();
        $dataUser = $this->user::where('id', $request->user_id)->first();
        if ($dataGuru && $dataUser) {
            DB::beginTransaction();
            try {
                $dataUser->email = $request->email;
                $dataUser->password = ($request->password === null ? $dataUser->password : Hash::make($request->password));
                $dataUser->save();
                $dataGuru->nama_lengkap = $request->nama_lengkap;
                $dataGuru->nuptk = $request->nuptk;
                $dataGuru->jenis_kelamin = $request->jenis_kelamin;
                $dataGuru->agama = $request->agama;
                $dataGuru->ttl = $request->ttl;
                $dataGuru->alamat = $request->alamat;
                $dataGuru->jabatan = $request->jabatan;
                $dataGuru->gelar = $request->gelar;
                $dataGuru->save();
                DB::commit();
                return response()->json($dataGuru, 200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json($th->getMessage(), 500);
            }
        }
    }

    public function destroy($id)
    {
        $this->authorize('is_admin');
        $dataGuru = $this->guru::with('user')->where('id', $id)->first();
        $dataUser = $this->user->where('id', $dataGuru->user_id)->first();
        if ($dataGuru && $dataUser) {
            DB::beginTransaction();
            try {
                $dataGuru->delete();
                $dataUser->delete();
                DB::commit();
                return response()->json(200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json($th->getMessage(), 500);
            }
        }
    }

    public function import(Request $request)
    {
        $this->authorize('is_admin');
        $import = Excel::import(new GuruImport, request()->file('file'));
        if ($import) {
            return response()->json(['message' => 'Import successful', 200]);
        } else {
            return response()->json(['message' => 'Import failed', 400]);
        }
    }
}
