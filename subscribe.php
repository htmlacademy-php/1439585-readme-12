<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('functions.php');

isUserLoggedIn();

$userData['id'] = (int)$_SESSION['user']['id'];
$userData['login'] = $_SESSION['user']['login'];
$httpRefererPage = $_SERVER['HTTP_REFERER'];

$authorId = (int)filter_input(INPUT_GET, 'author_id',
        FILTER_SANITIZE_NUMBER_INT) ?? header("Location: $httpRefererPage");

//По этому ID убедиться, что в таблице пользователей такой пользователь существует;
if (isUserExists($connect, $authorId) === false) {
    header("Location: $httpRefererPage");
}

subscribeToUser($connect, $userData['id'], $authorId);

//Выполнить переадресацию обратно на профиль пользователя, если в БД была добавлена запись
if (empty(mysqli_insert_id($connect))) {
    header("Location: $httpRefererPage");
}

//В случае если запись была успешно добавлена в БД, надо отправить этому пользователю уведомление о новом подписчике.
$recipientData = getUserDataForMailer($connect, $authorId);
$messageContent = messageContent($recipientData['login'], $userData, 'subscribe');
sendMailNotification($transport, $recipientData['email'], $messageContent);

redirectOnPage('profile.php?profile_id=' . $authorId);
