<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToCollection, WithHeadingRow
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
            $user->role_id = 3;
            $user->save();

            $siswa = new Siswa();
            $siswa->nama_lengkap = ucfirst($row['nama_lengkap']);
            $siswa->nisn = strtoupper($row['nisn']);
            $siswa->jenis_kelamin = strtoupper($row['jenis_kelamin']);
            $siswa->agama = ucfirst($row['agama']);
            $siswa->ttl = Carbon::createFromTimestampMs(strtolower($row['ttl']))->format('Y-m-d');
            $siswa->alamat = strtoupper($row['alamat']);
            $siswa->user_id = $user->id;
            $siswa->foto_profil = 'default.png';
            $siswa->save();
        }
    }
}
