<?php

declare(strict_types=1);

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('helpers.php');
require_once('functions.php');

$postId = filter_input(INPUT_GET, 'postId', FILTER_SANITIZE_NUMBER_INT) ?: header('Location: /nothing-to-show.php');

$isPostExsist = fetchPrepareStmt($connect, "SELECT * from posts WHERE id=?;", $postId);
if (empty($isPostExsist)) {
    header('Location: /nothing-to-show.php');
}

$sqlPostPage = "SELECT posts.id, author_id AS 'author_id', category_id AS 'category_id', categories.class_name, title, posts.content, quote_author, image_path, video_link, website_link, show_count FROM posts JOIN users ON users.id = posts.author_id JOIN categories ON posts.category_id = categories.id WHERE posts.id = ?;";
$post = fetchPrepareStmt($connect, $sqlPostPage, $postId);

$authouId = $post[0]['author_id'];

$sqlPostRating = "SELECT COUNT(DISTINCT likes.id) AS 'likes', COUNT(DISTINCT comments.id) AS 'count_comment' FROM posts JOIN users ON users.id = posts.author_id LEFT JOIN likes ON posts.id = likes.post_id LEFT JOIN comments ON comments.post_id = posts.id WHERE posts.id = ?;";
$postRating = ratingCount($connect, $sqlPostRating, $post);

$sqlAutor = "SELECT users.id, users.login, users.avatar, users.date_registration FROM users JOIN posts ON users.id = posts.author_id WHERE posts.id = ?;";
$author = fetchPrepareStmt($connect, $sqlAutor, $postId);

$sqlAuthorRating = "SELECT COUNT(DISTINCT subscribes.id) AS 'subscribes', COUNT(DISTINCT posts.id) AS 'count_posts' FROM users LEFT JOIN posts ON users.id = posts.author_id LEFT JOIN subscribes ON users.id = subscribes.author_id WHERE users.id = ?;";
$authorRating = fetchPrepareStmt($connect, $sqlAuthorRating, $authouId);

$authorData[] = call_user_func_array('array_merge', (array_merge($author, $authorRating)));

$sqlPostComments = "SELECT posts.id AS 'posts_id', users.id AS 'comment_author', users.login, users.avatar, comments.date_add AS 'comment_date', comments.content AS 'comment' FROM posts LEFT JOIN comments  ON comments.post_id = posts.id JOIN users ON users.id = comments.user_id WHERE posts.id  = ?;";
$postComments = fetchPrepareStmt($connect, $sqlPostComments, $postId);

/**формирование страницы */
$pageContent = include_template('post-details.php', ['post' => $post, 'postRating' => $postRating, 'postComments' => $postComments, 'authorData' => $authorData]);
$postPage = include_template('layout.php', ['pageContent' => $pageContent, 'user_name' => USER_NAME, 'titleName' => 'Публикация', 'is_auth' => IS_AUTH]);

print_r($postPage);
