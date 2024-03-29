<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

$errorFields = [];
$userData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $isEmptyFields = validateEmptyField($_POST, ['email' => "Email. ", 'password' => "Пароль."]);

    // Строкой выше есть проверка validateEmptyField, в которой проверяется, не пусты ли указанные используемые далее поля в массиве $_POST
    if (empty($isEmptyFields) && validateEmail($_POST['email'])) {

        if (checkEmailExists($connect, $_POST['email'])) {
            $userData = getUserAuthorizationData($connect, $_POST['email']);
            if (!password_verify($_POST['password'], $userData['password'])) {
                $errorFields['authorization'] = 'Вы ввели неверный email/пароль';
            }
        } else {
            $errorFields['authorization'] = 'Вы ввели неверный email/пароль';
        }
    } else {
        $errorFields['authorization'] = 'Вы ввели неверный email/пароль';
    }

    if (empty($errorFields)) {
        $_SESSION['user']['id'] = $userData['id'];
        $_SESSION['user']['login'] = $userData['login'];
        $_SESSION['user']['avatar'] = $userData['avatar'] ?? '';
    }
}

if (!empty($_SESSION['user'])) {
    redirectOnPage('feed.php');
} else {
    $authorizationPage = include_template('main-page.php',
        ['titleName' => 'readme: блог, каким он должен быть', 'errorFields' => $errorFields]);
    print_r($authorizationPage);
}
