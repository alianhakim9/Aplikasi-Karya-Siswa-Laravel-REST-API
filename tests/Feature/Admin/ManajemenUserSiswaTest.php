<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManajemenUserSiswaTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected $url = '/api/manajemen-siswa/';
    protected $auth = '/api/login';

    private function test_login()
    {
        // Perform login request to get token
        $loginResponse = $this->post($this->auth, [
            'email' => 'admin@gmail.com',
            'password' => 'password'
        ]);

        // Extract token from login response
        return $loginResponse->json('data.token');
    }

    public function test_tambah_akun_siswa()
    {
        $token = $this->test_login();
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($this->url . 'tambah-siswa/store', [
            'email' => 'siswaunit@gmail.com',
            'password' => 'password',
            'password_confirm' => 'password',
            'nama_lengkap' => 'Siswa Baru',
            'nisn' => '0987654321',
            'agama' => 'Islam',
            'ttl' => '2001-01-01',
            'alamat' => 'Jalan Raya No. 123, Kelurahan Cipedes, Kecamatan Cilengkrang, Kota Bandung, Provinsi Jawa Barat, Kode Pos 40135, Indonesia.'
        ])
            ->assertStatus(201);
    }

    public function test_lihat_akun_siswa()
    {
        $token = $this->test_login();
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($this->url)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => []
            ]);
    }

    public function test_get_akun_siswa()
    {
        $token = $this->test_login();
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($this->url . 'edit-siswa/' . 2)
            ->assertStatus(200);
    }

    // public function test_ubah_akun_siswa()
    // {
    //     $token = $this->test_login();
    //     $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $token
    //     ])->put($this->url . 'edit-siswa/update', [
    //         'email' => 'siswaupdateunit@gmail.com',
    //         'password' => 'password',
    //         'password_confirm' => 'password',
    //         'nama_lengkap' => 'Siswa Baru',
    //         'nisn' => '0987654321',
    //         'agama' => 'Islam',
    //         'ttl' => '2001-01-01',
    //         'alamat' => 'Jalan Raya No. 123, Kelurahan Cipedes, Kecamatan Cilengkrang, Kota Bandung, Provinsi Jawa Barat, Kode Pos 40135, Indonesia.'
    //     ])
    //         ->assertStatus(200);
    // }

    public function test_hapus_akun_siswa()
    {
        $token = $this->test_login();
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->delete($this->url . 'hapus-siswa/' . 2)
            ->assertStatus(200);
    }
}
