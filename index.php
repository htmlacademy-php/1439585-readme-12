<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

$errorFields = [];

/* Проверяем, что форма отправлена*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Валидация на заполненность полей*/
    $errorFields = validateEmptyField($_POST, ['email' => "Email. ", 'password' => "Пароль."]);

    /* Все ли поля заполнены и существует ли пользователь в БД*/
    if (checkEmailExists($connect, $_POST['email']) && empty($errorFields)) {

        /* Если да, получаем по нему данные и сверяем хеш паролей*/
        $userData = getUserAuthorizationData($connect, $_POST['email']);
        if (!password_verify($_POST['password'], $userData[0]['password'])) {
            $errorFields['email'] = $errorFields['password'] = 'Вы ввели неверный email/пароль';
        }

    } else {
        $errorFields['email'] = $errorFields['password'] = 'Вы ввели неверный email/пароль';
    }

    /* Если все ок, записываем в сессию id пользователя*/
    if (empty($errorFields)) {
        $_SESSION['user']['id'] = $userData[0]['id'];
        $_SESSION['user']['login'] = $userData[0]['login'];
        $_SESSION['user']['avatar'] = $userData[0]['avatar'];
    }
}

if (!empty($_SESSION['user'])) {
    redirectOnPage('feed.php');
} else {
    $authorizationPage = include_template('main-page.php',
        ['titleName' => 'readme: блог, каким он должен быть', 'errorFields' => $errorFields]);
    print_r($authorizationPage);
}
