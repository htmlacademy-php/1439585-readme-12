<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('functions.php');

isUserLoggedIn();

$userId = (int)$_SESSION['user']['id'];
$httpRefererPage = $_SERVER['HTTP_REFERER'];

$authorId = (int)filter_input(INPUT_GET, 'author_id',
        FILTER_SANITIZE_NUMBER_INT) ?? header("Location: $httpRefererPage");

//По этому ID убедиться, что в таблице пользователей такой пользователь существует;
if (isUserExists($connect, $authorId) === false) {
    header("Location: $httpRefererPage");
}

subscribeToUser($connect, $userId, $authorId);

//Выполнить переадресацию обратно на профиль пользователя, если в БД была добавлена запись
if (empty(mysqli_insert_id($connect))) {
    header("Location: $httpRefererPage");
}
redirectOnPage('profile.php?profile_id=' . $authorId);

//Отправить этому пользователю уведомление о новом подписчике (смотрите описание процесса «Отправка уведомлений»). - пока не делала, тк работа с отправкой писем в следующем задании.
