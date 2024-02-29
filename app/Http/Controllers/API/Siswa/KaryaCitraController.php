<?php

namespace App\Http\Controllers\API\Siswa;

use App\Http\Controllers\Controller;
use App\Models\KaryaCitra;
use App\Models\KaryaTulis;
use App\Models\KomentarKaryaCitra;
use App\Models\LikeKaryaCitra;
use App\Models\Notifikasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

/** 
 * controller : Controller API ini digunakan untuk mengelola karya citra
 * **/
class KaryaCitraController extends Controller
{
    /** 
     * method : untuk menampilkan karya citra yang sudah divalidasi oleh guru
     * **/
    public function index(Request $request)
    {
        $data = KaryaCitra::with('siswa', 'komentar', 'like')->where('status', 'Disetujui');
        if ($request->has('kategori')) {
            $data->where('nama_karya', 'like', '%' . $request->kategori . '%');
        }
        $data = $data->orderBy('created_at', 'desc')->paginate(12);
        return response()->json($data);
    }
    /** 
     * method : untuk mengunggah karya citra yang dimiliki siswa
     * **/
    public function store(Request $request)
    {
        $this->authorize('is_siswa');
        $siswa = $this->findSiswaByUserId();
        $this->validate($request, [
            'karya' => 'required|file|mimes:jpg,png,jpeg,mp4|max:20000',
            'nama_karya' => 'required|unique:karya_citra'
        ]);
        DB::beginTransaction();
        try {
            $karyaCitraBaru = new KaryaCitra();
            $karyaCitraBaru->nama_karya = $request->nama_karya;
            if ($request->hasFile('karya')) {
                $extension = $request->file('karya')->extension();
                $karyaName = 'karya-citra-' . date('dmyHis') . '.';
                if ($extension !== 'mp4') {
                    $photo = Image::make($request->file('karya'))
                        ->resize(1080, 1080, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    $photo->encode('webp', 100);
                    Storage::disk('karya_citra')->put($karyaName . 'webp', $photo);
                    $karyaCitraBaru->karya = $karyaName . 'webp';
                } else {
                    // Storage::disk('karya_citra_video')->put($karyaName, $request->file('karya'));
                    Storage::putFileAs('/public/karya_citra/video', $request->file('karya'), $karyaName . $extension);
                    $karyaCitraBaru->karya = $karyaName . $extension;
                }
            }
            $karyaCitraBaru->slug = Str::slug($request->nama_karya);
            $karyaCitraBaru->caption = $request->caption;
            $karyaCitraBaru->excerpt = Str::limit($request->caption, 50);
            $karyaCitraBaru->id_siswa = $siswa->id;
            $karyaCitraBaru->status = "Menunggu validasi";
            $karyaCitraBaru->kategori_karya_citra_id = $request->kategori_karya_citra_id;
            $karyaCitraBaru->save();
            $this->notifikasi($karyaCitraBaru, 'Karya ini menunggu validasi');
            DB::commit();
            return  response()->json([
                'data' => $karyaCitraBaru,
                'message' => 'Karya citra berhasil diunggah'
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            // jika terjadi kesalahan, hapus file yang sudah terupload
            if (Storage::disk('karya_citra')->exists($karyaName)) {
                Storage::disk('karya_citra')->delete($karyaName);
            }
            return response()->json([
                'message' => 'Terjadi kesalahan dalam proses upload karya citra',
                'error' => $th->getMessage()

            ], 500);
        }
    }
    /** 
     * method : untuk menampilkan karya citra yang dimiliki oleh masing-masing siswa
     * **/
    public function show($id_siswa)
    {
        $data = $this->findKaryaCitraByIdSiswa($id_siswa);
        if ($data) {
            return response()->json($data);
        }
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }
    /** 
     * method : untuk mengubah data karya citra yang dimiliki oleh masing-masing siswa
     * **/
    public function update(Request $request, $id)
    {
        $this->authorize('is_siswa');
        $data = $this->findKaryaCitraById($id);
        $siswa = $this->findSiswaByUserId();
        if ($siswa && $data) {
            DB::beginTransaction();
            try {
                $data->nama_karya = $request->nama_karya;
                if ($request->hasFile('karya')) {
                    $extension = $request->file('karya')->extension();
                    $karyaName = 'karya-citra-' . date('dmyHis') . '.';
                    if ($extension !== 'mp4') {
                        if (file_exists(storage_path('app/public/karya_citra/gambar/' . $data->karya_file))) {
                            Storage::disk('karya_citra')->delete($data->karya_file);
                        }
                        $photo = Image::make($request->file('karya'))
                            ->resize(1080, 1080, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        $photo->encode('png', 80);
                        Storage::disk('karya_citra')->put($karyaName . 'webp', $photo);
                    } else {
                        if (file_exists(storage_path('app/public/karya_citra/video/' . $data->karya_file))) {
                            unlink('storage/karya_citra/video/' . $data->karya_file);
                        }
                        Storage::putFileAs('/public/karya_citra/video', $request->file('karya'), $karyaName . $extension);
                    }
                    $data->karya = $karyaName;
                }
                $data->slug = Str::slug($request->nama_karya);
                $data->caption = $request->caption;
                $data->excerpt = Str::limit($request->caption, 50);
                $data->id_siswa = $siswa->id;
                $data->kategori_karya_citra_id = $request->kategori_karya_citra_id;
                if ($data->status === 'Ditolak') {
                    $data->status = 'Menunggu validasi';
                }
                $data->save();
                DB::commit();
                return  response()->json([
                    'data' => $data,
                    'message' => 'Karya citra berhasil diubah'
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
     * method : untuk menghapus karya citra yang dimiliki oleh masing-masing siswa
     * **/
    public function destroy($id)
    {
        $data = KaryaCitra::where('id', $id)->first();
        if ($data) {
            DB::beginTransaction();
            try {
                if ($data->format !== 'mp4') {
                    if (file_exists(storage_path('app/public/karya_citra/gambar/' . $data->karya_file))) {
                        unlink('storage/karya_citra/gambar/' . $data->karya_file);
                    }
                } else {
                    if (file_exists(storage_path('app/public/karya_citra/video/' . $data->karya_file))) {
                        unlink('storage/karya_citra/video/' . $data->karya_file);
                    }
                }
                $data->delete();
                DB::commit();
                return  response()->json([
                    'message' => 'Karya citra berhasil dihapus'
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
     * method : untuk menampilkan karya citra yang ditolak
     * **/
    public function listKaryaDitolak()
    {
        $this->authorize('is_siswa');
        $siswa = $this->findSiswaByUserId();
        $data = KaryaCitra::where(['status' => 'Ditolak', 'id_siswa' =>  $siswa->id])->with('statusKarya')->orderBy('created_at', 'desc')->paginate(12);
        return response()->json($data);
    }
    /** 
     * method : untuk memberikan like pada karya citra
     * **/
    public function like($id_karya_citra)
    {
        $this->authorize('is_siswa_or_guru');
        $karyaCitra = KaryaCitra::with('like')->where('id', $id_karya_citra)->withCount('like')->first();
        $currentLike = LikeKaryaCitra::where('user_id', auth()->user()->id)
            ->where('karya_citra_id', $id_karya_citra)
            ->first();
        if ($currentLike) {
            return response()->json([
                'message' => 'Like sudah diberikan'
            ], 409);
        } else {
            DB::beginTransaction();
            $likeKarya = new LikeKaryaCitra();
            $likeKarya->is_like = true;
            $likeKarya->user_id = auth()->user()->id;
            $likeKarya->karya_citra_id = $karyaCitra->id;
            $likeKarya->save();
            $karyaCitra->jumlah_like = $karyaCitra->like_count + 1;
            $karyaCitra->save();
            DB::commit();
            return  response()->json([
                'message' => 'Like berhasil diberikan'
            ], 200);
        }
    }
    /** 
     * method : untuk memberikan komentar pada karya citra
     * **/
    public function komentar(Request $request, $id_karya_citra)
    {
        $this->authorize('is_siswa_or_guru');
        $this->validate($request, [
            'komentar' => 'required'
        ]);
        $karyaCitra = $this->findKaryaCitraById($id_karya_citra);
        DB::beginTransaction();
        if ($karyaCitra) {
            try {
                $komentar = new KomentarKaryaCitra();
                $komentar->komentar = $request->komentar;
                $komentar->karya_citra_id = $karyaCitra->id;
                $komentar->user_id = auth()->user()->id;
                $komentar->save();
                DB::commit();
                return response()->json([
                    'message' => 'berhasil menambahkan komentar'
                ]);
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
            'message' => 'karya citra tidak ada'
        ], 404);
    }
    /** 
     * method : untuk menampilkan karya citra berdasarkan id karya citra
     * **/
    public function findKaryaCitraById($id)
    {
        return KaryaCitra::with('siswa', 'komentar')->where('id', $id)->first();
    }
    /** 
     * method : untuk menampilkan karya citra berdasarkan id siswa
     * **/
    public function findKaryaCitraByIdSiswa($id_siswa)
    {
        return KaryaCitra::with('siswa', 'komentar')->where('id_siswa', $id_siswa)->paginate(6);
    }
    /** 
     * method : untuk menampilkan siswa berdasarkan id user
     * **/
    public function findSiswaByUserId()
    {
        return Siswa::where('user_id', auth()->user()->id)->first();
    }

    public function countKaryaKu($id_siswa)
    {
        $this->authorize('is_siswa');
        return response()->json([
            'count_karya_tulis' => KaryaTulis::where('id_siswa', $id_siswa)->count(),
            'count_karya_citra' => KaryaCitra::where('id_siswa', $id_siswa)->count()
        ]);
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

    // get notifikasi untuk guru
    public function getNotifikasi()
    {
        $data = Notifikasi::where([
            'status' => 'Menunggu Validasi'
        ])->with('karya_citra', 'siswa')->orderBy('created_at', 'desc')->get();
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

    public function hapusNotifikasiGuru($id_notifikasi)
    {
        Notifikasi::where('id', $id_notifikasi)->delete();
        return response()->json([
            'message' => 'notifikasi berhasil dihapus'
        ]);
    }
}
