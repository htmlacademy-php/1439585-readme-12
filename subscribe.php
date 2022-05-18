<?php

declare(strict_types=1);

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

require_once('vendor/autoload.php');
require_once('config/db_connect.php');
require_once('config/smtp_configuration.php');
require_once('functions.php');

session_start();

isUserLoggedIn();

$userId = (int)$_SESSION['user']['id'];
$userLogin = $_SESSION['user']['login'];
$httpRefererPage = $_SERVER['HTTP_REFERER'];

$authorId = (int)filter_input(INPUT_GET, 'author_id',
        FILTER_SANITIZE_NUMBER_INT) ?? header("Location: $httpRefererPage");

//По этому ID убедиться, что в таблице пользователей такой пользователь существует;
if (isUserExists($connect, $authorId) === false) {
    header("Location: $httpRefererPage");
}

subscribeToUser($connect, $userId, $authorId);

//Выполнить переадресацию обратно на профиль пользователя, если в БД была добавлена запись
if (empty(mysqli_insert_id($connect))) {
    header("Location: $httpRefererPage");
}

//В случае если запись была успешно добавлена в БД, надо отправить этому пользователю уведомление о новом подписчике.
$recipientData = getUserDataForMailer($connect, $authorId);
$messageSubject = "У вас новый подписчик";
$messageBody = "Здравствуйте, " . $recipientData['login'] . ". На вас подписался новый пользователь $userLogin. Вот ссылка на его профиль: " . $_SERVER['HTTP_HOST'] . "/profile.php?profile_id=$userId";

$emailNewSubscriber = (new Email())
    ->from(SENDER_ADDRESS)
    ->to($recipientData['email'])
    ->subject($messageSubject)
    ->text($messageBody);

$mailerNewSubscriber = new Mailer($transport);
try {
    $mailerNewSubscriber->send($emailNewSubscriber);
} catch (TransportExceptionInterface $exception) {
    echo sprintf("Поймано исключение: %s", $exception->getMessage());
    die;
}

redirectOnPage('profile.php?profile_id=' . $authorId);
