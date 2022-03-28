<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();

// Получаем данные по пользователю из сессии
$userId = $_SESSION['user']['id'];
$userData['login'] = $_SESSION['user']['login'];
$userData['avatar'] = $_SESSION['user']['avatar'];

$postHashtags = [];

$categories = getCategoryList($connect);

/*Получаем id категории, если пользователем выбрана категория на странице */
$categoryId = (int)filter_input(INPUT_GET, 'category_id', FILTER_SANITIZE_NUMBER_INT);

/*получить список постов с сортировкой по дате добавления вместе с данными авторов, выборка только тех постов, на кого подписан пользователь*/
if (!empty($categoryId)) {
    $posts = getSubscribesPostsByCategory($connect, $userId, $categoryId);
} else {
    $posts = getSubscribesPosts($connect, $userId);
}

/* Получаем в ассоциативный массив в хэштегами ко всем постам на странице, где ключ массова - id поста*/
foreach ($posts as $post) {
    $postHashtags[$post['post_id']] = array_column(getPostHashtags($connect, $post['post_id']), 'hashtag_content');
}

$pageContent = include_template('feed-details.php', ['categories' => $categories, 'posts' => $posts, 'postHashtags' => $postHashtags]);
$feedPage = include_template('layout.php', ['pageContent' => $pageContent, 'titleName' => 'readme: моя лента', 'userData' => $userData, 'is_auth' => AUTH]);

print_r($feedPage);
