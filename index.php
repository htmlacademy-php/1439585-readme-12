<?php

declare(strict_types=1);

require_once('config/db_connect.php');
require_once('helpers.php');
require_once('functions.php');

$is_auth = rand(0, 1);
$user_name = 'Стас';

$sqlCategories = "SELECT * FROM categories;";
$categories = fetchAll($sqlCategories, $connect);

$categoryIdList = [];
foreach ($categories as $category) {
    $categoryIdList[] = $category['id'];
}

$categoryId = filter_input(INPUT_GET, 'categoryid', FILTER_SANITIZE_NUMBER_INT);

$sqlСards = '';
$isPostsExsist = fetchAll("SELECT id FROM posts;", $connect);
if (empty($isPostsExsist)) {
    header('Location: /nothing-to-show.php');
}

if (in_array($categoryId, $categoryIdList)) {
    $sqlСards = "SELECT posts.id, full_name, avatar, title, category_id, content, quote_author, image_path, video_link, website_link, date_add, show_count FROM users JOIN posts ON users.id = posts.author_id WHERE category_id = ? ORDER BY show_count DESC;";
} else {
    $sqlСards = "SELECT posts.id, full_name, avatar, title, category_id, content, quote_author, image_path, video_link, website_link, date_add, show_count FROM users JOIN posts ON users.id = posts.author_id ORDER BY show_count DESC;";
}
$cards = fetchPrepareStmt($connect, $sqlСards, $categoryId);

$sqlRating = "SELECT COUNT(DISTINCT likes.id) AS 'likes', COUNT(DISTINCT comments.id) AS 'count_comment' FROM posts JOIN users ON users.id = posts.author_id LEFT JOIN likes ON posts.id = likes.post_id LEFT JOIN comments ON comments.post_id = posts.id WHERE posts.id = ?;";
$ratings = ratingCount($connect, $sqlRating, $cards);

$pageContent = include_template('main.php', ['cards' => $cards, 'categories' => $categories, 'ratings' => $ratings]);
$popularPage = include_template('layout.php', ['pageContent' => $pageContent, 'user_name' => $user_name, 'titleName' => 'readme: популярное', 'is_auth' => $is_auth]);

print_r($popularPage);
