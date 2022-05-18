<?php

/* В файле config/smtp_config.php в виде объявленных констант содержаться параметры подключения к SMTP серверу, адрес отправителя.
В качестве SMTP параметров взяты параметры от своего личного почтового ящика.
Выглядят они следующим образом:

const SERVER = 'SMTP-сервер';
const USERNAME = 'E-mail сайта';
const SENDER_ADDRESS = 'Отправитель письма';
const PASS = 'Пароль для SMTP: пароль от почтового ящика';
const PORT = 'Порт SMTP-сервера';
const ENCRYPTION = 'Защита SMTP';
const AUTH_MODE = 'Авторизация на SMTP-сервере';
*/

use Symfony\Component\Mailer\Transport;

require_once('vendor/autoload.php');
require_once('config/smtp_config.php');

$mailerDsn = 'smtp://' . USERNAME . ':' . PASS . '@' . SERVER . ':' . PORT . '?encryption=' . ENCRYPTION . '&auth_mode=' . AUTH_MODE;
$transport = Transport::fromDsn($mailerDsn);
