<?php

use Symfony\Component\Mailer\Transport;

require_once('vendor/autoload.php');
require_once('config/smtp_config.php');

$mailerDsn = 'smtp://' . USERNAME . ':' . PASS . '@' . SERVER . ':' . PORT . '?encryption=' . ENCRYPTION . '&auth_mode=' . AUTH_MODE;
$transport = Transport::fromDsn($mailerDsn);
