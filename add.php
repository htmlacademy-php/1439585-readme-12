<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();

$userData = userInitialization($connect);

if (!empty($userData)) {
    $categories = getCategoryList($connect);
    $errorFields = [];

    // Если была отправлена форма, то выполняем дальнейшие действия на проверку и отправку данных в БД
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post-type'])) {

        $categoryId = '';
        $imageName = '';

        $postType = $_POST['post-type'];
        foreach ($categories as $category) {
            if ((string)$postType === $category['class_name']) {
                $categoryId = $category['id'];
                break;
            }
        }

        switch ($postType) {
            case ('text'):
                $requiredFields = ['heading' => "Заголовок.", 'post-text' => "Текст поста."];
                $errorFields = validateEmptyField($_POST, $requiredFields);

                if (empty($errorFields)) {
                    // Добавляем непосредственно сам пост в таблицу с постами
                    addNewTextPost($connect, $requiredFields, $userData['id'], $categoryId);
                }
                break;

            case ('quote'):
                $requiredFields = [
                    'heading' => "Заголовок.",
                    'cite-text' => "Текст цитаты.",
                    'quote-author' => "Автор цитаты."
                ];
                $errorFields = validateEmptyField($_POST, $requiredFields);

                if (empty($errorFields)) {
                    addNewQuotePost($connect, $requiredFields, $userData['id'], $categoryId);
                }
                break;

            case ('photo'):
                $requiredFields = ['heading' => "Заголовок."];
                $errorFields = validateEmptyField($_POST, $requiredFields);

                // Проверяем далее по отдельности вызывая функции, тк нам нужно, чтобы выдавало разное описание в случае ошибки
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

                    if (validatePictureUrl($_POST['photo-url']) === false) {
                        $errorFields['image-path'] = 'Указан не корректный url-адрес картинки';
                    } else {
                        $imageName = savePictureByUrl($_POST['photo-url']);
                        if ($imageName === false) {
                            $errorFields['image-path'] = 'Указан не корректный url-адрес: не удалось сохранить картинку';
                        }
                    }
                }

                if (empty($errorFields)) {
                    $imagePath = getPicturePath($imageName);
                    addNewPhotoPost($connect, $requiredFields, $userData['id'], $categoryId, $imagePath);
                }
                break;

            case ('video'):
                $requiredFields = ['heading' => "Заголовок.", 'video-url' => "Ссылка на YouTube."];
                $errorFields = validateEmptyField($_POST, $requiredFields);

                // Строкой выше есть проверка validateEmptyField, в которой проверяется, не пусты ли указанные поля в массиве $_POST,
                if (empty($errorFields)) {
                    $validateVideoUrl = validateVideo($_POST['video-url']);

                    if ($validateVideoUrl !== null) {
                        $errorFields['video-url'] = $validateVideoUrl;
                    }

                    if (empty($errorFields)) {
                        addNewVideoPost($connect, $requiredFields, $userData['id'], $categoryId);
                    }
                }
                break;

            case ('link'):
                $requiredFields = ['heading' => "Заголовок.", 'post-link' => "Ссылка."];
                $errorFields = validateEmptyField($_POST, $requiredFields);

                if (empty($errorFields) && (validateUrl($_POST['post-link']) === false)) {
                    $errorFields['post-link'] = 'Ссылка не является корректным url адресом';
                }

                if (empty($errorFields)) {
                    addNewLinkPost($connect, $requiredFields, $userData['id'], $categoryId);
                }
                break;
        }

        // Если пост был записан, необходимо отправить подписчикам уведомление о новом посте и перенаправить автора на страницу нового поста
        $post_id = mysqli_insert_id($connect);
        if (!empty($post_id)) {

            // Проверяем наличие тегов ибо они не обязательные поля; при наличии добавляем в БД
            if (!empty($_POST['tags'])) {
                $hashtags = prepareTags($_POST['tags']);
                if (!empty($hashtags)) {
                    addPostsHashtags($connect, $post_id, $hashtags);
                }
            }

            if (checkSubscribersExists($connect, $userData['id']) === true) {
                $recipientList = getSubscribersListForMail($connect, $userData['id']);
                foreach ($recipientList as $recipient) {
                    $messageContent = messageContent($recipient['login'], $userData['login'], (int)$userData['id'],
                        'add');
                    sendMailNotification($transport, $recipient['email'], $messageContent['subject'],
                        $messageContent['body']);
                }
            }

            $redirectPage = "post.php?post_id=" . $post_id;
            redirectOnPage($redirectPage);
        }
    }

    $redErrorBanner = include_template('/error-fields.php', ['errorFields' => $errorFields]);
    $pageContent = include_template('adding-post.php', [
        'categories' => $categories,
        'errorFields' => $errorFields,
        'redErrorBanner' => $redErrorBanner
    ]);
    $addingPostPage = include_template('layout.php', [
        'pageContent' => $pageContent,
        'titleName' => 'readme: добавление публикации',
        'userData' => $userData,
        'is_auth' => AUTH
    ]);
    print_r($addingPostPage);
} else {
    /* Если по каким-то причинам массив $userData не заполнен, то принудительно разлогиневаем пользователя,
    чтобы он не получил просто белый экран, а смог авторизоваться еще раз; так как первым делом в скрипте функцией isUserLoggedIn() проверяется, есть ли у пользователя доступ к странице, то есть залогинен ли он*/
    redirectOnPage('logout.php');
}
