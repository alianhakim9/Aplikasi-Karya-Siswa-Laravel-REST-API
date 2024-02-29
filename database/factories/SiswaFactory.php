<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama_lengkap' => 'Rizky Ramadhan',
            'nisn' => '1234567890',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'foto_profil' => 'default.png',
            'ttl' => $this->faker->date('Y-m-d'),
            'alamat' => 'Jalan Kenangan No. 123, Kebayoran Lama, Jakarta Selatan, Jakarta, 12345',
            'user_id' => 3,
        ];
    }
}
