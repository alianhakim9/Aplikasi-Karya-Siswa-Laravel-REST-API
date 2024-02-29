<?php

namespace App\Http\Controllers\API\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;

/**
 * controller: controller API ini digunakan untuk mengelola data profil siswa
 * **/
class SiswaController extends Controller
{
    public function show()
    {
        $userId = auth()->user()->id;
        $data = Siswa::where('user_id', $userId)->with('user')->first();
        /** 
         * condition : jika data tersedia
         * **/
        if ($data) {
            return response()->json(
                [
                    'data' => $data,
                    'message' => 'lihat data profil siswa',
                ],
                200
            );
        }
        /** 
         * condition : jika data tidak tersedia
         * **/
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }
    /** 
     * method : digunakan untuk melakukan update terhadap profil siswa berdasarkan id
     * **/
    public function update(Request $request, $id)
    {
        /** 
         * gate : hanya siswa yang dapat melakukan pemanggilan terhadap method ini
         * **/
        $this->authorize('is_siswa');
        $data = Siswa::where('id', $id)->with('user')->first();
        /** 
         * condition: jika data tersedia 
         * **/
        if ($data) {
            DB::beginTransaction();
            try {
                $data->nama_lengkap = $request->nama_lengkap;
                $data->nisn = $request->nisn;
                $data->jenis_kelamin = $request->jenis_kelamin;
                $data->agama = $request->agama;
                $data->ttl = $request->ttl;
                $data->alamat = $request->alamat;
                if ($request->hasFile('foto_profil')) {
                    if (file_exists(public_path('storage/foto_profil/siswa/' . $data->foto_profil_file))) {
                        Storage::disk('foto_profil_siswa')->delete($data->foto_profil_file);
                    }
                    $extension = $request->file('foto_profil')->extension();
                    $fotoProfil = 'foto-profil-siswa-' . date('dmyHis') . '.' . $extension;
                    $photo = Image::make($request->file('foto_profil'))
                        ->resize(150, 150, function ($constraint) {
                            $constraint->aspectRatio();
                        })->encode('jpg', 80);
                    Storage::disk('foto_profil_siswa')->put($fotoProfil, $photo);
                    $data->foto_profil = $fotoProfil;
                }
                $data->save();
                DB::commit();
                return response()->json([
                    'data' => $data,
                    'message' => 'Profil berhasil disimpan'
                ], 200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json([
                    'data' => $th->getMessage(),
                    'message' => 'Terjadi kesalahan server'
                ], 500);
            }
        }
        /** 
         * condition : jika data tidak tersedia
         * **/
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }
}
