<?php

namespace Tests\Feature;

use Tests\TestCase;

class ManajemenUserAdminTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected $url = '/api/manajemen-akun-admin/';
    protected $auth = '/api/login';

    public function test_tambah_akun_admin()
    {
        // Perform login request to get token
        $loginResponse = $this->post($this->auth, [
            'email' => 'admin@gmail.com',
            'password' => 'password'
        ]);

        // Extract token from login response
        $token = $loginResponse->json('data.token');

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->post($this->url . 'tambah-admin/store', [
            'email' => 'adminbaruunit@gmail.com',
            'password' => 'password',
            'password_confirm' => 'password'
        ])
            ->assertStatus(200);
    }

    public function test_lihat_akun_admin()
    {
        // Perform login request to get token
        $loginResponse = $this->post($this->auth, [
            'email' => 'admin@gmail.com',
            'password' => 'password'
        ]);

        // Extract token from login response
        $token = $loginResponse->json('data.token');

        // Add token to headers of subsequent requests
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($this->url)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'email',
                        'password',
                        'role_id'
                    ]
                ]
            ]);
    }

    public function test_get_admin_by_id()
    {
        // Perform login request to get token
        $loginResponse = $this->post($this->auth, [
            'email' => 'admin@gmail.com',
            'password' => 'password'
        ]);

        // Extract token from login response
        $token = $loginResponse->json('data.token');
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($this->url . 'edit-admin/' . 1)
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'email',
                'password',
                'role_id'
            ]);
    }

    public function test_ubah_akun_admin()
    {
        // Perform login request to get token
        $loginResponse = $this->post($this->auth, [
            'email' => 'admin@gmail.com',
            'password' => 'password'
        ]);

        // Extract token from login response
        $token = $loginResponse->json('data.token');
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->put($this->url . 'edit-admin/update', [
            'id' => 5, // adminbaru
            'email' => 'adminupdate@gmail.com',
            'password' => 'password',
            'password_confirm' => 'password'
        ])
            ->assertStatus(200);
    }

    public function test_hapus_akun_admin()
    {
        $loginResponse = $this->post($this->auth, [
            'email' => 'admin@gmail.com',
            'password' => 'password'
        ]);

        // Extract token from login response
        $token = $loginResponse->json('data.token');
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->delete($this->url . 'hapus-admin/' . 5)
            ->assertStatus(200);
    }
}
