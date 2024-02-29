<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $user = new User();
            $user->email = strtolower($row['email']);
            $user->password = Hash::make($row['password']);
            $user->role_id = 2;
            $user->save();

            $guru = new Guru();
            $guru->nama_lengkap = ucfirst($row['nama_lengkap']);
            $guru->nuptk = strtoupper($row['nuptk']);
            $guru->jenis_kelamin = strtoupper($row['jenis_kelamin']);
            $guru->agama = ucfirst($row['agama']);
            $guru->foto_profil = 'default.png';
            $guru->ttl = Carbon::createFromTimestampMs($row['ttl'])->format('Y-m-d');
            $guru->alamat = strtoupper($row['alamat']);
            $guru->gelar = strtoupper($row['gelar']);
            $guru->jabatan = ucfirst($row['jabatan']);
            $guru->user_id = $user->id;
            $guru->save();
        }
    }
}
