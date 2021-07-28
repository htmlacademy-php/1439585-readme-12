<?php

require_once('helpers.php');

function cutCardContent($cardContent, $lenght = 300)
{
    $words = explode(' ', $cardContent);
    $count = 0;
    foreach ($words as $key => $nextWord) {
        $count = $count + (iconv_strlen($nextWord));
        if ($count > $lenght) {
            break;
        }
    }
    if ($count < $lenght) {
        return htmlspecialchars(implode(' ', $words));
    }
    return htmlspecialchars(implode(' ', array_slice($words, 0, $key))) . '...' . '<p> <a class="post-text__more-link" href="#">Читать далее</a>';
}

function showPostDate($key, $dateAdd)
{
    $tmstPostsDate = strtotime($dateAdd);
    $titleDate = date('Y-m-d H:i', $tmstPostsDate);
    $currentDate = date_format(date_create(), 'U');
    $dateDiffer = $currentDate - $tmstPostsDate;

    if ($dateDiffer < 60 * 60) {
        $humanTime = ceil($dateDiffer / 60);
        $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'минута', 'минуты', 'минут') . " назад";
    } elseif ($dateDiffer >= 60 * 60 && $dateDiffer < 60 * 60 * 24) {
        $humanTime = ceil($dateDiffer / (60 * 60));
        $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'час', 'часа', 'часов') . " назад";
    } elseif ($dateDiffer >= 60 * 60 * 24 && $dateDiffer < 60 * 60 * 24 * 7) {
        $humanTime = ceil($dateDiffer / (60 * 60 * 24));
        $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'день', 'дня', 'дней') . " назад";
    } elseif ($dateDiffer >= 60 * 60 * 24 * 7 && $dateDiffer < 60 * 60 * 24 * 7 * 5) {
        $humanTime = ceil($dateDiffer / (60 * 60 * 24 * 7));
        $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'неделя', 'недели', 'недель') . " назад";
    } else {
        $humanTime = ceil($dateDiffer / (60 * 60 * 24 * 7 * 5));
        $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'месяц', 'месяца', 'месяцев') . " назад";
    }
    return ['datetime' => $dateAdd, 'title' => $titleDate, 'relative_time' => $relativeTime];
}

function showError($link)
{
    $form = "Ошибка получения данных. %d %s";
    return sprintf($form, $link->errno, $link->error);
}
