<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();

// Получаем данные по пользователю из сессии
$userData['id'] = $_SESSION['user']['id'];
$userData['login'] = $_SESSION['user']['login'];
$userData['avatar'] = $_SESSION['user']['avatar'];

/*Получение списка категорий из БД */
$categories = getCategoryList($connect);

/*Получаем id категории, если пользователем выбрана категория на странице */
$categoryId = (int)filter_input(INPUT_GET, 'category_id', FILTER_SANITIZE_NUMBER_INT);

/* Получаем параметры сортировки и направление сортировки */
$getSortBy = (string)filter_input(INPUT_GET, 'by', FILTER_SANITIZE_SPECIAL_CHARS);
$getSortOrder = (string)filter_input(INPUT_GET, 'sorting', FILTER_SANITIZE_SPECIAL_CHARS);

/* Используется switch case default, чтобы при получении иного типа сортировки или отсутствия заданных параметров не возникало ошибки при обращении к БД, а задавались параметры по умолчанию*/
switch ($getSortBy) {
    case('rating'):
        $sortByParam = 'likes_count';
        break;
    case('date_add'):
        $sortByParam = 'date_add';
        break;
    default:
        $sortByParam = 'show_count';
}
switch ($getSortOrder) {
    case('asc'):
        $sortOrderParam = 'ASC';
        break;
    default:
        $sortOrderParam = 'DESC';
}

//Текущие параметры сортировки и фильтрации на странице
$currentPageParams = '';
// Узнаем, на какой странице находимся сейчас
$currentPage = (int)filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
if (empty($currentPage) || ($currentPage === 1)) {
    $currentPage = 1;
    $previousPage = $currentPage;
} else {
    $previousPage = $currentPage - 1;
}

$limit = 6;
$offset = $limit * ($currentPage - 1);

if (!empty($categoryId)) {
    // если выбрана категория постов, то действия следующие:
    // количество постов всего в данной категории
    $countPosts = countPostsByCategory($connect, $categoryId);
    $cards = getCardsByCategory($connect, $categoryId, $limit, $offset, $sortByParam, $sortOrderParam);
    $currentPageParams = '&category_id=' . $categoryId;
    if (!empty($getSortBy)) {
        $currentPageParams = '&category_id=' . $categoryId . '&by=' . $getSortBy . '&sorting=' . $getSortOrder;;
    }
} else {
    // иначе показать все посты и выполняем следующие действия:
    // количество постов всего
    $countPosts = countAllPosts($connect);
    $cards = getAllCardsContent($connect, $limit, $offset, $sortByParam, $sortOrderParam);
    if (!empty($getSortBy)) {
        $currentPageParams = '&by=' . $getSortBy . '&sorting=' . $getSortOrder;
    }
}

// количество выводимых страниц
$countPages = ceil($countPosts / $limit);
// условие, что количество next page должны быть ограничены количеством страниц
if ($currentPage < $countPages) {
    $nextPage = $currentPage + 1;
} else {
    $nextPage = $currentPage;
}

/*если массив $cards пустой, редиректим на nothing-to-show.php */
if (empty($cards)) {
    redirectOnPage('nothing-to-show.php');
}

$pageContent = include_template('main.php', [
    'cards' => $cards,
    'categories' => $categories,
    'getSortBy' => $getSortBy,
    'getSortOrder' => $getSortOrder,
    'countPosts' => $countPosts,
    'currentPage' => $currentPage,
    'currentPageParams' => $currentPageParams,
    'previousPage' => $previousPage,
    'nextPage' => $nextPage
]);
$popularPage = include_template('layout.php',
    ['pageContent' => $pageContent, 'titleName' => 'readme: популярное', 'userData' => $userData, 'is_auth' => AUTH]);

print_r($popularPage);
