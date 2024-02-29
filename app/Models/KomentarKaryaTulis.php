<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomentarKaryaTulis extends Model
{
    use HasFactory;
    protected $table = 'komentar_karya_tulis';
    protected $guarded = ['id'];
    protected $with = ['user', 'siswa', 'guru'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }

    public function karyaTulis()
    {
        return $this->belongsTo(KaryaTulis::class, 'karya_tulis_id', 'id');
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id', 'user_id');
    }

    public function guru()
    {
        return $this->hasOne(Guru::class, 'user_id', 'user_id');
    }
}
