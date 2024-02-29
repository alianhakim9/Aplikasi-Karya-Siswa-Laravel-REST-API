<?php

namespace App\Http\Controllers\API\Siswa;

use App\Http\Controllers\Controller;
use App\Models\KaryaTulis;
use App\Models\KomentarKaryaTulis;
use App\Models\LikeKaryaTulis;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class KaryaTulisController extends Controller
{
    /** 
     * method : untuk menampilkan karya tulis yang sudah divalidasi oleh guru
     * **/
    public function index(Request $request)
    {
        $data = KaryaTulis::with('siswa', 'komentar', 'like');
        if ($request->has('kategori')) {
            $data->where('judul_karya', 'like', '%' . $request->kategori . '%');
        }
        $data = $data->paginate(12);
        if ($data) {
            return response()->json($data);
        }
    }
    /** 
     * method : untuk mengunggah karya tulis yang dimiliki siswa
     * **/
    public function store(Request $request)
    {
        $this->authorize('is_siswa');
        $siswa = $this->findSiswaByUserId();
        $this->validate($request, [
            'judul_karya' => 'required|unique:karya_tulis',
            'konten_karya' => 'required',
            'kategori_karya_tulis_id' => 'required'
        ]);
        DB::beginTransaction();
        if ($siswa) {
            try {
                $data = new KaryaTulis();
                $data->judul_karya = $request->judul_karya;
                $data->konten_karya = htmlspecialchars($request->konten_karya);
                $data->slug = Str::slug($request->judul_karya);
                $data->excerpt = Str::limit(htmlspecialchars($request->konten_karya), 50);
                $data->id_siswa = $siswa->id;
                $data->kategori_karya_tulis_id = $request->kategori_karya_tulis_id;
                $data->sumber = $request->sumber;
                $data->save();
                DB::commit();
                return  response()->json([
                    'data' => $data,
                    'message' => 'Karya tulis berhasil diunggah'
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
     * method : untuk menampilkan karya tulis yang dimiliki oleh masing-masing siswa
     * **/
    public function show($id_siswa)
    {
        $data = $this->findKaryatulisByIdSiswa($id_siswa);
        if ($data) {
            return response()->json(
                $data
            );
        }
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }
    /** 
     * method : untuk mengubah data karya tulis yang dimiliki oleh masing-masing siswa
     * **/
    public function update(Request $request, $id)
    {
        $this->authorize('is_siswa');
        $data = $this->findKaryatulisById($id);
        $siswa = $this->findSiswaByUserId();
        if ($siswa && $data) {
            DB::beginTransaction();
            try {
                $data->judul_karya = $request->judul_karya;
                $data->konten_karya = $request->konten_karya;
                $data->slug = Str::slug($request->judul_karya);
                $data->excerpt = Str::limit($request->konten_karya, 50);
                $data->id_siswa = $siswa->id;
                $data->kategori_karya_tulis_id = $request->kategori_karya_tulis_id;
                $data->sumber = $request->sumber;
                $data->save();
                DB::commit();
                return  response()->json([
                    'data' => $data,
                    'message' => 'Karya tulis berhasil diubah'
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
     * method : untuk menghapus karya tulis yang dimiliki oleh masing-masing siswa
     * **/
    public function destroy($id)
    {
        $data = $this->findKaryatulisById($id);
        DB::beginTransaction();
        if ($data) {
            try {

                $data->delete();
                DB::commit();
                return  response()->json([
                    'message' => 'Karya tulis berhasil dihapus'
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
     * method : untuk memberikan like pada karya tulis
     * **/
    public function like($id_karya_tulis)
    {
        $this->authorize('is_siswa_or_guru');
        $karyaTulis = KaryaTulis::with('like')->where('id', $id_karya_tulis)->withCount('like')->first();
        $currentLike = LikeKaryaTulis::where('user_id', auth()->user()->id)
            ->where('karya_tulis_id', $id_karya_tulis)
            ->first();
        if ($currentLike) {
            return response()->json([
                'message' => 'Like sudah diberikan'
            ], 409);
        } else {
            DB::beginTransaction();
            $likeKarya = new LikeKaryaTulis();
            $likeKarya->is_like = true;
            $likeKarya->user_id = auth()->user()->id;
            $likeKarya->karya_tulis_id = $karyaTulis->id;
            $karyaTulis->jumlah_like = $karyaTulis->like_count + 1;
            $karyaTulis->save();
            $likeKarya->save();
            DB::commit();
            return  response()->json([
                'message' => 'Like berhasil diberikan'
            ], 200);
        }
    }
    /** 
     * method : untuk memberikan komentar pada karya tulis
     * **/
    public function komentar(Request $request, $id_karya_tulis)
    {
        $this->authorize('is_siswa_or_guru');
        Validator::make($request->all(), [
            'komentar' => 'required',
            'karya_tulis_id' => 'required',
            'user_id' => 'required'
        ]);
        $karyaTulis = $this->findKaryatulisById($id_karya_tulis);
        if ($karyaTulis) {
            DB::beginTransaction();
            try {
                $komentar = new KomentarKaryaTulis();
                $komentar->komentar = $request->komentar;
                $komentar->karya_tulis_id = $karyaTulis->id;
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
        return response()->json([
            'message' => 'data tidak ada'
        ], 404);
    }
    /** 
     * method : untuk menampilkan karya tulis berdasarkan id karya tulis
     * **/
    public function findKaryatulisById($id)
    {
        return KaryaTulis::with('komentar', 'siswa')->where('id', $id)->first();
    }
    /** 
     * method : untuk menampilkan karya tulis berdasarkan id siswa
     * **/
    public function findKaryatulisByIdSiswa($id_siswa)
    {
        return KaryaTulis::with('siswa', 'komentar', 'like')->where('id_siswa', $id_siswa)->paginate(6);
    }
    /** 
     * method : untuk menampilkan siswa berdasarkan id user
     * **/
    public function findSiswaByUserId()
    {
        return Siswa::where('user_id', auth()->user()->id)->first();
    }
}
