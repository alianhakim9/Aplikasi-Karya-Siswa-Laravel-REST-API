<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GuruFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama_lengkap' => 'Ruri Susanti',
            'nuptk' => $this->faker->randomNumber(),
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'foto_profil' => 'default.png',
            'ttl' => $this->faker->date('Y-m-d'),
            'alamat' => 'Jl. Ciwaruga Komplek Dipalaya No.4, Kecamatan Parongpong Kabupaten Bandung Barat, 40559',
            'user_id' => 2,
            'gelar' => 'M.Pd,Gr',
            'jabatan' => 'Kepala Sekolah'
        ];
    }
}
