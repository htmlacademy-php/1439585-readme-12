<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();

/*Получение списка категорий из БД */
$categories = getCategoryList($connect);

/*Получаем id категории если пользователем выбрана категория на странице */
$categoryId = (int)filter_input(INPUT_GET, 'category_id', FILTER_SANITIZE_NUMBER_INT);

if (!empty($categoryId)) {
    // если выбрана категория
    $cards = getCardsByCategory($categoryId, $connect);
} else {
    // иначе показать все
    $cards = getAllCardsContent($connect);
}

/*если массив $cards пустой, редиректим на nothing-to-show.php */
if (empty($cards)) {
    redirectOnPage('nothing-to-show.php');
}

$pageContent = include_template('main.php', ['cards' => $cards, 'categories' => $categories]);
$popularPage = include_template('layout.php', ['pageContent' => $pageContent, 'titleName' => 'readme: популярное', 'is_auth' => AUTH]);

print_r($popularPage);
