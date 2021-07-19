<?php

require('vendor/autoload.php');

$transport = (new Swift_SmtpTransport('smtp.mail.ru', 465))
    ->setUsername('readme1162533@mail.ru')
    ->setPassword('22tYrpRIupM^')
    ->setEncryption('SSL')
;

$mailer = new Swift_Mailer($transport);
