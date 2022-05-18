<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();

$userData['id'] = (int)$_SESSION['user']['id'];
$userData['login'] = $_SESSION['user']['login'];
$userData['avatar'] = $_SESSION['user']['avatar'];
$userData['all_new_messages'] = countAllNewMessages($connect, $userData['id']);

$newDialogUserData = [];
$messagesHistory = [];
$dialogId = '';
$validationError = '';

// Был ли выбран диалог с каким-либо пользователем
$messagesUserId = (int)filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка адресата
    $recipientId = (int)filter_input(INPUT_POST, 'recipient_user_id', FILTER_SANITIZE_NUMBER_INT);
    if ((isUserExists($connect, $recipientId) === false) || ($recipientId === $userData['id'])) {
        redirectOnPage('nothing-to-show');
    }

    // Проверка самого сообщения, не должно быть пустым
    if (validateEmptyMessage($_POST['message_content']) === false) {
        $validationError = 'Это поле обязательно к заполнению';
        $messagesUserId = $recipientId;
    } else {
        $messageData = [$userData['id'], $recipientId, $_POST['dialog_id'], trim($_POST['message_content'])];
        addMessage($connect, $messageData, (int)$_POST['dialog_id']);
        redirectOnPage('messages.php?user_id=' . $recipientId);
    }
}


// Получаем список, с кем есть диалоги
$contactsList = getMessagesContactsList($connect, $userData['id']);

// Если выбран диалог или мы перешли с профиля пользователя нажав на кнопку "сообщение", то надо получить переписку с ним в хронологическом порядке от обратного
if (!empty($messagesUserId)) {
    if (isDialogExists($connect, $userData['id'], $messagesUserId) === true) {
        // Если переписка с пользователем уже велась, то надо достать dialog_id
        $dialogId = getDialogId($connect, $userData['id'], $messagesUserId);
        $messagesHistory = getMessages($connect, $userData['id'], $messagesUserId);

        // После того как получили список сообщений с пользователем, отмечаем все сообщения прочитанными
        foreach ($messagesHistory as $message) {
            if (($message['is_new'] === 1) && ($message['sender_id'] !== $userData['id'])) {
                markMessageAsRead($connect, $message['id']);
            }
        }
    } else {
        // Если переписки нет, надо сгенерировать dialog_id и получить данные по пользователю для добавления в начало массива $contactsList, чтобы показать диалог выше всех
        $dialogId = generateNewDialogId($connect);
        $newDialogUserData = getUserDataForContactList($connect, $messagesUserId);
        array_unshift($contactsList, $newDialogUserData);
    }
}

$pageContent = include_template('messages-details.php',
    [
        'userData' => $userData,
        'messagesUserId' => $messagesUserId,
        'contactsList' => $contactsList,
        'newDialogUserData' => $newDialogUserData,
        'messagesHistory' => $messagesHistory,
        'dialogId' => $dialogId,
        'validationError' => $validationError
    ]);
$feedPage = include_template('layout.php',
    [
        'pageContent' => $pageContent,
        'titleName' => 'readme: личные сообщения',
        'userData' => $userData,
        'is_auth' => AUTH
    ]);

print_r($feedPage);
