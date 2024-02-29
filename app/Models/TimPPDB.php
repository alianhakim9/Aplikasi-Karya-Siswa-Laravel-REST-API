<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimPPDB extends Model
{
    use HasFactory;
    protected $table = 'tim_ppdb';
    protected $guarded = ['id'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,  'user_id');
    }

    public function promosi()
    {
        return $this->hasMany(Promosi::class, 'tim_ppdb_id');
    }
}
