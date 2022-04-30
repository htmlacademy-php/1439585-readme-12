<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();

$userData['id'] = $_SESSION['user']['id'];
$userData['login'] = $_SESSION['user']['login'];
$userData['avatar'] = $_SESSION['user']['avatar'];
$postHashtags = [];

$userProfileId = (int)filter_input(INPUT_GET, 'profile_id', FILTER_SANITIZE_NUMBER_INT);
if (empty($userProfileId)) {
    redirectOnPage('nothing-to-show');
}

// Проверить, что такой пользователь существует
if (isUserExists($connect, $userProfileId) === false) {
    redirectOnPage('nothing-to-show');
}
$userProfileData = getUserData($connect, $userProfileId, $userData['id']);

// В зависимости от того, какая вкладка выбрана для показа, тот контент и подгружаем
$section = (string)filter_input(INPUT_GET, 'section', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'posts';
switch ($section) {
    case('likes'):
        $mainContent = getLikesForUserProfilePage($connect, $userProfileId);
        break;
    case('subscriptions'):
        $mainContent = getSubscribersList($connect, $userProfileId, $userData['id']);
        break;
    default:
        $mainContent = getUsersPosts($connect, $userProfileId);

        // Список тегов к этим постам
        foreach ($mainContent as $post) {
            $postHashtags[$post['post_id']] = array_column(getPostHashtags($connect, $post['post_id']),
                'hashtag_content');
        }
        break;
}

$pageContent = include_template('profile-details.php', [
    'userProfileData' => $userProfileData,
    'mainContent' => $mainContent,
    'postHashtags' => $postHashtags,
    'section' => $section
]);
$profilePage = include_template('layout.php',
    ['pageContent' => $pageContent, 'titleName' => 'readme: профиль', 'userData' => $userData, 'is_auth' => AUTH]);
print_r($profilePage);
