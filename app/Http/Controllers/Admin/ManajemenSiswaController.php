<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\SiswaImport;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ManajemenSiswaController extends Controller
{
    protected $user;
    protected $siswa;

    public function __construct(Siswa $siswa, User $user)
    {
        $this->user = $user;
        $this->siswa = $siswa;
    }

    public function index()
    {
        $data = $this->siswa::with('user')->paginate(5);
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
            'nisn' => 'required|unique:siswa|max:10',
            'agama' => 'required',
            'ttl' => 'required',
            'alamat' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $user = $this->user;
            $user->email = strtolower($request->email);
            $user->password = Hash::make($request->password);
            $user->role_id = 3;
            $user->save();
            $siswa = $this->siswa;
            $siswa->nama_lengkap = ucfirst($request->nama_lengkap);
            $siswa->nisn = strtoupper($request->nisn);
            $siswa->jenis_kelamin = strtoupper($request->jenis_kelamin);
            $siswa->agama =  ucfirst($request->agama);
            $siswa->foto_profil = 'default.jpg';
            $siswa->ttl = $request->ttl;
            $siswa->alamat = strtoupper($request->alamat);
            $siswa->user_id = $user->id;
            $siswa->save();
            DB::commit();
            return response()->json($siswa, 201);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json($th->getMessage(), 500);
        }
    }

    public function edit($nisn)
    {
        $data['siswa'] = $this->siswa::with('user')->where('id', $nisn)->first();
        return response()->json($data, 200);
    }

    public function update(Request $request)
    {
        $this->authorize('is_admin');
        $dataSiswa = $this->siswa::with('user')->where('id', $request->id)->first();
        $dataUser = $this->user::where('id', $dataSiswa->user_id)->first();
        if ($dataSiswa && $dataUser) {
            DB::beginTransaction();
            try {
                $dataUser->email = strtolower($request->email);
                $dataUser->password = ($request->password === null ? $dataUser->password : Hash::make($request->password));
                $dataUser->save();
                $dataSiswa->nama_lengkap = ucfirst($request->nama_lengkap);
                $dataSiswa->nisn = strtoupper($request->nisn);
                $dataSiswa->jenis_kelamin = strtoupper($request->jenis_kelamin);
                $dataSiswa->agama = ucfirst($request->agama);
                $dataSiswa->ttl = $request->ttl;
                $dataSiswa->alamat = strtoupper($request->alamat);
                $dataSiswa->save();
                DB::commit();
                return response()->json($dataSiswa, 200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json($th->getMessage(), 500);
            }
        }
    }

    public function destroy($id)
    {
        $this->authorize('is_admin');
        $dataSiswa = $this->siswa::with('user')->where('id', $id)->first();
        $dataUser = $this->user->where('id', $dataSiswa->user_id)->first();
        if ($dataSiswa && $dataUser) {
            DB::beginTransaction();
            try {
                $dataSiswa->delete();
                $dataUser->delete();
                DB::commit();
                return response()->json($dataUser, 200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json($th->getMessage(), 500);
            }
        }
    }

    public function import(Request $request)
    {
        $this->authorize('is_admin');
        $import = Excel::import(new SiswaImport, request()->file('file'));
        if ($import) {
            return response()->json(['message' => 'Import successful', 200]);
        } else {
            return response()->json(['message' => 'Import failed', 400]);
        }
    }
}
