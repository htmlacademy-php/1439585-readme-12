<?php

require_once('config/db_connect.php');
require_once('helpers.php');
require_once('functions.php');


$is_auth = rand(0, 1);
$user_name = 'Стас';

$sqlCategories = "SELECT * FROM categories;";
$categories = fetchAll($sqlCategories, $connect);

$categoreId = [];
foreach ($categories as $category) {
    $categoreId[] = $category['id'];
}

$categoryName = filter_input(INPUT_GET, 'categoryname', FILTER_SANITIZE_SPECIAL_CHARS);
if ($_GET == NULL) {
    $sqlСards = "SELECT posts.id, full_name, avatar, title, category_id, content, quote_author, image_path, video_link, website_link, date_add, show_count FROM users JOIN posts ON users.id = posts.author_id ORDER BY show_count DESC;";
} elseif (in_array($categoryName, $categoreId)) {
    $sqlСards = "SELECT posts.id, full_name, avatar, title, category_id, content, quote_author, image_path, video_link, website_link, date_add, show_count FROM users JOIN posts ON users.id = posts.author_id WHERE category_id = {$categoryName} ORDER BY show_count DESC;";
} else {
    header('Location: /nothing-to-show.php');
}
$cards = fetchAll($sqlСards, $connect);

/*добавила вывод кол-ва лайков и комментов на страницу популярного*/
$ratings = [];
$k = 0;
foreach ($cards as $card) {
    $key = $card['id'];
    $sqlPostRating = "SELECT COUNT(DISTINCT likes.id) AS 'likes', COUNT(DISTINCT comments.id) AS 'count_comment' FROM posts JOIN users ON users.id = posts.author_id LEFT JOIN likes ON posts.id = likes.post_id LEFT JOIN comments ON comments.post_id = posts.id WHERE posts.id = {$key};";
    $postRating = fetchAll($sqlPostRating, $connect);

    $ratings[$k]['post_id'] = $key;
    $ratings[$k]['likes'] = $postRating[0]['likes'];
    $ratings[$k]['count_comment'] = $postRating[0]['count_comment'];
    $k++;
}

$pageContent = include_template('main.php', ['cards' => $cards, 'categories' => $categories, 'ratings' => $ratings]);
$popularPage = include_template('layout.php', ['pageContent' => $pageContent, 'user_name' => $user_name, 'titleName' => 'readme: популярное', 'is_auth' => $is_auth]);

print_r($popularPage);
