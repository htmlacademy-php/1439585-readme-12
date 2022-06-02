<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();

$userData = userInitialization($connect);

if (!empty($userData)) {
    $postHashtags = [];

    $categories = getCategoryList($connect);

    //Получаем id категории, если пользователем выбрана категория на странице
    $categoryId = (int)filter_input(INPUT_GET, 'category_id', FILTER_SANITIZE_NUMBER_INT);

    //Получить список постов с сортировкой по дате добавления вместе с данными авторов, выборка только тех постов, на кого подписан пользователь
    if (!empty($categoryId)) {
        $posts = getSubscribesPostsByCategory($connect, $userData['id'], $categoryId);
    } else {
        $posts = getSubscribesPosts($connect, $userData['id']);
    }

    foreach ($posts as $post) {
        $postHashtags[$post['post_id']] = getPostHashtags($connect, $post['post_id']);
    }

    $pageContent = include_template('feed-details.php',
        ['categories' => $categories, 'posts' => $posts, 'postHashtags' => $postHashtags]);
    $feedPage = include_template('layout.php',
        [
            'pageContent' => $pageContent,
            'titleName' => 'readme: моя лента',
            'userData' => $userData,
            'is_auth' => AUTH
        ]);

    print_r($feedPage);
} else {
    /* Если по каким-то причинам массив $userData не заполнен, то принудительно разлогиневаем пользователя,
    чтобы он не получил просто белый экран, а смог авторизоваться еще раз; так как первым делом в скрипте функцией isUserLoggedIn() проверяется, есть ли у пользователя доступ к странице, то есть залогинен ли он*/
    redirectOnPage('logout.php');
}
