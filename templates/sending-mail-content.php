<?php

//Тема и тело письма в зависимости от исполняемого скрипта:
switch ($_SERVER['SCRIPT_NAME']) {
    case ('/add.php'):
        $messageSubject = "Новая публикация от пользователя " . $userData['login'];
        $messageBody = "Здравствуйте, " . $recipient['login'] . ". Пользователь " . $userData['login'] . " только что опубликовал новую запись „" . htmlspecialchars($_POST['heading']) . "“. Посмотрите её на странице пользователя: " . $_SERVER['HTTP_HOST'] . "/profile.php?profile_id=" . $userData['id'];
        break;
    case ('/subscribe.php'):
        $messageSubject = "У вас новый подписчик";
        $messageBody = "Здравствуйте, " . $recipientData['login'] . ". На вас подписался новый пользователь $userLogin. Вот ссылка на его профиль: " . $_SERVER['HTTP_HOST'] . "/profile.php?profile_id=$userId";
        break;
}
