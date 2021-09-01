<?php

declare(strict_types=1);
require_once('helpers.php');
require_once('functions.php');
require_once('config/dbConnect.php');

$is_auth = rand(0, 1);
$user_name = 'Стас';

if (isset($_GET['postId'])) {
    $postId = $_GET['postId'];
} else {
    header('Location: /nothing-to-show.php');
}

$isPostExsist = fetchAll("SELECT * from posts WHERE id={$postId};", $connect);
if (count($isPostExsist) == 0) {
    header('Location: /nothing-to-show.php');
}

/* запрос типов категорий */
$sqlCategories = "SELECT id, name, class_name FROM categories;";
$categories = fetchAll($sqlCategories, $connect);

/* запрос поста*/
$sqlPostPage = "SELECT author_id, full_name, avatar, date_registration, category_id, title, posts.content, image_path, video_link, website_link, show_count FROM posts
JOIN users ON users.id = posts.author_id WHERE posts.id = {$postId};";
$post = fetchAll($sqlPostPage, $connect);

$authouId = $post[0]['author_id'];

/* Количество лайков к посту*/
$sqlLikes = "SELECT * FROM posts  JOIN users ON users.id = posts.author_id  JOIN likes ON posts.id = likes.post_id WHERE posts.id = {$postId};";
$likesCount = count(fetchAll($sqlLikes, $connect));

/* Количество комментов к посту*/
$sqlComments = "SELECT * FROM comments JOIN posts ON comments.post_id = posts.id JOIN users ON users.id = posts.author_id  WHERE posts.id = {$postId};";
$commentsCount = count(fetchAll($sqlComments, $connect));

/* данные по пользователю*/
$sqlAuthorData = "SELECT users.id, users.full_name, users.avatar, users.date_registration FROM users JOIN posts ON users.id = posts.author_id WHERE posts.id = {$postId};";
$authorData = fetchAll($sqlAuthorData, $connect);

/* число подписчиков*/
$sqlSubscribers = "SELECT* FROM users JOIN subscribes ON users.id = subscribes.author_id WHERE users.id = {$authouId};";
$subscribersCount = count(fetchAll($sqlSubscribers, $connect));

/* кол-во публикаций*/
$sqlPublishedPosts = "SELECT * FROM users  JOIN posts ON users.id = posts.author_id  WHERE users.id = {$authouId};";
$postsCount = count(fetchAll($sqlPublishedPosts, $connect));

$pageContent = include_template('post-details.php', ['post' => $post, 'authorData' => $authorData, 'categories' => $categories, 'likesCount' => $likesCount, 'commentsCount' => $commentsCount, 'subscribersCount' => $subscribersCount, 'postsCount' => $postsCount]);
$postPage = include_template('layout.php', ['pageContent' => $pageContent, 'user_name' => $user_name, 'titleName' => 'Публикация', 'is_auth' => $is_auth]);

print_r($postPage);
