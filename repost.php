<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('functions.php');

isUserLoggedIn();

if (!empty($_SESSION['user']['id'])) {
    $httpRefererPage = $_SERVER['HTTP_REFERER'];
    $userId = (int)$_SESSION['user']['id'];

    $originalPostId = (int)filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);

    //Если такого поста в БД не существует, отправим пользователя на главную страницу
    if (isPostExists($connect, $originalPostId) === false) {
        redirectOnPage('index.php');
    }

    $originalPostData = getPostData($connect, $originalPostId);

    //После того как получили данные по посту, сравниваем, не является ли автор поста тем, кто делает репост;
    // иначе оставим пользователя на той же странице, с которой выполняется процесс репоста
    if ($userId === (int)$originalPostData['author_id']) {
        header("Location: $httpRefererPage");
        die;
    }

    //Проверить, есть ли к посту хэштеги
    $repostHashtags = [];
    if (!empty(getHashtagsForRepost($connect, $originalPostId))) {
        $repostHashtags = getHashtagsForRepost($connect, $originalPostId);
    }

    //Скорректируем данные из оригинального поста: извлечем автора оригинального поста в $authorId,
    // затем заменим авторизованным пользователем
    $authorId = $originalPostData['author_id'];
    $originalPostData['author_id'] = $userId;
    $originalPostData['is_repost'] = 1;
    $originalPostData['original_author_id'] = $authorId;

    addRepost($connect, $originalPostData, $originalPostId, $repostHashtags);

    redirectOnPage("profile.php?profile_id=$userId");
} else {
    /* Если по каким-то причинам $_SESSION['user']['id'] не заполнен, то принудительно разлогиневаем пользователя,
    чтобы он не получил просто белый экран, а смог авторизоваться еще раз; так как первым делом в скрипте функцией isUserLoggedIn() проверяется, есть ли у пользователя доступ к странице, то есть залогинен ли он*/
    redirectOnPage('logout.php');
}
