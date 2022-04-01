<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('helpers.php');
require_once('functions.php');

isUserLoggedIn();

// Получаем данные по пользователю из сессии
$userData['login'] = $_SESSION['user']['login'];
$userData['avatar'] = $_SESSION['user']['avatar'];

$postId = filter_input(INPUT_GET, 'postId',
    FILTER_SANITIZE_NUMBER_INT) ?: header('Location: /nothing-to-show.php');

$isPostExsist = fetchArrayPrepareStmt($connect, "SELECT * from posts WHERE id = ?;", $postId);
if (empty($isPostExsist)) {
    header('Location: /nothing-to-show.php');
}

//Получение данных о посте, авторе поста и рейтинге
$postData = getContentDataForPostPage($connect, (int)$postId);

//Получение комментов к посту
$postsComments = getPostComments($connect, (int)$postId);

/*формирование страницы */
$pageContent = include_template('post-details.php',
    ['postData' => $postData, 'postsComments' => $postsComments, 'userData' => $userData]);
$postPage = include_template('layout.php',
    ['pageContent' => $pageContent, 'titleName' => 'readme: публикация', 'userData' => $userData, 'is_auth' => AUTH]);

print_r($postPage);
