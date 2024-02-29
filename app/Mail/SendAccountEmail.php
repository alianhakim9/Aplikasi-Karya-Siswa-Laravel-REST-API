<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAccountEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $akun;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($akun)
    {
        $this->akun = $akun;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('aplikasikaryasiswa@gmail.com')
            ->subject('Akun otentikasi aplikasi karya siswa')
            ->view('email', ['akun' => $this->akun]);
    }
}
