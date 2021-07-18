<?php

require('vendor/autoload.php');

$transport = (new Swift_SmtpTransport('mailtrap.io', 25))
    ->setUsername('keks@phpdemo.ru')
    ->setPassword('htmlacademy')
;

$mailer = new Swift_Mailer($transport);
