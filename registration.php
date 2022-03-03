<?php

declare(strict_types=1);

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('helpers.php');
require_once('functions.php');

$errorFields = [];
$userData = [];
$requiredFields = [
    'email' => "Электронная почта.",
    'login' => "Логин.",
    'password' => "Пароль.",
    'password-repeat' => "Повтор пароля."
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Убедиться, что заполены все обязательные поля*/
    $errorFields = validateEmptyField($_POST, $requiredFields);

    /* Валидация емейла*/
    $userEmail = mysqli_real_escape_string($connect, $_POST['email']);

    if (checkUserExists($connect, $userEmail) !== false) {
        $errorFields = array_merge(['email' => 'Пользователь с таким email, ' . $userEmail . ', уже зарегистрирован.'], $errorFields);
    }

    if (empty($errorFields) & (validateEmail($userEmail) === false)) {
        $errorFields = array_merge(['email' => 'Вы указали некорректный email.'], $errorFields);
    }

    /* Валидация паролей*/
    if (isPasswordCorrect($_POST['password']) != true) {
        $errorFields['password'] = 'Пароль должен содержать не менее 6 символов. В нем должны быть цифры и буквы латинского алфавита верхнего и нижнего регистров.';
    }

    if (checkPasswordMatch($_POST['password'], $_POST['password-repeat']) === false) {
        $errorFields['password'] = 'Пароли не совпадают.';
    }

    /*  Если юзер добавил аватарку, поверяем mime-тип*/
    if (!empty($_FILES['userpic-avatar']['name'])) {
        if (validatePictureFromUser('userpic-avatar') === false) {
            $errorFields['userpic-avatar'] = 'Файл не является картинкой.';
        } else {
            $avatarImageName = savePictureFromUser('userpic-avatar');
        }
    }

    /* Если нет никаких ошибок, то сохранить данные в таблице пользователей*/
    if (empty($errorFields)) {

        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        if (!empty($avatarImageName)) {
            $avatarPath = getPicturePath($avatarImageName);
        }

        addNewUser($connect, [$userEmail, $_POST['login'], $hashedPassword, $avatarPath]);
        $post_id = mysqli_insert_id($connect);

        /* Если данные были записаны, т.е. получен id записи в БД, то переадресовываем пользователя на главную */
        if (!empty($post_id)) {
            $redirectPage = "main.html";
            redirectOnPage($redirectPage);
        }
    }
}

/* формирование страницы, разделение на шаблоны с баннером ошибок и самой формой */
$redErrorBanner = include_template('/error-fields.php', ['errorFields' => $errorFields]);
$registrationPageContent = include_template('user-registration.php', ['errorFields' => $errorFields, 'redErrorBanner' => $redErrorBanner]);
$registrationPage = include_template('layout.php', ['pageContent' => $registrationPageContent, 'titleName' => 'Регистрация', 'is_auth' => 0]);

print_r($registrationPage);
