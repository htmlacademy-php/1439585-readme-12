<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();
$userData = userInitialization($connect);

if (!empty($userData)) {
    $postId = (int)filter_input(INPUT_GET, 'post_id',
            FILTER_SANITIZE_NUMBER_INT) ?? pageNotFound($userData);

    if (isPostExists($connect, $postId) === false) {
        pageNotFound($userData);
    }

    $postData = getContentDataForPostPage($connect, $postId, $userData['id']);
    $postsComments = getPostComments($connect, $postId);

    //Добавление комментария к посту
    $validationError = '';
    if (!empty($_POST['comment_content']) && !empty($_POST['post-id'])) {
        // Проверяем, что такой пост все еще существует, post-id берем из формы
        if (isPostExists($connect, (int)$_POST['post-id']) === false) {
            pageNotFound();
        }

        if (validateCommentLength($_POST['comment_content']) === true) {
            addCommentToPost($connect, $postId, (int)$userData['id'], $_POST['comment_content']);
            if (!empty(mysqli_insert_id($connect))) {
                redirectOnPage("profile.php?profile_id=" . $postData['user_id']);
            }
        } else {
            $validationError = 'Это поле обязательно к заполнению';
        }
    }

    //Обновление счетчика просмотров в случае, если было простое обновление страницы, а не при неудачной валидации добавление коммента
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        updateShowCount($connect, $postId);
    }

    $pageContent = include_template('post-details.php',
        [
            'postData' => $postData,
            'postsComments' => $postsComments,
            'userData' => $userData,
            'validationError' => $validationError
        ]);
    $postPage = include_template('layout.php',
        [
            'pageContent' => $pageContent,
            'titleName' => 'readme: ' . $postData['title'],
            'userData' => $userData,
            'is_auth' => AUTH
        ]);

    print_r($postPage);
} else {
    /* Если по каким-то причинам массив $userData не заполнен, то принудительно разлогиневаем пользователя,
    чтобы он не получил просто белый экран, а смог авторизоваться еще раз; так как первым делом в скрипте функцией isUserLoggedIn() проверяется, есть ли у пользователя доступ к странице, то есть залогинен ли он*/
    redirectOnPage('logout.php');
}
