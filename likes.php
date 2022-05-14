<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('functions.php');

isUserLoggedIn();

$userId = (int)$_SESSION['user']['id'];
$httpRefererPage = $_SERVER['HTTP_REFERER'];

//Из параметра запроса нужно извлечь ID поста; если получено что-то другое, просто отправить юзера обратно на предыдущую страницу, соответственно без добавления лайка
$postId = (int)filter_input(INPUT_GET, 'post_id',
        FILTER_SANITIZE_NUMBER_INT) ?? header("Location: $httpRefererPage");

//По этому ID убедиться, что в таблице с постами есть такой пост;
if (isPostExists($connect, $postId) === false) {
    header("Location: $httpRefererPage");
}

//До того как добавить запись в таблицу связей проверим, нет ли там уже лайка к этому посту от этого пользователя
if (isLikeExists($connect, $userId, $postId) === false) {
    addLikeToPost($connect, $userId, $postId);
}
header("Location: $httpRefererPage");
