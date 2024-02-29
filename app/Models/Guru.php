<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Guru extends Model
{
    use HasFactory;
    protected $table = 'guru';
    protected $guarded = ['id'];
    protected $appends = array('foto_profil_file');


    public function user()
    {
        return $this->belongsTo(User::class,  'user_id');
    }

    public function getFotoProfilAttribute()
    {
        $path = Storage::disk('foto_profil_guru')->url($this->attributes['foto_profil']);
        return $path;
    }

    public function getFotoProfilFileAttribute()
    {
        return $this->attributes['foto_profil'];
    }
}
