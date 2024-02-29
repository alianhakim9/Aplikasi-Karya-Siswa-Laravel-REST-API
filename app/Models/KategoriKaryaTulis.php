<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKaryaTulis extends Model
{
    use HasFactory;
    protected $table = 'kategori_karya_tulis';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function karyaTulis()
    {
        return $this->hasMany(karyaTulis::class, 'kategori_karya_tulis_id', 'id');
    }
}
