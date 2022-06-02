<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();

$userData = userInitialization($connect);

if (!empty($userData)) {

    $postHashtags = [];

    $userProfileId = (int)filter_input(INPUT_GET, 'profile_id', FILTER_SANITIZE_NUMBER_INT);
    if (empty($userProfileId)) {
        pageNotFound($userData);
    }

    if (isUserExists($connect, $userProfileId) === false) {
        pageNotFound($userData);
    }
    $userProfileData = getUserData($connect, $userProfileId, $userData['id']);

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

            foreach ($mainContent as $post) {
                $postHashtags[$post['post_id']] = getPostHashtags($connect, $post['post_id']);
            }
            break;
    }

    $pageContent = include_template('profile-details.php', [
        'userProfileData' => $userProfileData,
        'authorizedUser' => $userData['id'],
        'mainContent' => $mainContent,
        'postHashtags' => $postHashtags,
        'section' => $section
    ]);
    $profilePage = include_template('layout.php',
        ['pageContent' => $pageContent, 'titleName' => 'readme: профиль', 'userData' => $userData, 'is_auth' => AUTH]);
    print_r($profilePage);
} else {
    /* Если по каким-то причинам массив $userData не заполнен, то принудительно разлогиневаем пользователя,
    чтобы он не получил просто белый экран, а смог авторизоваться еще раз; так как первым делом в скрипте функцией isUserLoggedIn() проверяется, есть ли у пользователя доступ к странице, то есть залогинен ли он*/
    redirectOnPage('logout.php');
}
