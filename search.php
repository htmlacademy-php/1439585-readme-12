<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();

$userData = userInitialization($connect);

$errorFields = '';
$searchQuery = '';
$searchContent = [];
$postHashtags = [];
$templateName = 'no-results.php';
$titleName = 'readme: страница результатов поиска (нет результатов)';

//Узнаем, с какой страницы пришел пользователь, чтобы в случае, когда ничего не найдено можно было вернуть его обратно
$httpRefererPage = '';
if (isset($_SERVER['HTTP_REFERER'])) {
    $httpRefererPage = $_SERVER['HTTP_REFERER'];
}

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {

    $searchQuery = (trim($_GET['query']));

    //Определяем, поисковой запрос был из строки запроса или по хэштегу
    $searchType = defineTypeSearchQuery($searchQuery);

    switch ($searchType) {
        case('queryString'):
            $searchContent = getSearchQueryResult($connect, $searchQuery);
            break;
        case('tag'):
            $tagSearch = substr($_GET['query'], 1);
            $searchContent = getTagSearchResult($connect, $tagSearch);
            break;
    }

    if (empty($searchContent)) {
        $errorFields = 'error';
    }
} else {
    $errorFields = 'error';
}

if (empty($errorFields)) {

    // Получаем в ассоциативный массив в хэштегами ко всем постам для страницы поиска, где ключ массова - id поста
    foreach ($searchContent as $content) {
        $postHashtags[$content['post_id']] = getPostHashtags($connect, $content['post_id']);
    }

    $templateName = 'search-results.php';
    $titleName = 'readme: страница результатов поиска';
}

$pageContent = include_template($templateName,
    [
        'searchQuery' => $searchQuery,
        'searchContent' => $searchContent,
        'postHashtags' => $postHashtags,
        'httpRefererPage' => $httpRefererPage
    ]);

$searchPage = include_template('layout.php',
    ['pageContent' => $pageContent, 'titleName' => $titleName, 'userData' => $userData, 'is_auth' => AUTH]);

print_r($searchPage);
