<?php
declare(strict_types=1);
date_default_timezone_set('Europe/Moscow');
require_once('helpers.php');
require_once('functions.php');
require_once('config/dbConnect.php');

$is_auth = rand(0, 1);
$user_name = 'Стас';

$sqlCategories = "SELECT id, name, class_name FROM categories;";
$categories = fetchAll($sqlCategories, $connect);

if ($sortCategoryBy = $_GET['categoryname']) {
    $sqlСardsByCategory = "SELECT posts.id, full_name, avatar, title, category_id, content, quote_author, image_path, video_link, website_link, date_add, show_count FROM users JOIN posts ON users.id = posts.author_id WHERE category_id = {$sortCategoryBy} ORDER BY show_count DESC;";
    $cards = fetchAll($sqlСardsByCategory, $connect);
} else {
    $sqlСards = "SELECT posts.id, full_name, avatar, title, category_id, content, quote_author, image_path, video_link, website_link, date_add, show_count FROM users JOIN posts ON users.id = posts.author_id ORDER BY show_count DESC;";
    $cards = fetchAll($sqlСards, $connect);
}

$pageContent = include_template('main.php', ['cards' => $cards, 'categories' => $categories]);
$popularPage = include_template('layout.php', ['pageContent' => $pageContent, 'user_name' => $user_name, 'titleName' => 'readme: популярное', 'is_auth' => $is_auth]);

print_r($popularPage);
