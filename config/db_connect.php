<?php

/* В файле config/db_config.php в виде объявленных констант содержаться параметры подключения к БД, а также объявлена по умолчанию date_default_timezone_set
Выглядят они следующим образом:

date_default_timezone_set('Europe/Moscow');

const DB_NAME = 'Имя БД';
const DB_USER = 'Логин';
const DB_PASSWORD = 'Пароль';
const DB_HOST = 'Имя хоста';
const DB_CHARSET = 'utf8';
*/

require_once('config/db_config.php');

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($connect->connect_error) {
    echo "Ошибка подключения: " . $connect->connect_error;
    exit();
}
$connect->set_charset(DB_CHARSET);
