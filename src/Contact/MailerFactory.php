<?php

namespace App\Contact;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class MailerFactory
{
    public function __invoke()
    {
        $transport = Transport::fromDsn('smtp://localhost');
        return new Mailer($transport);
    }
}
