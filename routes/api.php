<?php

use App\Http\Controllers\Admin\ManajemenAdminController;
use App\Http\Controllers\Admin\ManajemenGuruController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\ManajemenTimPPDBController;
use App\Http\Controllers\API\Guru\GuruController;
use App\Http\Controllers\API\Guru\KategoriKaryaCitraController;
use App\Http\Controllers\API\Guru\KategoriKaryaTulisController;
use App\Http\Controllers\API\Guru\ValidasiKaryaCitraController;
use App\Http\Controllers\API\Siswa\KaryaCitraController;
use App\Http\Controllers\API\Siswa\KaryaTulisController;
use App\Http\Controllers\API\Siswa\SiswaController;
use App\Http\Controllers\API\TimPPDB\PromosiController;
use App\Http\Controllers\API\TimPPDB\TimppdbController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;

/**
 * route: digunakan untuk route API login
 * **/
Route::post('login', [UsersController::class, 'login']);
Route::post('password/email', [AuthController::class, 'sendResetLink']);
Route::post('password/reset', [AuthController::class, 'updatePassword']);
/**
 * route: middleware sanctum
 * **/
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/current-user', [UsersController::class, 'currentUser']);
    /**
     * route: digunakan untuk route group API siswa
     * **/
    Route::prefix('siswa')->group(function () {
        Route::get('profil', [SiswaController::class, 'show']);
        Route::put('profil/update/{id}', [SiswaController::class, 'update']);
    });
    /**
     * route: digunakan untuk route group API guru
     * **/
    Route::prefix('guru')->group(function () {
        Route::get('profil/', [GuruController::class, 'show']);
        Route::put('profil/update/{id}', [GuruController::class, 'update']);
    });
    /**
     * route: digunakan untuk route group API tim ppdb
     * **/
    Route::prefix('tim-ppdb')->group(function () {
        Route::get('profil/', [TimppdbController::class, 'show']);
        Route::put('profil/update/{id}', [TimppdbController::class, 'update']);
    });
    /**
     * route: digunakan untuk route group API karya
     * **/
    Route::prefix('karya')->group(function () {
        // karya citra
        Route::prefix('citra')->group(function () {
            // kategori
            Route::prefix('kategori')->group(function () {
                Route::get('all', [KategoriKaryaCitraController::class, 'index'])->name('kategori-karya-citra');
                Route::post('store', [KategoriKaryaCitraController::class, 'store'])->name('tambah-kategori-karya-citra');
                Route::delete('delete/{id}', [KategoriKaryaCitraController::class, 'destroy'])->name('hapus-kategori-karya-citra');
            });
            // karya
            Route::get('all', [KaryaCitraController::class, 'index'])->name('lihat-karya-citra');
            Route::get('detail/{id}', [KaryaCitraController::class, 'findKaryaCitraById']);
            Route::post('store', [KaryaCitraController::class, 'store'])->name('store-karya-citra');
            Route::put('update/{id}', [KaryaCitraController::class, 'update'])->name('update-karya-citra');
            Route::delete('delete/{id}', [KaryaCitraController::class, 'destroy'])->name('hapus-karya-citra');
            Route::get('owner/{id_siswa}', [KaryaCitraController::class, 'show'])->name('lihat-karya-by-siswa');
            Route::get('ditolak', [KaryaCitraController::class, 'listKaryaDitolak']);
            // validasi karya
            Route::prefix('validasi')->group(function () {
                Route::get('all', [ValidasiKaryaCitraController::class, 'listKaryaNotValidated'])->name('list-not-validated-karya');
                Route::post('terima/{id}', [ValidasiKaryaCitraController::class, 'terimaKarya'])->name('validasi-karya');
                Route::post('tolak/{id}', [ValidasiKaryaCitraController::class, 'tolakKarya'])->name('tolak-karya');
            });
            // like dan komentar
            Route::post('like/{id_karya_citra}', [KaryaCitraController::class, 'like'])->name('like-karya-citra');
            Route::post('komentar/{id_karya_citra}', [KaryaCitraController::class, 'komentar'])->name('komentar-karya-citra');
        });
        // karya tulis
        Route::prefix('tulis')->group(function () {
            // kategori karya tulis
            Route::prefix('kategori')->group(function () {
                Route::get('all', [KategoriKaryaTulisController::class, 'index'])->name('kategori-karya-tulis');
                Route::post('store', [KategoriKaryaTulisController::class, 'store'])->name('tambah-kategori-karya-tulis');
                Route::delete('delete/{id}', [KategoriKaryaTulisController::class, 'destroy'])->name('hapus-kategori-karya-tulis');
            });
            // karya
            Route::get('all', [KaryaTulisController::class, 'index'])->name('lihat-karya-tulis-api');
            Route::post('store', [KaryaTulisController::class, 'store'])->name('store-karya-tulis-api');
            Route::put('update/{id}', [KaryaTulisController::class, 'update'])->name('update-karya-tulis-api');
            Route::delete('delete/{id}', [KaryaTulisController::class, 'destroy'])->name('hapus-karya-tulis-api');
            Route::get('owner/{id_siswa}', [KaryaTulisController::class, 'show'])->name('lihat-karya-by-siswa-api');
            // like dan komentar
            Route::post('like/{id_karya_citra}', [KaryaTulisController::class, 'like'])->name('like-karya-tulis');
            Route::post('komentar/{id_karya_citra}', [KaryaTulisController::class, 'komentar'])->name('komentar-karya-tulis');
            Route::get('detail/{id_karya_tulis}', [KaryaTulisController::class, 'findKaryaTulisById']);
        });
    });
    /**
     * route: digunakan untuk route group API promosi
     * **/
    Route::prefix('promosi')->group(function () {
        // ubah data promosi
        Route::post('store', [PromosiController::class, 'store'])->name('store-promosi-api');
        // menampilkan semua data promosi
        Route::get('all', [PromosiController::class, 'index']);
        Route::get('active', [PromosiController::class, 'active']);
        // update data promosi
        Route::put('update/{id}', [PromosiController::class, 'update'])->name('update-promosi-api');
        // hapus data promosi
        Route::delete('delete/{id}', [PromosiController::class, 'destroy'])->name('delete-promosi-api');
        // tampil data promosi berdasarkan id
        Route::get('{id_promosi}', [PromosiController::class, 'show'])->name('show-tim-ppdb-api');
        // tampil data promosi berdasarkan id tim ppdb
        Route::get('owner/{id_tim_ppdb}', [PromosiController::class, 'byTimPPDB'])->name('by-tim-ppdb-api');
    });
    Route::get('count-karya-ku/{id_siswa}', [KaryaCitraController::class, 'countKaryaku']);
    Route::get('notifikasi/', [KaryaCitraController::class, 'getNotifikasi']);
    Route::put('notifikasi/update/{id_karya}/{id}', [KaryaCitraController::class, 'updateNotifikasi']);
    Route::get('notifikasi/siswa', [ValidasiKaryaCitraController::class, 'getNotifikasi']);
    Route::delete('notifikasi/siswa/hapus/{id_siswa}', [ValidasiKaryaCitraController::class, 'hapusNotifikasiSiswa']);
    Route::delete('notifikasi/guru/hapus/{id_notifikasi}', [KaryaCitraController::class, 'hapusNotifikasiGuru']);
    Route::prefix('manajemen-akun-admin')->group(function () {
        Route::get('/', [ManajemenAdminController::class, 'index'])->name('dashboard-akun-admin');
        Route::post('tambah-admin/store', [ManajemenAdminController::class, 'store'])->name('store-admin');
        Route::get('edit-admin/{id}', [ManajemenAdminController::class, 'edit'])->name('edit-admin');
        Route::put('edit-admin/update', [ManajemenAdminController::class, 'update'])->name('update-admin');
        Route::delete('hapus-admin/{id}', [ManajemenAdminController::class, 'destroy'])->name('hapus-admin');
        Route::post('import', [ManajemenAdminController::class, 'import']);
    });
    Route::prefix('manajemen-siswa')->group(function () {
        Route::get('/', [ManajemenSiswaController::class, 'index'])->name('dashboard-akun-siswa');
        Route::post('tambah-siswa/store', [ManajemenSiswaController::class, 'store'])->name('store-siswa');
        Route::get('edit-siswa/{nisn}', [ManajemenSiswaController::class, 'edit'])->name('edit-siswa');
        Route::put('edit-siswa/update/', [ManajemenSiswaController::class, 'update'])->name('update-siswa');
        Route::delete('hapus-siswa/{id}', [ManajemenSiswaController::class, 'destroy'])->name('hapus-siswa');
        Route::post('import', [ManajemenSiswaController::class, 'import']);
    });
    Route::prefix('manajemen-guru')->group(function () {
        Route::get('/', [ManajemenGuruController::class, 'index'])->name('dashboard-akun-guru');
        Route::post('tambah-guru/store', [ManajemenGuruController::class, 'store'])->name('store-guru');
        Route::get('edit-guru/{nuptk}', [ManajemenGuruController::class, 'edit'])->name('edit-guru');
        Route::put('edit-guru/update', [ManajemenGuruController::class, 'update'])->name('update-guru');
        Route::delete('hapus-guru/{id}', [ManajemenGuruController::class, 'destroy'])->name('hapus-guru');
        Route::post('import', [ManajemenGuruController::class, 'import']);
    });
    Route::prefix('manajemen-tim')->group(function () {
        Route::get('/', [ManajemenTimPPDBController::class, 'index'])->name('dashboard-akun-tim-ppdb');
        Route::post('tambah-tim/store', [ManajemenTimPPDBController::class, 'store'])->name('store-tim-ppdb');
        Route::get('edit-tim/{id}', [ManajemenTimPPDBController::class, 'edit'])->name('edit-tim-ppdb');
        Route::put('edit-tim/update', [ManajemenTimPPDBController::class, 'update'])->name('update-tim-ppdb');
        Route::delete('hapus-tim/{id}', [ManajemenTimPPDBController::class, 'destroy'])->name('hapus-tim-ppdb');
        Route::post('import', [ManajemenTimPPDBController::class, 'import']);
    });
    Route::get('count', [ManajemenAdminController::class, 'count']);
    Route::post('kirim/akun', [EmailController::class, 'store']);
});

// client route
Route::get('/', [ClientController::class, 'index']);
Route::group(['prefix' => 'karya-visual', 'namespace' => 'KaryaVisual'], function () {
    Route::get('all', [ClientController::class, 'karyaCitraAll']);
    Route::get('{slug}', [ClientController::class, 'detailKaryaCitra']);
});
Route::group(['prefix' => 'karya-tulis', 'namespace' => 'karyaTulis'], function () {
    Route::get('all', [ClientController::class, 'karyaTulisAll']);
    Route::get('{slug}', [ClientController::class, 'detailKaryaTulis']);
});
// kategori
Route::prefix('kategori/karya-citra')->group(function () {
    Route::get('all', [KategoriKaryaCitraController::class, 'index'])->name('kategori-karya-citra');
    Route::post('store', [KategoriKaryaCitraController::class, 'store'])->name('tambah-kategori-karya-citra');
    Route::delete('delete/{id}', [KategoriKaryaCitraController::class, 'destroy'])->name('hapus-kategori-karya-citra');
});
// kategori karya tulis
Route::prefix('kategori/karya-tulis')->group(function () {
    Route::get('all', [KategoriKaryaTulisController::class, 'index'])->name('kategori-karya-tulis');
    Route::post('store', [KategoriKaryaTulisController::class, 'store'])->name('tambah-kategori-karya-tulis');
    Route::delete('delete/{id}', [KategoriKaryaTulisController::class, 'destroy'])->name('hapus-kategori-karya-tulis');
});
Route::get('promosi/all/client', [ClientController::class, 'getAllPromosi']);
