<?php
require_once('config/dbconfig.php');

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($connect->connect_error) {
    echo "Ошибка подключения: " . $connect->connect_error;
}
$connect->set_charset(DB_CHARSET);
