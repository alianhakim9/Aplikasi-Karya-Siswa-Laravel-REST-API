<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeKaryaTulis extends Model
{
    use HasFactory;
    protected $table = 'like_karya_tulis';
    protected $guarded = ['id'];

    public function karyaTulis()
    {
        return $this->belongsTo(KaryaTulis::class, 'karya_tulis_id', 'id');
    }
}
