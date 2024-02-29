<?php

namespace App\Http\Controllers\API\TimPPDB;

use App\Http\Controllers\Controller;
use App\Models\Promosi;
use App\Models\TimPPDB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;


/**
 * controller: controller API ini digunakan untuk mengelola data promosi
 * **/
class PromosiController extends Controller
{
    /**
     * method: digunakan untuk menampilkan data promosi
     * **/
    public function index()
    {
        $this->authorize('is_tim_ppdb');
        $promosi = Promosi::where('status', 'AKTIF')->with('timPPDB')->orderBy('tanggal_promosi')->get();
        return response()->json([
            'data' => $promosi,
            'message' => 'data promosi'
        ]);
    }
    public function active()
    {
        $this->authorize('is_tim_ppdb');
        $promosi = Promosi::where('status', 'AKTIF')->with('timPPDB')->orderBy('tanggal_promosi')->first();
        return response()->json([
            'data' => $promosi,
            'message' => 'data promosi'
        ]);
    }
    /**
     * method: digunakan untuk menambahkan data promosi
     * **/
    public function store(Request $request)
    {
        /**
         * gate: hanya dapat diakses oleh tim ppdb
         * **/
        $this->authorize('is_tim_ppdb');
        /**
         * validation: melakukan validasi terhadap request
         * **/
        $this->validate($request, [
            'nama_promosi' => 'required',
            'keterangan' => 'required',
            'gambar' => 'required|file|mimes:jpg,png,jpeg,mp4|max:5000',
            'tanggal_promosi' => 'required',
            'status' => 'required'
        ]);
        $timPPDB = $this->findTimPppdbByUserId();
        if ($timPPDB) {
            DB::beginTransaction();
            try {
                $promosi = new Promosi();
                $promosi->nama_promosi = $request->nama_promosi;
                if ($request->hasFile('gambar')) {
                    $extension = $request->file('gambar')->extension();
                    $gambarPromosi = 'promosi-' . date('dmyHis') . '.' . $extension;
                    $photo = Image::make($request->file('gambar'))
                        ->resize(1080, 1080, function ($constraint) {
                            $constraint->aspectRatio();
                        })->encode('png', 80);
                    Storage::disk('promosi')->put($gambarPromosi, $photo);
                    $promosi->gambar = $gambarPromosi;
                }
                $promosi->keterangan = $request->keterangan;
                $promosi->tanggal_promosi = $request->tanggal_promosi;
                $promosi->tim_ppdb_id = $timPPDB->id;
                $promosi->status = $request->status;
                $promosi->save();
                DB::commit();
                return  response()->json([
                    'data' => $promosi,
                    'message' => 'Promosi berhasil diunggah'
                ]);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json([
                    'message' => 'Terjadi kesalahan server',
                    'error' => $th->getMessage()
                ], 500);
            }
        }
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }
    /**
     * method: digunakan untuk mengubah data promosi
     * **/
    public function update(Request $request, $id)
    {
        /**
         * gate: hanya dapat diakses oleh tim ppdb
         * **/
        $this->authorize('is_tim_ppdb');
        $promosi = $this->findPromosiById($id);
        $tim_ppdb = $this->findTimPppdbByUserId(auth()->user()->id);
        /**
         * validation: melakukan validasi terhadap data request
         * **/
        $this->validate($request, [
            'nama_promosi' => 'required',
            'keterangan' => 'required',
            'tanggal_promosi' => 'required',
            'status' => 'required'
        ]);
        if ($tim_ppdb && $promosi) {
            DB::beginTransaction();
            try {
                $promosi->nama_promosi = $request->nama_promosi;
                if ($request->hasFile('gambar')) {
                    if (file_exists(public_path('storage/promosi/' . $promosi->gambar_file))) {
                        Storage::disk('promosi')->delete($promosi->gambar_file);
                    }
                    $extension = $request->file('gambar')->extension();
                    $gambarPromosi = 'promosi-' . date('dmyHis') . '.' . $extension;
                    $photo = Image::make($request->file('gambar'))
                        ->resize(1080, 1080, function ($constraint) {
                            $constraint->aspectRatio();
                        })->encode('png', 80);
                    Storage::disk('promosi')->put($gambarPromosi, $photo);
                    $promosi->gambar = $gambarPromosi;
                }
                $promosi->keterangan = $request->keterangan;
                $promosi->tanggal_promosi = $request->tanggal_promosi;
                $promosi->tim_ppdb_id = $tim_ppdb->id;
                $promosi->status = $request->status;
                $promosi->save();
                DB::commit();
                return  response()->json([
                    'data' => $promosi,
                    'message' => 'promosi citra berhasil diubah'
                ]);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json([
                    'message' => 'Terjadi kesalahan server',
                    'error' => $th->getMessage()
                ], 500);
            }
        }
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }
    /**
     * method: digunakan untuk menghapus data promosi berdasarkan id
     * **/
    public function destroy($id_promosi)
    {
        /**
         * gate: hanya dapat diakses oleh tim ppdb
         * **/
        $promosi = $this->findPromosiById($id_promosi);
        DB::beginTransaction();
        if ($promosi) {
            try {
                if (file_exists(public_path('storage\promosi') . '\\' .  $promosi->gambar)) {
                    unlink('storage/promosi/' . $promosi->gambar);
                }
                $promosi->delete();
                DB::commit();
                return  response()->json([
                    'message' => 'Promosi berhasil dihapus'
                ], 200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json([
                    'message' => 'Terjadi kesalahan server',
                    'error' => $th->getMessage()
                ], 500);
            }
        }
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }
    /**
     * method: digunakan untuk menampilkan data promosi berdasarkan id promosi
     * **/
    public function show($id_promosi)
    {
        /**
         * gate: hanya dapat diakses oleh tim ppdb
         * **/
        $this->authorize('is_tim_ppdb');
        $promosi = Promosi::where('id', $id_promosi)->first();
        if ($promosi) {
            return response()->json([
                'data' => $promosi,
                'message' => 'data promosi by id'
            ]);
        }
        return response()->json([
            'message' => 'data tidak tersedia'
        ], 404);
    }
    /**
     * method: digunakan untuk menampilkan data promosi berdasarkan id tim ppdb
     * **/
    public function byTimPPDB($id_tim_ppdb)
    {
        /**
         * gate: hanya dapat diakses oleh tim ppdb
         * **/
        $this->authorize('is_tim_ppdb');
        $promosi = Promosi::where('tim_ppdb_id', $id_tim_ppdb)->with('timPPDB')->get();
        if ($promosi) {
            return response()->json([
                'data' => $promosi,
                'message' => 'data promosi by id tim ppdb'
            ]);
        }
        return response()->json([
            'message' => 'data tidak tersedia'
        ], 404);
    }
    /**
     * method: digunakan untuk mengambil data tim ppdb berdasarkan user id
     * **/
    public function findTimPppdbByUserId()
    {
        return TimPPDB::where('user_id', auth()->user()->id)->first();
    }
    /**
     * method: digunakan untuk mengambil data promosi berdasarkan id
     * **/
    public function findPromosiById($id)
    {
        return Promosi::where('id', $id)->first();
    }
}
