<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Promosi extends Model
{
    use HasFactory;
    protected $table = 'promosi';
    protected $guarded = ['id'];
    protected $appends = array('gambar_file');


    public function timPPDB()
    {
        return $this->belongsTo(TimPPDB::class, 'tim_ppdb_id');
    }

    public function getGambarAttribute()
    {
        $path = Storage::disk('promosi')->url($this->attributes['gambar']);
        return $path;
    }

    public function getGambarFileAttribute()
    {
        return $this->attributes['gambar'];
    }
}
