<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKaryaCitra extends Model
{
    use HasFactory;
    protected $table = 'kategori_karya_citra';
    protected $guarded = ['id'];

    public function karyaCitra()
    {
        return $this->hasMany(KaryaCitra::class, 'kategori_karya_citra_id', 'id');
    }
}
