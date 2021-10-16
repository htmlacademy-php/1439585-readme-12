<?php

require_once('helpers.php');

function cutCardContent(string $cardContent, int $lenght = 300): string
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

/**переделанная функция(бывшая showPostDate) для показа даты  в относительном формате
 * подходит как для показа даты у поста, так и для показа, как давно пользователь на сайте зарегистрирован
 */
function showDate(string $dateAdd): array
{
    $tmstPostsDate = strtotime($dateAdd);
    $titleDate = date('Y-m-d H:i', $tmstPostsDate);
    $currentDate = date_format(date_create(), 'U');
    $dateDiffer = $currentDate - $tmstPostsDate;

    switch (true) {
        case ($dateDiffer < 60 * 60):
            $humanTime = ceil($dateDiffer / 60);
            $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'минута', 'минуты', 'минут');
            break;
        case ($dateDiffer >= 60 * 60 && $dateDiffer < 60 * 60 * 24):
            $humanTime = ceil($dateDiffer / (60 * 60));
            $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'час', 'часа', 'часов');
            break;
        case ($dateDiffer >= 60 * 60 * 24 && $dateDiffer < 60 * 60 * 24 * 7):
            $humanTime = ceil($dateDiffer / (60 * 60 * 24));
            $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'день', 'дня', 'дней');
            break;
        case ($dateDiffer >= 60 * 60 * 24 * 7 && $dateDiffer < 60 * 60 * 24 * 7 * 5):
            $humanTime = ceil($dateDiffer / (60 * 60 * 24 * 7));
            $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'неделя', 'недели', 'недель');
            break;
        case ($dateDiffer >= 60 * 60 * 24 * 7 * 5 && $dateDiffer < 60 * 60 * 24 * 7 * 52):
            $humanTime = ceil($dateDiffer / (60 * 60 * 24 * 7 * 5));
            $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'месяц', 'месяца', 'месяцев');
            break;
        case ($dateDiffer >= 60 * 60 * 24 * 7 * 52):
            $humanTime = ceil($dateDiffer / (60 * 60 * 24 * 365 + 172800));
            $relativeTime =  "{$humanTime} " . get_noun_plural_form($humanTime, 'год', 'года', 'лет');
            break;
    }
    return ['datetime' => $dateAdd, 'title' => $titleDate, 'relative_time' => $relativeTime];
}

function fetchAll($sqlQuery, $connect)
{
    $resultSqlQuery = $connect->query($sqlQuery);
    if ($resultSqlQuery) {
        return $resultSqlQuery->fetch_all(MYSQLI_ASSOC);
    }
    echo sprintf("Ошибка получения данных. %d %s", $connect->errno, $connect->error);
    die;
}

/**получение массива данных из подготовленного sql-выражения с использованием db_get_prepare_stmt
 * из helpers как для выражения с 1 плейсхолдером, так и с несколькими
 */
function fetchPrepareStmt($connect, $sqlQuery, $val)
{
    $stmt = db_get_prepare_stmt($connect, $sqlQuery, (array) $val);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $stmtResult = $result->fetch_all(MYSQLI_ASSOC);
        return $stmtResult;
    }
    echo sprintf("Ошибка получения данных. %d %s", $connect->errno, $connect->error);
    die;
}

/**подсчет рейтинга */
function ratingCount($connect, $sqlQuery, $cards): array
{
    $ratings = [];
    $k = 0;

    foreach ($cards as $card) {
        $stmtResult = fetchPrepareStmt($connect, $sqlQuery, $card['id']);
        $ratings[$k]['post_id'] = $card['id'];
        $ratings[$k]['likes'] = $stmtResult[0]['likes'];
        $ratings[$k]['count_comment'] = $stmtResult[0]['count_comment'];
        $k++;
    }
    return $ratings;
}
