<?php

namespace App\Contracts;

use Illuminate\Contracts\Mail\Mailable;

interface MailerFactoryInterface
{    
    public function make(string $key, array $parameters = []): Mailable;
}
