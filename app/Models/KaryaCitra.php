<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class KaryaCitra extends Model
{
    use HasFactory;
    protected $table = 'karya_citra';
    protected $guarded = ['id'];
    protected $appends = array('format', 'karya_file', 'nama_kategori');

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }

    public function kategoriKaryaCitra()
    {
        return $this->belongsTo(KategoriKaryaCitra::class, 'kategori_karya_citra_id', 'id');
    }

    public function getNamaKategoriAttribute()
    {
        return $this->kategoriKaryaCitra->nama_kategori;
    }

    public function statusKarya()
    {
        return $this->hasOne(StatusKaryaCitra::class, 'id_karya_citra');
    }

    public function like()
    {
        return $this->hasMany(LikeKaryaCitra::class, 'karya_citra_id');
    }

    public function komentar()
    {
        return $this->hasMany(KomentarKaryaCitra::class, 'karya_citra_id');
    }

    public function getKaryaAttribute()
    {
        $path = Storage::disk('karya_citra')->url($this->attributes['karya']);
        $pathVideo = Storage::disk('karya_citra_video')->url($this->attributes['karya']);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'webp') {
            return $path;
        } else {
            return $pathVideo;
        }
    }

    public function getFormatAttribute()
    {
        $path = Storage::disk('karya_citra')->url($this->attributes['karya']);
        $pathVideo = Storage::disk('karya_citra_video')->url($this->attributes['karya']);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg') {
            return $ext;
        } else {
            return $ext;
        }
    }

    public function getKaryaFileAttribute()
    {
        return $this->attributes['karya'];
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d M Y H:i:s');
    }
}
