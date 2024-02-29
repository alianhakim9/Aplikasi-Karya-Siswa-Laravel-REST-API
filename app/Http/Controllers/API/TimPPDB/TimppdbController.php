<?php

namespace App\Http\Controllers\API\TimPPDB;

use App\Http\Controllers\Controller;
use App\Models\TimPPDB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimppdbController extends Controller
{
    public function show()
    {
        $userId = auth()->user()->id;
        $data = TimPPDB::where('user_id', $userId)->with('user')->first();
        return response()->json(
            [
                'data' => $data,
                'message' => 'lihat data profil tim ppdb',
            ],
            200
        );
    }

    public function update(Request $request, $id)
    {
        $this->authorize('is_tim_ppdb');
        $data = TimPPDB::where('id', $id)->first();
        try {
            DB::beginTransaction();
            $data->nama_lengkap = $request->nama_lengkap;
            $data->jabatan = $request->jabatan;
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
}
