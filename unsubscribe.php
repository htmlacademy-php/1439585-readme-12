<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('functions.php');

isUserLoggedIn();

if (isset($_SESSION['user']['id'])) {
    $userId = (int)$_SESSION['user']['id'];
    $httpRefererPage = $_SERVER['HTTP_REFERER'];

    $authorId = (int)filter_input(INPUT_GET, 'author_id',
            FILTER_SANITIZE_NUMBER_INT) ?? header("Location: $httpRefererPage");

    //По этому ID убедиться, что в таблице подписок существует такая связь "авторизованный пользователь-автор"
    if (isUserSubscribe($connect, $userId, $authorId) === false) {
        header("Location: $httpRefererPage");
    }

    unsubscribeFromUser($connect, $userId, $authorId);

    //Проверка на наличие или отсутствие ошибки последней операции и редирект на профайл
    if (mysqli_errno($connect) > 0) {
        header("Location: $httpRefererPage");
    }
    redirectOnPage('profile.php?profile_id=' . $authorId);
} else {
    // Если ключ массива $_SESSION['user']['id'] не установлен, принудительно разлогиневаем пользователя
    redirectOnPage('logout.php');
}
