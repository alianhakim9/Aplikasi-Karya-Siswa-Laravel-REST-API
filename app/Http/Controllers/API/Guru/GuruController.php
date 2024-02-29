<?php

namespace App\Http\Controllers\API\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;

/**
 * controller: controller API yang digunakan untuk mengelola data guru termasuk didalamnya view profil guru dan edit profil guru
 * **/
class GuruController extends Controller
{
    /**
     * variable: deklarasi variable guru
     * **/
    private $guru;
    /**
     * constructor: inisiasi awal pada saat class dipanggil dan melakukan constructor dependency injection
     * **/
    public function __construct(Guru $guru)
    {
        $this->guru = $guru;
    }
    /**
     * method: digunakan untuk menampilkan data guru berdasarkan nuptk
     * **/
    public function show()
    {
        $userId = auth()->user()->id;
        $profilGuru = $this->guru::where('user_id', $userId)->with('user')->firstOrFail();
        return response()->json([
            'data' => $profilGuru,
            'message' => 'profil guru',
        ], 200);
    }
    /**
     * method: digunakan untuk melakukan edit profil berdasarkan id
     * **/
    public function update(Request $request, $id)
    {
        $this->authorize('is_guru');
        $dataguru = $this->guru::where('id', $id)->with('user')->first();

        $validator = Validator::make($request->all(), [
            'foto_profil' => 'max:1024',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 402);
        }
        try {
            $dataguru->nama_lengkap = $request->nama_lengkap;
            $dataguru->nuptk = $request->nuptk;
            $dataguru->jenis_kelamin = $request->jenis_kelamin;
            $dataguru->agama = $request->agama;
            $dataguru->ttl = $request->ttl;
            $dataguru->jabatan = $request->jabatan;
            $dataguru->gelar = $request->gelar;
            $dataguru->alamat = $request->alamat;
            if ($request->hasFile('foto_profil')) {
                if (file_exists(public_path('storage/foto_profil/guru/' . $dataguru->foto_profil_file))) {
                    Storage::disk('foto_profil_guru')->delete($dataguru->foto_profil_file);
                }
                $extension = $request->file('foto_profil')->extension();
                $fotoProfil = 'foto-profil-guru-' . date('dmyHis') . '.' . $extension;
                $photo = Image::make($request->file('foto_profil'))
                    ->resize(150, 150, function ($constraint) {
                        $constraint->aspectRatio();
                    })->encode('jpg', 80);
                Storage::disk('foto_profil_guru')->put($fotoProfil, $photo);
                $dataguru->foto_profil = $fotoProfil;
            }
            $dataguru->save();
            DB::commit();
            return response()->json([
                'data' => $dataguru,
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
}
