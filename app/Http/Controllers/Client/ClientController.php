<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\KaryaCitra;
use App\Models\KaryaTulis;
use App\Models\KomentarKaryaCitra;
use App\Models\KomentarKaryaTulis;
use App\Models\Promosi;

class ClientController extends Controller
{
    public function index()
    {
        $data =
            KaryaCitra::select('id', 'nama_karya', 'karya', 'id_siswa', 'slug')
            ->with([
                'siswa' => function ($q) {
                    return $q->select('id', 'nama_lengkap', 'nisn', 'foto_profil');
                },
            ])
            ->withCount('komentar', 'like')
            ->limit(7)
            ->get();
        return response()->json($data, 200);
    }


    public function karyaCitraAll()
    {
        $data =
            KaryaCitra::select('id', 'nama_karya', 'karya', 'id_siswa', 'slug', 'excerpt', 'created_at', 'kategori_karya_citra_id')
            ->with([
                'siswa' => function ($q) {
                    return $q->select('id', 'nama_lengkap', 'nisn', 'foto_profil');
                },
            ])
            ->withCount('komentar', 'like')
            ->where('status', 'Disetujui')
            ->paginate(8);
        return response()->json($data);
    }

    public function karyaTulisAll()
    {
        $data =
            KaryaTulis::select('id', 'judul_karya', 'konten_karya', 'id_siswa', 'slug', 'excerpt', 'created_at', 'kategori_karya_tulis_id')
            ->with([
                'siswa' => function ($q) {
                    return $q->select('id', 'nama_lengkap', 'nisn', 'foto_profil');
                },
            ])
            ->withCount('komentar', 'like')->paginate(8);
        return response()->json($data);
    }

    public function detailKaryaCitra($slug)
    {
        $data = KaryaCitra::with('siswa', 'komentar')
            ->withCount('komentar', 'like')
            ->where('slug', $slug)
            ->first();
        $komentar = KomentarKaryaCitra::where('karya_citra_id', $data['id'])
            ->with('user')
            ->paginate(10);
        return response()->json([
            'karya_citra' => $data,
            'komentar' => $komentar
        ]);
    }

    public function detailKaryaTulis($slug)
    {
        $data = KaryaTulis::with('siswa', 'komentar')
            ->withCount('komentar', 'like')
            ->where('slug', $slug)
            ->first();
        $komentar = KomentarKaryaTulis::where('karya_tulis_id', $data['id'])
            ->with('user')
            ->paginate(10);
        return response()->json([
            'karya_tulis' => $data,
            'komentar' => $komentar
        ]);
    }

    public function getAllPromosi()
    {
        $promosi = Promosi::where('status', 'AKTIF')->with('timPPDB')->orderBy('tanggal_promosi')->first();
        return response()->json([
            'data' => $promosi,
            'message' => 'data promosi'
        ]);
    }
}
