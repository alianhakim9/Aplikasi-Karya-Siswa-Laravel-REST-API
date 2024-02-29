<?php

namespace App\Imports;

use App\Models\TimPPDB;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TimPPDBImport implements ToCollection, WithHeadingRow
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
            $user->role_id = 4;
            $user->save();

            $siswa = new TimPPDB();
            $siswa->nama_lengkap = ucfirst($row['nama_lengkap']);
            $siswa->jabatan = strtoupper($row['jabatan']);
            $siswa->user_id = $user->id;
            $siswa->save();
        }
    }
}
