<?php

require_once('helpers.php');
require_once('functions.php');
require_once('config/db_connect.php');

$is_auth = rand(0, 1);
$user_name = 'Стас';

$postId = filter_input(INPUT_GET, 'postId', FILTER_SANITIZE_NUMBER_INT) ?: header('Location: /nothing-to-show.php');

$isPostExsist = fetchAll("SELECT * from posts WHERE id={$postId};", $connect);
if (count($isPostExsist) == 0) {
    header('Location: /nothing-to-show.php');
}

/* запрос поста объединенный с категорией*/
$sqlPostPage = "SELECT author_id AS 'author_id',  category_id AS 'category_id', categories.class_name, title, posts.content, image_path, video_link, website_link, show_count FROM posts JOIN users ON users.id = posts.author_id JOIN categories ON posts.category_id = categories.id WHERE posts.id = {$postId};";
$post = fetchAll($sqlPostPage, $connect);

$authouId = $post[0]['author_id'];

/* запрос на количество лайков и комментов к конкретному посту*/
$sqlPostRating = "SELECT COUNT(DISTINCT likes.id) AS 'likes', COUNT(DISTINCT comments.id) AS 'count_comment' FROM posts JOIN users ON users.id = posts.author_id LEFT JOIN likes ON posts.id = likes.post_id LEFT JOIN comments ON comments.post_id = posts.id WHERE posts.id = {$postId};";
$postRating = fetchAll($sqlPostRating, $connect);

/* объеденила данные по пользователю, число подписчиков и кол-во публикаций */
$sqlAuthorData = "SELECT users.id AS 'users_id', users.full_name, users.avatar, users.date_registration, COUNT(DISTINCT subscribes.id) AS 'subscribes', COUNT(DISTINCT posts.id) AS 'count_posts' FROM users LEFT JOIN subscribes ON users.id = subscribes.author_id LEFT JOIN posts ON users.id = posts.author_id WHERE users.id = {$authouId};";
$authorData = fetchAll($sqlAuthorData, $connect);

/* получить комменты к посту */
$sqlPostComments = "SELECT posts.id AS 'posts_id', users.id AS 'comment_author', users.full_name, users.avatar, comments.date_add AS 'comment_date', comments.content AS 'comment' FROM posts LEFT JOIN comments  ON comments.post_id = posts.id JOIN users ON users.id = comments.user_id WHERE posts.id  = {$postId};";
$postComments = fetchAll($sqlPostComments, $connect);

/* формирование страницы */
$pageContent = include_template('post-details.php', ['post' => $post, 'postRating' => $postRating, 'postComments' => $postComments, 'authorData' => $authorData]);
$postPage = include_template('layout.php', ['pageContent' => $pageContent, 'user_name' => $user_name, 'titleName' => 'Публикация', 'is_auth' => $is_auth]);

print_r($postPage);
