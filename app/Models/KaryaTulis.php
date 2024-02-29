<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class KaryaTulis extends Model
{
    use HasFactory;
    protected $table = 'karya_tulis';
    protected $guarded = ['id'];
    protected $appends = array('nama_kategori');

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }

    public function kategoriKaryaTulis()
    {
        return $this->belongsTo(KategoriKaryaTulis::class, 'kategori_karya_tulis_id', 'id');
    }

    public function getNamaKategoriAttribute()
    {
        return $this->kategoriKaryaTulis->nama_kategori;
    }

    public function like()
    {
        return $this->hasMany(LikeKaryaTulis::class, 'karya_tulis_id');
    }

    public function komentar()
    {
        return $this->hasMany(KomentarKaryaTulis::class, 'karya_tulis_id');
    }
    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }

    public function getKontenKaryaAttribute()
    {
        return htmlspecialchars_decode($this->attributes['konten_karya']);
    }

    public function getExcerptAttribute()
    {
        $output = strip_tags($this->attributes['excerpt']);
        $output = htmlspecialchars_decode($output);
        $output = preg_replace('/\s+/', ' ', strip_tags($output));
        return $output;
    }
}
