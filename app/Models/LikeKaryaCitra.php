<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeKaryaCitra extends Model
{
    use HasFactory;
    protected $table = 'like_karya_citra';
    protected $guarded = ['id'];

    public function karyaCitra()
    {
        return $this->belongsTo(KaryaCitra::class, 'karya_citra_id', 'id');
    }
}
