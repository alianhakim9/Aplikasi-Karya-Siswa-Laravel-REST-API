<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TimPPDBFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama_lengkap' => $this->faker->name(),
            'jabatan' => 'Kepala Tim PPDB',
            'user_id' => 4,
        ];
    }
}
