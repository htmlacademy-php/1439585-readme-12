<?php

declare(strict_types=1);

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('helpers.php');
require_once('functions.php');

$categories = getCategoryList($connect);
$errorFields = [];

/* допустим у нас id пользователя храниться в переменной $user_id */
$userId = '1';

/* Если была отправлена форма, то выполняем дальнейшие действия на проверку и отправку данных в БД */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postType = $_POST['post-type'];
    foreach ($categories as $category) {
        if ($postType == $category['class_name']) {
            $categoryId = $category['id'];
            break;
        }
    }

    switch ($postType) {
        case ('text'):
            $requiredFields = ['heading' => "Заголовок.", 'post-text' => "Текст поста."];
            /* Пусты ли обязательные поля */
            $errorFields = validateEmptyField($_POST, $requiredFields);

            if (empty($errorFields)) {
                /* Добавляем непосредственно сам пост в таблицу с постами */
                addNewTextPost($connect, $requiredFields, $userId, $categoryId);
            }

            break;

        case ('quote'):
            $requiredFields = ['heading' => "Заголовок.", 'cite-text' => "Текст цитаты.", 'quote-author' => "Автор цитаты."];
            $errorFields = validateEmptyField($_POST, $requiredFields);

            if (empty($errorFields)) {
                addNewQuotePost($connect, $requiredFields, $userId, $categoryId);
            }

            break;

        case ('photo'):
            $requiredFields = ['heading' => "Заголовок."];
            $errorFields = validateEmptyField($_POST, $requiredFields);

            /* Проверяем далее по отдельности вызывая функции, тк нам нужно, чтобы выдавало разное описание в случае ошибки*/
            if (validateEmptyPicture() === false) {
                $errorFields['image-path'] = 'Вы не добавили картинку';
            }

            if (!empty(($_FILES['userpic-file-photo']['name']))) {

                if (validatePictureFromUser('userpic-file-photo') === false) {
                    $errorFields['image-path'] = 'Файл не является картинкой';
                } else {
                    $imageName = savePictureFromUser('userpic-file-photo');
                }
            } elseif (!empty($_POST['photo-url'])) {

                if (validatePictureUrl('photo-url') === false) {
                    $errorFields['image-path'] = 'Указан не корректный url-адрес картинки';
                } else {
                    $imageName = savePictureByUrl('photo-url');
                }
            }

            if (empty($errorFields)) {
                $imagePath = getPicturePath($imageName);
                addNewPhotoPost($connect, $requiredFields, $userId, $categoryId, $imagePath);
            }

            break;

        case ('video'):
            $requiredFields = ['heading' => "Заголовок.", 'video-url' => "Ссылка на YouTube."];
            $errorFields = validateEmptyField($_POST, $requiredFields);
            $validateVideoUrl = validateVideo($_POST['video-url']);

            if (empty($errorFields) & ($validateVideoUrl !== null)) {
                $errorFields['video-url'] = $validateVideoUrl;
            }

            if (empty($errorFields)) {
                addNewVideoPost($connect, $requiredFields, $userId, $categoryId);
            }

            break;

        case ('link'):
            $requiredFields = ['heading' => "Заголовок.", 'post-link' => "Ссылка."];
            $errorFields = validateEmptyField($_POST, $requiredFields);

            if (empty($errorFields) & (validateUrl($_POST['post-link']) === false)) {
                $errorFields['post-link'] = 'Ссылка не является корректным url адресом';
            }

            if (empty($errorFields)) {
                addNewLinkPost($connect, $requiredFields, $userId, $categoryId);
            }

            break;
    }

    $post_id = mysqli_insert_id($connect);

    /* Если пост был записан */
    if (!empty($post_id)) {

        /* Проверяем наличие тегов ибо они не обязательные поля; при наличии добавляем в БД */
        if (!empty($_POST['tags'])) {
            $hashtags = prepareTags('tags');
            if (!empty($hashtags)) {
                addPostsHashtags($connect, $post_id, $hashtags);
            }
        }

        $redirectPage = "post.php?postId=" . $post_id;
        redirectOnPage($redirectPage);
    }
}

/* формирование страницы, разделение на пару шаблонов с баннером ошибок и самой формой */
$redErrorBanner = include_template('/error-fields.php', ['errorFields' => $errorFields]);
$pageContent = include_template('adding-post.php', ['categories' => $categories, 'user_name' => USER_NAME, 'titleName' => 'Добавление публикации', 'is_auth' => IS_AUTH, 'postType' => $postType, 'errorFields' => $errorFields, 'redErrorBanner' => $redErrorBanner]);

print_r($pageContent);
