<?php

namespace App\Http\Controllers\API\Guru;

use App\Http\Controllers\Controller;
use App\Models\KaryaCitra;
use App\Models\Notifikasi;
use App\Models\StatusKaryaCitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;

/**
 * controller: controller API yang digunakan untuk validasi karya citra
 * **/
class ValidasiKaryaCitraController extends Controller
{
    /** 
     * method : untuk menyetujui karya citra yang diunggah oleh siswa berdasarkan id karya citra
     * **/
    public function terimaKarya($id, Request $request)
    {
        /**
         * gate: hanya guru yang dapat melakukan request ke method ini
         * **/

        $this->authorize('is_guru');
        $data = KaryaCitra::where('id', $id)->first();
        $notifikasi = Notifikasi::where('id_karya_citra', $id)->where('status', 'Menunggu validasi')->first();
        $tolakKarya = StatusKaryaCitra::where('id', $id)->first();
        $isWatermarked = $request->query('isWatermarked');
        /**
         * condition: jika data tersedia
         * **/
        if ($data) {
            DB::beginTransaction();
            try {
                /**
                 * condition: jika data karya citra sebelumnya sudah ditolak dan ingin di status ingin diubah menjadi diterima hapus/ubah data status pada table status karya
                 * **/
                if ($tolakKarya) {
                    $tolakKarya->delete();
                }
                if (Storage::disk('karya_citra')->exists($data->karya_file)) {
                    // get file dari storage
                    $tempKarya = Image::make(storage_path('app/public/karya_citra/gambar/' . $data->karya_file));
                    if ($isWatermarked === 'true') {
                        $watermark = Image::make(public_path('/img/watermark.png'));
                        $watermark->opacity(100);
                        $watermark->resize($tempKarya->width() * 0.1, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $tempKarya->insert($watermark, 'bottom-right', 10, 10);
                        $filenameWatermark = 'karya-citra-approve-' . $data->karya_file; //GENERATE NAMA FILE YANG SUDAH BERISI WATERMARK
                        $tempKarya->save(storage_path('app/public/karya_citra/gambar/' . $filenameWatermark)); //DAN SIMPAN JUGA KE DALAM FOLDER YG SAMA
                        Storage::disk('karya_citra')->delete($data->karya_file);
                        $data->karya = $filenameWatermark;
                    } else {
                        $fileName = 'karya-citra-approve-' . $data->karya_file;
                        $tempKarya->save(storage_path('app/public/karya_citra/gambar/' . $fileName));
                        Storage::disk('karya_citra')->delete($data->karya_file);
                        $data->karya = $fileName;
                    }
                    $data->status = "Disetujui";
                    $data->save();
                    $this->notifikasi($data, "Selamat, karya kamu sudah divalidasi!");
                    $this->updateNotifikasi($data->id, $notifikasi->id);
                }

                if (Storage::disk('karya_citra_video')->exists($data->karya_file)) {
                    $data->status = "Disetujui";
                    $data->save();
                    $this->notifikasi($data, "Selamat, karya kamu sudah divalidasi!");
                    $this->updateNotifikasi($data->id, $notifikasi->id);
                }
                /**
                 * db action: hapus data kategori karya tulis
                 * **/
                DB::commit();
                return  response()->json([
                    'message' => 'Karya citra berhasil disetujui'
                ], 200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json([
                    'message' => 'Terjadi kesalahan server',
                    'error' => $th->getMessage()
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
    /** 
     * method : untuk menolak karya citra yang dimiliki oleh masing-masing siswa
     * **/
    public function tolakKarya(Request $request, $id)
    {
        /**
         * gate: hanya guru yang dapat melakukan request ke method ini
         * **/
        $this->authorize('is_guru');
        $data = KaryaCitra::where('id', $id)->first();
        /**
         * condition: jika data tersedia
         * **/
        if ($data) {
            DB::beginTransaction();
            $this->validate($request, [
                'keterangan' => 'required',
            ]);
            try {
                $data->status = "Ditolak";
                $status = new StatusKaryaCitra();
                $status->keterangan = $request->keterangan;
                $status->id_karya_citra = $data->id;
                $status->save();
                $data->save();
                DB::commit();
                return  response()->json([
                    'message' => 'Karya citra berhasil ditolak'
                ], 200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json([
                    'message' => 'Terjadi kesalahan server',
                    'error' => $th->getMessage()
                ], 500);
            }
        }
        /**
         * condition: jika data tidak tersedia
         * **/
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }

    /** 
     * method : untuk menampilkan karya siswa yang belum di validasi
     * **/
    public function listKaryaNotValidated()
    {
        $this->authorize('is_guru');
        $data = KaryaCitra::with('siswa')->where('status', 'Menunggu validasi')->get();
        if ($data) {
            return response()->json([
                'data' => $data,
                'message' => 'Karya yang belum di validasi'
            ]);
        } else {
            return response()->json([
                'message' => 'data tidak ada'
            ], 404);
        }
    }

    public function notifikasi($data, $desc)
    {

        $notifikasi = new Notifikasi();
        $notifikasi->user_id = auth()->user()->id;
        $notifikasi->id_karya_citra = $data->id;
        $notifikasi->id_siswa = $data->id_siswa;
        $notifikasi->status = $data->status;
        $notifikasi->notifikasi = 1;
        $notifikasi->desc = $desc;
        $notifikasi->save();
    }

    public function getNotifikasi()
    {
        $data = Notifikasi::where([
            'id_siswa' => auth()->user()->siswa[0]->id,
            'status' => 'Disetujui'
        ])->with('karya_citra', 'siswa')->orderBy('updated_at', 'desc')->get();

        $count = 0;
        foreach ($data as $item) {
            if ($item->notifikasi == 1) {
                $count++;
            }
        }

        $result = [
            'data' => $data,
            'count' => $count
        ];
        return response()->json($result);
    }

    public function updateNotifikasi($id_karya, $id)
    {
        $data = Notifikasi::where([
            'id_karya_citra' => $id_karya,
            'id' => $id
        ])->first();
        $data->notifikasi = 0;
        $data->save();
        return response()->json([
            'message' => 'sukses'
        ]);
    }

    public function hapusNotifikasiSiswa($id_siswa)
    {
        $notifikasi = Notifikasi::where([
            'id_siswa' => $id_siswa,
            'status' => 'Disetujui'
        ])->get();
        foreach ($notifikasi as $item) {
            $item->delete();
        }
        return response()->json([
            'message' => 'delete notifikasi'
        ]);
    }
}
