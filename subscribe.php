<?php

declare(strict_types=1);

require_once('config/db_connect.php');
require_once('functions.php');
require_once('templates/sending-mail-content.php');

session_start();

isUserLoggedIn();

$userId = (int)$_SESSION['user']['id'];
$userLogin = $_SESSION['user']['login'];
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

//В случае если запись была успешно добавлена в БД, надо отправить этому пользователю уведомление о новом подписчике.
//Тема и тело письма содержаться в шаблоне /templates/sending-mail-content.php
$recipientData = getUserDataForMailer($connect, $authorId);
sendMailNotification($transport, $recipientData['email'], $messageSubject, $messageBody);

redirectOnPage('profile.php?profile_id=' . $authorId);
