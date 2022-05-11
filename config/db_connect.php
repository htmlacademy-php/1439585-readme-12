<?php
require_once('config/db_config.php');

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($connect->connect_error) {
    echo "Ошибка подключения: " . $connect->connect_error;
    exit();
}
$connect->set_charset(DB_CHARSET);
