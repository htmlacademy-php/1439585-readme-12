<?php
date_default_timezone_set('Europe/Moscow');
require_once('helpers.php');
require_once('functions.php');

$connect = new mysqli("sbrt", "root", "root", "readme");
if ($connect->connect_error) {
    echo "SHIT HAPPENS: " . $connect->connect_error;;
}
$connect->set_charset('utf8');

$sqlCategories = "SELECT id, name, class_name FROM categories";
$resultSqlCategories = $connect->query($sqlCategories);
$categories = $resultSqlCategories->fetch_all(MYSQLI_ASSOC);

$sqlСards = "SELECT users.id, login, full_name, avatar, title, category_id, content, quote_author, image_path, video_link, website_link, date_add, show_count FROM users JOIN posts ON users.id = posts.author_id ORDER BY show_count DESC;";
$resultSqlСards = $connect->query($sqlСards);
$cards = $resultSqlСards->fetch_all(MYSQLI_ASSOC);

$is_auth = rand(0, 1);
$user_name = 'Стас';

$mainContent = include_template('main.php', ['cards' => $cards, 'categories' => $categories]);

$popularPage = include_template('layout.php', ['mainContent' => $mainContent, 'user_name' => $user_name, 'titleName' => 'readme: популярное', 'is_auth' => $is_auth]);

print_r($popularPage);
