<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\KaryaTulis;
use App\Models\KategoriKaryaAudioVisual;
use App\Models\KategoriKaryaCitra;
use App\Models\KategoriKaryaTulis;
use App\Models\Siswa;
use App\Models\TimPPDB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->roleSeeder();
        KategoriKaryaCitra::factory(1)->create();
        KategoriKaryaTulis::factory(1)->create();
        Siswa::factory(1)->create();
        TimPPDB::factory(1)->create();
        Guru::factory(1)->create();
    }

    public function roleSeeder()
    {
        DB::table('role')->insert(
            ['nama_role' => 'Admin', 'created_at' => Carbon::now()],
        );
        DB::table('role')->insert(
            ['nama_role' => 'Guru', 'created_at' => Carbon::now()],
        );
        DB::table('role')->insert(
            ['nama_role' => 'Siswa', 'created_at' => Carbon::now()],
        );
        DB::table('role')->insert(
            ['nama_role' => 'Tim PPDB', 'created_at' => Carbon::now()],
        );
        DB::table('users')->insert([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 1
        ]);
        DB::table('users')->insert([
            'email' => 'guru@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 2
        ]);
        DB::table('users')->insert([
            'email' => 'siswa@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 3
        ]);
        DB::table('users')->insert([
            'email' => 'timppdb@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 4
        ]);
    }
}
