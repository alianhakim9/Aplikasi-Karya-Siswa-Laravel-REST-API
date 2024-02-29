<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{
    private $loginUrl = 'api/login';

    public function test_login_success()
    {
        $value = [
            'email' => 'admin@gmail.com',
            'password' => 'password'
        ];

        $this->post($this->loginUrl, $value)->assertStatus(200);
    }

    public function test_login_failed()
    {
        $value = [
            'email' => 'emailsalah@gmail.com',
            'password' => 'passwordsalah'
        ];

        $this->post($this->loginUrl, $value)->assertStatus(401);
    }

    public function test_login_email_invalid()
    {
        $value = [
            'email' => 'email@invalid.com',
            'password' => 'password'
        ];
        $this->post($this->loginUrl, $value)->assertStatus(302);
    }

    public function test_login_password_invalid()
    {
        $value = [
            'email' => 'admin@gmail.com',
            'password' => '123'
        ];
        $this->post($this->loginUrl, $value)->assertStatus(401);
    }

    public function test_search_email()
    {
        $value = [
            'email' => 'admin@gmail.com',
        ];
        $this->post('api/password/email', $value)->assertStatus(200);
    }

    public function test_not_available_email()
    {
        $value = [
            'email' => 'notvalid@gmail.com',
        ];
        $this->post('api/password/email', $value)->assertStatus(500);
    }

    // public function test_reset_password()
    // {
    //     $value = [
    //         'email' => 'alianhakim9@gmail.com',
    //         'password' => 'passwordbaru',
    //         'token' =
    //     ];
    //     $this->post('api/password/reset', $value)->assertStatus(200);
    // }
}
