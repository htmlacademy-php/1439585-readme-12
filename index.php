<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

$errorFields = [];
$userData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Валидация на заполненность полей*/
    $isEmptyFields = validateEmptyField($_POST, ['email' => "Email. ", 'password' => "Пароль."]);

    /* Проверяем, что пользователь ввел не пустые данные и email валидный*/
    if (empty($isEmptyFields) && validateEmail($_POST['email'])) {

        /* Проверяем существование пользователя в БД; если есть, получаем по нему данные и сверяем хеш паролей*/
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

    /* Если все ок, записываем в сессию пользователя*/
    if (empty($errorFields)) {
        $_SESSION['user']['id'] = $userData['id'];
        $_SESSION['user']['login'] = $userData['login'];
        $_SESSION['user']['avatar'] = $userData['avatar'];
    }
}

if (!empty($_SESSION['user'])) {
    redirectOnPage('feed.php');
} else {
    $authorizationPage = include_template('main-page.php',
        ['titleName' => 'readme: блог, каким он должен быть', 'errorFields' => $errorFields]);
    print_r($authorizationPage);
}
