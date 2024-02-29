<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Siswa extends Model
{
    use HasFactory;
    protected $table = 'siswa';
    protected $guarded = ['id'];
    protected $appends = array('foto_profil_file');


    public function user()
    {
        return $this->belongsTo(User::class,  'user_id');
    }

    public function karyaCitra()
    {
        return $this->hasMany(KaryaCitra::class, 'id_siswa');
    }

    public function karyaTulis()
    {
        return $this->hasMany(KaryaTulis::class, 'id_siswa');
    }

    public function getFotoProfilAttribute()
    {
        $path = Storage::disk('foto_profil_siswa')->url($this->attributes['foto_profil']);
        return $path;
    }

    public function getFotoProfilFileAttribute()
    {
        return $this->attributes['foto_profil'];
    }
}
