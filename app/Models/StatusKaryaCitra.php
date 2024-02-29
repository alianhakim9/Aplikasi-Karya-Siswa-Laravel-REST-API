<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusKaryaCitra extends Model
{
    use HasFactory;
    protected $table = 'status_karya_citra';
    protected $guarded = ['id'];

    public function karyaCitra()
    {
        return $this->hasOne(KaryaCitra::class, 'id_karya_citra');
    }
}
