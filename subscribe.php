<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('functions.php');

isUserLoggedIn();

if (isset($_SESSION['user']['id']) && isset($_SESSION['user']['login'])) {
    $userData['id'] = (int)$_SESSION['user']['id'];
    $userData['login'] = $_SESSION['user']['login'];
    $httpRefererPage = $_SERVER['HTTP_REFERER'] ?? '/index.php';

    $authorId = (int)filter_input(INPUT_GET, 'author_id',
            FILTER_SANITIZE_NUMBER_INT) ?? header("Location: $httpRefererPage");

    //По этому ID убедиться, что в таблице пользователей такой пользователь существует;
    if (isUserExists($connect, $authorId) === false) {
        pageNotFound($userData);
    }

    subscribeToUser($connect, $userData['id'], $authorId);

    //Выполнить переадресацию обратно на профиль пользователя, если в БД была добавлена запись
    if (empty(mysqli_insert_id($connect))) {
        header("Location: $httpRefererPage");
    }

    //В случае если запись была успешно добавлена в БД, надо отправить этому пользователю уведомление о новом подписчике.
    $recipientData = getUserDataForMailer($connect, $authorId);
    $messageContent = messageContent($recipientData['login'], $userData['login'], (int)$userData['id'], 'subscribe');
    sendMailNotification($transport, $recipientData['email'], $messageContent['subject'], $messageContent['body']);
    redirectOnPage('profile.php?profile_id=' . $authorId);
} else {
    // Если массив $_SESSION['user'] не установлен, принудительно разлогиневаем пользователя
    redirectOnPage('logout.php');
}
