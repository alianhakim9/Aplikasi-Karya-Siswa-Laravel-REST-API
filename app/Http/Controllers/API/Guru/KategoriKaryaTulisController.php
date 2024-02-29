<?php

namespace App\Http\Controllers\API\Guru;

use App\Http\Controllers\Controller;
use App\Models\KategoriKaryaTulis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller: controller API yang digunakan untuk mengelola kategori karya tulis
 * **/
class KategoriKaryaTulisController extends Controller
{
    /**
     * variable: deklarasi variable 
     * **/
    private $kategoriKaryaTulis;
    /**
     * constructor: membuat constructor untuk constructor dependency injection
     * **/
    public function __construct(KategoriKaryaTulis $kategoriKaryaTulis)
    {
        $this->kategoriKaryaTulis = $kategoriKaryaTulis;
    }
    /**
     * method: digunakan untuk menampilkan list dari data kategori karya tulis
     * **/
    public function index()
    {
        $data = $this->kategoriKaryaTulis->all();
        /**
         * condition: jika data tersedia
         * **/
        if ($data) {
            return response()->json([
                'data' => $data,
                'message' => 'Kategori karya tulis',
            ], 200);
        }
        /**
         * kondisi: jika data tidak ada
         * **/
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }
    /**
     * method: digunakan untuk menambahkan data kategori karya tulis
     * **/
    public function store(Request $request)
    {
        /**
         * gate: hanya guru yang dapat melakukan request ke method ini
         * **/
        $this->authorize('is_guru');
        /**
         * validation: melakukan validasi terhadap beberapa data request
         * **/
        $this->validate($request, [
            'nama_kategori' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $this->kategoriKaryaTulis;
            $data->nama_kategori = $request->nama_kategori;
            $data->save();
            /**
             * db action: simpan data kategori karya tulis
             * **/
            DB::commit();
            return response()->json([
                'data' => $data,
                'message' => 'Kategori karya tulis berhasil ditambahkan'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'data' => $data,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }
    /**
     * method: digunakan untuk menghapus kategori karya tulis berdasarkan id
     * **/
    public function destroy($id)
    {
        /**
         * gate: hanya guru yang dapat melakukan request ke method ini
         * **/
        $this->authorize('is_guru');
        $data = $this->kategoriKaryaTulis::where('id', $id);
        /**
         * condition: jika data kategori karya tulis tersedia  
         * **/
        if ($data) {
            DB::beginTransaction();
            try {
                $data->delete();
                /**
                 * db action: hapus data kategori karya tulis
                 * **/
                DB::commit();
                return response()->json([
                    'message' => 'Kategori karya tulis berhasil dihapus'
                ], 200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json([
                    'message' => 'Terjadi kesalahan server'
                ], 500);
            }
        }
        /**
         * condition: jika data karya tulis tidak tersedia
         * **/
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }
}
