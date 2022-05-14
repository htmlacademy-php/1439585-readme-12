<?php

require_once('helpers.php');
require_once('config/site_config.php');

/**
 * Обрезает текст до указанной длины и добавляет в конце троеточие и ссылку на полный текст.
 * @param string $cardContent Текст для обрезания
 * @param int $postId Id поста для формирования ссылки на него
 * @param int $length Длина строки, установленная по умолчанию
 * @return string
 */
function cutCardContent(string $cardContent, int $postId, int $length = 300): string
{
    $words = explode(' ', $cardContent);
    $count = 0;
    foreach ($words as $key => $nextWord) {
        $count = $count + (iconv_strlen($nextWord));
        if ($count > $length) {
            break;
        }
    }
    if ($count < $length) {
        return htmlspecialchars(implode(' ', $words));
    }

    return htmlspecialchars(implode(' ',
            array_slice($words, 0,
                $key))) . '...' . '<p> <a class="post-text__more-link" href="post.php?post_id=' . $postId . '">Читать далее</a>';
}

/**
 * Обрезает превью ссылки для поста-ссылки на странице популярного.
 * @param string $siteLink Ссылка для обрезания
 * @return string
 */
function cutPreviewLink(string $siteLink): string
{
    $siteLink = htmlspecialchars($siteLink);

    return mb_substr($siteLink, 0, 30);
}

/**
 * Корректировка URL-ссылки, в зависимости от наличия http:// в ней.
 * @param string $siteLink Ссылка для проверки
 * @return string
 */
function correctSiteUrl(string $siteLink): string
{
    $needleChars = 'http';

    if (strpos($siteLink, $needleChars) === false) {
        $siteLink = 'http://' . $siteLink;
    }

    return $siteLink;
}

/**
 * Возвращает массив, содержащий:
 * datetime - дата добавления на сайт; title - время в формате "YYYY-MM-DD HH-MM"; relative_time - форму относительного времени.
 * Подходит как для показа даты добавления поста, так и для показа, как давно пользователь был зарегистрирован.
 * @param string $dateAdd Дата, которую нужно обработать
 * @return array
 */
function showDate(string $dateAdd): array
{
    $relativeTime = '';
    $tmstPostsDate = strtotime($dateAdd);
    $titleDate = date('Y-m-d H:i', $tmstPostsDate);
    $currentDate = date_format(date_create(), 'U');
    $dateDiffer = $currentDate - $tmstPostsDate;

    switch (true) {
        case ($dateDiffer < 60 * 60):
            $humanTime = ceil($dateDiffer / 60);
            $relativeTime = "$humanTime " . get_noun_plural_form($humanTime, 'минута', 'минуты', 'минут');
            break;
        case ($dateDiffer >= 60 * 60 && $dateDiffer < 60 * 60 * 24):
            $humanTime = ceil($dateDiffer / (60 * 60));
            $relativeTime = "$humanTime " . get_noun_plural_form($humanTime, 'час', 'часа', 'часов');
            break;
        case ($dateDiffer >= 60 * 60 * 24 && $dateDiffer < 60 * 60 * 24 * 7):
            $humanTime = ceil($dateDiffer / (60 * 60 * 24));
            $relativeTime = "$humanTime " . get_noun_plural_form($humanTime, 'день', 'дня', 'дней');
            break;
        case ($dateDiffer >= 60 * 60 * 24 * 7 && $dateDiffer < 60 * 60 * 24 * 7 * 5):
            $humanTime = ceil($dateDiffer / (60 * 60 * 24 * 7));
            $relativeTime = "$humanTime " . get_noun_plural_form($humanTime, 'неделя', 'недели', 'недель');
            break;
        case ($dateDiffer >= 60 * 60 * 24 * 7 * 5 && $dateDiffer < 60 * 60 * 24 * 7 * 52):
            $humanTime = ceil($dateDiffer / (60 * 60 * 24 * 7 * 5));
            $relativeTime = "$humanTime " . get_noun_plural_form($humanTime, 'месяц', 'месяца', 'месяцев');
            break;
        case ($dateDiffer >= 60 * 60 * 24 * 7 * 52):
            $humanTime = ceil($dateDiffer / (60 * 60 * 24 * 365 + 172800));
            $relativeTime = "$humanTime " . get_noun_plural_form($humanTime, 'год', 'года', 'лет');
            break;
    }

    return ['datetime' => $dateAdd, 'title' => $titleDate, 'relative_time' => $relativeTime];
}

/**
 * Выполнение запроса к БД и извлечение результата в ассоциативный массив.
 * @param $connect mysqli Ресурс соединения
 * @param string $sqlQuery SQL запрос с данными, которые нужно получить из БД
 * @return array
 */
function fetchAll($connect, string $sqlQuery): array
{
    $resultSqlQuery = $connect->query($sqlQuery);

    if ($resultSqlQuery) {
        return $resultSqlQuery->fetch_all(MYSQLI_ASSOC);
    }

    echo sprintf("Ошибка получения данных. %d %s", $connect->errno, $connect->error);
    die;
}

/**
 * Возвращает двумерный массив, содержащий ассоциативный массив данных из подготовленного sql-выражения
 * как для выражения с 1 плейсхолдером, так и с несколькими.
 * @param $connect mysqli Ресурс соединения
 * @param string $sqlQuery SQL запрос с плейсхолдерами
 * @param mixed $val Значения для вставки вместо плейсхолдеров
 * @return array
 */
function fetchAllPrepareStmt($connect, string $sqlQuery, $val): array
{
    $stmt = db_get_prepare_stmt($connect, $sqlQuery, (array)$val);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    echo sprintf("Ошибка получения данных. %d %s", $connect->errno, $connect->error);
    die;
}

/**
 * Возвращает одномерный ассоциативный массив данных из подготовленного sql-выражения
 * как для выражения с 1 плейсхолдером, так и с несколькими.
 * @param $connect mysqli Ресурс соединения
 * @param string $sqlQuery SQL запрос с плейсхолдерами
 * @param mixed $val Значения для вставки вместо плейсхолдеров
 * @return void
 */
function fetchArrayPrepareStmt($connect, string $sqlQuery, $val)
{
    $stmt = db_get_prepare_stmt($connect, $sqlQuery, (array)$val);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    echo sprintf("Ошибка получения данных. %d %s", $connect->errno, $connect->error);
    die;
}

/**
 * Выполнение запроса к БД и извлечение результата в одномерный массив.
 * @param $connect mysqli Ресурс соединения
 * @param string $sqlQuery SQL запрос с данными, которые нужно получить из БД
 * @return array
 */
function fetchResult($connect, $sqlQuery): array
{
    $resultSqlQuery = $connect->query($sqlQuery);

    if ($resultSqlQuery) {
        return $resultSqlQuery->fetch_assoc();
    }

    echo sprintf("Ошибка получения данных. %d %s", $connect->errno, $connect->error);
    die;
}

/**
 * Выполнение запроса к БД с подготовленным выражением
 * @param $connect mysqli Ресурс соединения
 * @param string $sqlQuery
 * @param array $data Данные для вставки вместо плейсхолдеров
 * @return void
 */
function executePrepareStmt($connect, string $sqlQuery, array $data)
{
    $stmt = db_get_prepare_stmt($connect, $sqlQuery, $data);
    mysqli_stmt_execute($stmt);

    if (mysqli_errno($connect) > 0) {
        echo sprintf("Ошибка получения данных. %d %s", mysqli_errno($connect), mysqli_error($connect));
        die;
    }
}

/**
 * Подготовка данных для записи в таблицу с постами.
 * Из массива $_POST копируем нужные поля и помещаем их со значениями в результирующий массив.
 * @param array $fields Список полей для заполнения
 * @return array
 */
function prepareData(array $fields): array
{
    $resultData = [];

    foreach ($_POST as $key => $data) {
        if (array_key_exists($key, $fields)) {
            $resultData[$key] .= filter_var(trim($data), FILTER_UNSAFE_RAW);
        }
    }

    return $resultData;
}

/**
 * Валидация заполненности всех обязательных полей.
 * Если все поля заполнены, вернется пустой массив, иначе ассоциативный массив со списком ошибок.
 * @param array $allFields Массив всех полей, который необходимо проверить
 * @param array $requiredFields Обязательные поля
 * @return array
 */
function validateEmptyField(array $allFields, array $requiredFields): array
{
    $errors = [];

    foreach ($allFields as $keyAllField => $field) {
        $trimField = trim($_POST[$keyAllField]);
        $isKeyExist = array_key_exists($keyAllField, $requiredFields);
        if (empty($trimField) && ($isKeyExist == true)) {
            $errors[$keyAllField] = $requiredFields[$keyAllField] . " Это поле должно быть заполнено.";
        }
    }

    return $errors;
}

/**
 * Преобразовывает строку с хэштегами в массив, разделяя по пробелам, для дальнейшей записи в БД.
 * @param string $hashtags Хэштеги из нового поста
 * @return array
 */
function prepareTags(string $hashtags): array
{
    $tags = [];
    $trimHashtags = trim($_POST[$hashtags]);

    if (!empty($trimHashtags)) {
        $tags = explode(' ', $trimHashtags);
        foreach ($tags as $key => $tag) {
            if ($tag == null) {
                unset($tags[$key]);
            }
        }
    }

    return $tags;
}

/**
 * Валидация видео-ссылки.
 * @param string $videoLink Ссылка на видео из youtube
 * @return null|string Вернет null в случае отсутствия ошибки, иначе описание ошибки
 */
function validateVideo(string $videoLink)
{
    $error = null;

    $filteredVideoLink = filter_var($videoLink, FILTER_VALIDATE_URL);

    if (check_youtube_url($filteredVideoLink) == 0) {
        $error = check_youtube_url($filteredVideoLink);
    }

    return $error;
}

/**
 * Проверяет, было ли добавлено фото хоть в одном поле.
 * @return bool Вернет false в случае отсутствия ошибки.
 */
function validateEmptyPicture(): bool
{
    if (empty(($_FILES['userpic-file-photo']['name'])) && empty($_POST['photo-url'])) {
        return false;
    }

    return true;
}

/**
 * Валидация картинки на соответствие разрешенному mime-типу.
 * @param string $keyName Ключ для обращения к элементам массива $_FILES
 * @return bool Возвращает false, если файл не является картинкой или не соответствует mime-типу
 */
function validatePictureFromUser(string $keyName): bool
{
    $tmpName = $_FILES[$keyName]['tmp_name'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($finfo, $tmpName);
    finfo_close($finfo);

    if (in_array($fileType, ALLOWED_MIME_TYPES)) {
        return true;
    }

    return false;
}

/**
 * Сохранение картинки, загруженной юзером.
 * @param string $keyName Ключ для обращения к элементам массива $_FILES
 * @return string Имя сохраненного файла
 */
function savePictureFromUser(string $keyName): string
{
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
    $tmpName = $_FILES[$keyName]['tmp_name'];
    $fileName = $_FILES[$keyName]['name'];
    $newFileName = uniqid() . $fileName;

    move_uploaded_file($tmpName, $filePath . $newFileName);

    return $newFileName;
}

/**
 * Валидация картинки по URL.
 * Пример ссылки url корректного формата: https://pbs.twimg.com/media/EP63Du7X4AE7c3B.jpg
 * @param string $keyName Ключ для обращения к элементам массиву $_POST
 * @return bool Возвращает false, если не корректны ссылка или mime-тип
 */
function validatePictureUrl(string $keyName): bool
{
    $siteUrl = filter_var($_POST[$keyName], FILTER_VALIDATE_URL);

    /*Если ссылка не прошла FILTER_VALIDATE_URL, return false */
    if ($siteUrl == false) {
        return false;
    }

    /*Если заголовок не содержит content-type соответсвующий формату image, return false */
    $siteHeaders = get_headers($siteUrl);
    if (strstr(implode($siteHeaders), 'Content-Type: image')) {
        $imageType = exif_imagetype($siteUrl);
    } else {
        return false;
    }

    /* Соответствие mime-типу */
    if (in_array($imageType, [1, 2, 3])) {
        return true;
    } else {
        return false;
    }
}

/**
 * Сохранение картинки по указанному URL.
 * @param string $keyName Ключ для обращения к элементам массиву $_POST
 * @return string Имя сохраненного файла
 */
function savePictureByUrl(string $keyName): string
{
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
    $siteUrl = $_POST[$keyName];
    $imageUrl = file_get_contents($siteUrl);

    $type = pathinfo($siteUrl, PATHINFO_EXTENSION);
    $name = pathinfo($siteUrl, PATHINFO_FILENAME);
    $newFileName = uniqid() . $name . '.' . $type;
    file_put_contents($filePath . $newFileName, $imageUrl);

    return $newFileName;
}

/**
 * Возвращает полный путь к файлу-картинке на основе его названия.
 * @param string $fileName Название файла для сохранения
 * @return string Путь к файлу в папке
 */
function getPicturePath(string $fileName): string
{
    return DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $fileName;
}

/**
 * Проверяет, корректна ли указанная ссылка.
 * @param string $link Проверяемая ссылка
 * @return bool Вернет false в случае отсутствия ошибки
 */
function validateUrl(string $link): bool
{
    $checkedLink = filter_var($link, FILTER_VALIDATE_URL);

    if ($checkedLink == null) {
        return false;
    }

    return true;
}

/**
 * Добавление основного контента поста в таблицу posts.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param string $sql SQL запрос для добавления поста с плейсхолдерами
 * @param string $imageName Опционально; ссылка на полный путь к файлу-изображения
 * @return void
 */
function addMainPostContent($connect, array $requiredFields, string $sql, string $imageName = null)
{
    $AddPostContent = prepareData($requiredFields);

    /* На случай добавления поста-фотографии добавляем отдельно данное поле */
    if (!empty($imageName)) {
        $AddPostContent['image_path'] = $imageName;
    }

    $stmt = db_get_prepare_stmt($connect, $sql, $AddPostContent);
    mysqli_stmt_execute($stmt);
}

/**
 * Добавление хэштегов нового поста в таблицу хештегов и связей.
 * @param $connect mysqli Ресурс соединения
 * @param int $post_id Id поста, к которому относятся данные теги
 * @param array $hashtags Массив тегов на добавление
 * @return void
 */
function addPostsHashtags($connect, int $post_id, array $hashtags)
{
    $sqlHashtags = "INSERT INTO hashtags (hashtag_content) VALUES (?);";
    $sqlPostsHashtags = "INSERT INTO posts_hashtags (post_id, hashtag_id) VALUES (?, ?);";

    foreach ($hashtags as $tags) {

        $isTagExsist = fetchAllPrepareStmt($connect, "SELECT * FROM hashtags WHERE hashtag_content = ?;", $tags);

        if (!empty($isTagExsist)) {
            $tags_id = $isTagExsist[0]['id'];
        } else {
            $stmtTags = db_get_prepare_stmt($connect, $sqlHashtags, (array)$tags);
            mysqli_stmt_execute($stmtTags);
            $tags_id = mysqli_insert_id($connect);
        }

        $intPostId = $post_id;
        $intTagsId = (int)$tags_id;
        $stmtPH = db_get_prepare_stmt($connect, $sqlPostsHashtags, ["$intPostId", "$intTagsId"]);
        mysqli_stmt_execute($stmtPH);
    }
}

/**
 * Добавление в БД нового текстового поста.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param int $userId Id юзера, добавляющего пост
 * @param string $categoryId Id категории типа поста
 * @return void
 */
function addNewTextPost($connect, array $requiredFields, int $userId, string $categoryId)
{
    $sql = "INSERT INTO posts (author_id, category_id, date_add, title, content ) VALUES ($userId, $categoryId, NOW(), ?, ?);";

    addMainPostContent($connect, $requiredFields, $sql);
}

/**
 * Добавление в БД нового поста-цитаты.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param int $userId Id юзера, добавляющего пост
 * @param string $categoryId Id категории типа поста
 * @return void
 */
function addNewQuotePost($connect, array $requiredFields, int $userId, string $categoryId)
{
    $sql = "INSERT INTO posts (author_id, category_id, date_add, title, content, quote_author) VALUES ($userId, $categoryId, NOW(), ?, ?, ?);";

    addMainPostContent($connect, $requiredFields, $sql);
}

/**
 * Добавление в БД нового поста с фото.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param int $userId Id юзера, добавляющего пост
 * @param string $categoryId Id категории типа поста
 * @param string $imageName Ссылка на полный путь к файлу-изображения
 * @return void
 */
function addNewPhotoPost($connect, array $requiredFields, int $userId, string $categoryId, $imageName)
{
    $sql = "INSERT INTO posts (author_id, category_id, date_add, title, image_path) VALUES ($userId, $categoryId, NOW(), ?, ?);";

    addMainPostContent($connect, $requiredFields, $sql, $imageName);
}

/**
 * Добавление в БД нового видео-поста.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param int $userId Id юзера, добавляющего пост
 * @param string $categoryId Id категории типа поста
 * @return void
 */
function addNewVideoPost($connect, array $requiredFields, int $userId, string $categoryId)
{
    $sql = "INSERT INTO posts (author_id, category_id, date_add, title, video_link) VALUES ($userId, $categoryId, NOW(), ?, ?);";

    addMainPostContent($connect, $requiredFields, $sql);
}

/**
 * Добавление в БД нового поста-ссылки.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param int $userId Id юзера, добавляющего пост
 * @param string $categoryId Id категории типа поста
 * @return void
 */
function addNewLinkPost($connect, array $requiredFields, int $userId, string $categoryId)
{
    $sql = "INSERT INTO posts (author_id, category_id, date_add, title, website_link) VALUES ($userId, $categoryId, NOW(), ?, ?);";

    addMainPostContent($connect, $requiredFields, $sql);
}

/**
 * Получение списка всех категорий.
 * @param $connect mysqli Ресурс соединения
 * @return array
 */
function getCategoryList($connect): array
{
    $sqlCategories = "SELECT * FROM categories;";
    return fetchAll($connect, $sqlCategories);
}

/**
 * Подсчет общего количества всех постов по всем категориям
 * @param $connect mysqli Ресурс соединения
 * @return int
 */
function countAllPosts($connect): int
{
    $sql = "SELECT COUNT(id) AS count_posts FROM posts;";

    return fetchResult($connect, $sql)['count_posts'];
}

/**
 * Подсчет общего количества постов в выбранной категории
 * @param $connect mysqli Ресурс соединения
 * @param int $categoryId Id выбранной пользователем категории
 * @return int
 */
function countPostsByCategory($connect, int $categoryId): int
{
    $sql = "SELECT COUNT(id) AS count_posts FROM posts WHERE category_id = ?;";

    return fetchArrayPrepareStmt($connect, $sql, $categoryId)['count_posts'];
}

/**
 * Получение всех существующих карточек постов с рейтингом, отсортированных по популярности.
 * @param $connect mysqli Ресурс соединения
 * @param int $limit Ограничение количества записей за запрос
 * @param int $offset Смещение для выборки
 * @param string $sortingBy Параметр сортировки(по какому полю из БД)
 * @param string $sortOrder Порядок сортировки
 * @return array
 */
function getAllCardsContent($connect, int $limit, int $offset, string $sortingBy, string $sortOrder): array
{
    $sqlCardsContent = "SELECT  posts.id AS 'post_id',
                                title,
                                category_id,
                                posts.content,
                                quote_author,
                                image_path,
                                video_link,
                                website_link,
                                posts.date_add,
                                show_count,
                                login,
                                users.id AS 'user_id',
                                avatar,
                                COUNT(DISTINCT likes.id) AS 'likes_count',
                                COUNT(DISTINCT comments.id) AS 'comment_count'
                        FROM posts
                            JOIN users ON users.id = posts.author_id
                            LEFT JOIN likes ON posts.id = likes.post_id
                            LEFT JOIN comments ON comments.post_id = posts.id
                        GROUP BY posts.id
                        ORDER BY $sortingBy $sortOrder
                        LIMIT $limit OFFSET $offset;";

    return fetchAll($connect, $sqlCardsContent);
}

/**
 * Получение карточек постов с рейтингом по конкретной категории, отсортированных по популярности.
 * @param $connect mysqli Ресурс соединения
 * @param int $categoryId Id категории, по которой происходит выборка
 * @param int $limit Ограничение количества записей за запрос
 * @param int $offset Смещение для выборки
 * @param string $sortingBy Параметр сортировки(по какому полю из БД)
 * @param string $sortOrder Порядок сортировки
 * @return array
 */
function getCardsByCategory(
    $connect,
    int $categoryId,
    int $limit,
    int $offset,
    string $sortingBy,
    string $sortOrder
): array {
    $sqlCardsOnCategory = "SELECT   posts.id AS 'post_id',
                                    title,
                                    category_id,
                                    posts.content,
                                    quote_author,
                                    image_path,
                                    video_link,
                                    website_link,
                                    posts.date_add,
                                    show_count,
                                    login,
                                    users.id AS 'user_id',
                                    avatar,
                                    COUNT(DISTINCT likes.id) AS 'likes_count',
                                    COUNT(DISTINCT comments.id) AS 'comment_count'
                            FROM posts
                                JOIN users ON users.id = posts.author_id
                                LEFT JOIN likes ON posts.id = likes.post_id
                                LEFT JOIN comments ON comments.post_id = posts.id
                            WHERE posts.category_id = $categoryId
                            GROUP BY posts.id
                            ORDER BY $sortingBy $sortOrder
                            LIMIT $limit OFFSET $offset;";

    return fetchAll($connect, $sqlCardsOnCategory);
}

/**
 * Переадресация пользователя на нужную нам страницу.
 * @param string $page Путь на страницу для перенаправления
 * @return void
 */
function redirectOnPage(string $page)
{
    header("Location: /$page");
    exit();
}

/**
 * Проверка существования email'а пользователя в БД.
 * @param $connect mysqli Ресурс соединения
 * @param string $userEmail Email нового пользователя
 * @return bool False, если такой пользователь в БД не существует
 */
function checkEmailExists($connect, string $userEmail): bool
{
    $sqlQuery = 'SELECT email FROM users WHERE email = ?;';
    $userExists = fetchAllPrepareStmt($connect, $sqlQuery, $userEmail);

    if (!empty($userExists)) {
        return true;
    }

    return false;
}

/**
 * Проверяет, является ли введенный email корректным.
 * @param string $userEmail email нового пользователя
 * @return bool False если email не корректен
 */
function validateEmail(string $userEmail): bool
{
    if (filter_var($userEmail, FILTER_VALIDATE_EMAIL) == false) {
        return false;
    }

    return true;
}

/**
 * Проверка на соответствие пароля условиям ТЗ:
 * пароль должен быть не менее 6 символов, а также содержать в себе цифры и латинские буквы, в верхнем и нижнем регистре.
 * Условия, по которым пароль должен соответствовать взяты из видео в ТЗ на сайте: https://www.youtube.com/watch?v=NexC8QPTNpM
 * @param string $password Проверяемый пароль
 * @return bool False, если не пароль не соответствует требованиям
 */
function isPasswordCorrect(string $password): bool
{
    $passwordLength = iconv_strlen($password, 'UTF-8');
    if ($passwordLength < 6) {
        return false;
    }

    // Убеждаемся, что пароль содержит буквы и только латинского алфавита
    if (!preg_match("/[a-z]/i", $password)) {
        return false;
    }

    // Содержит цифры
    if (!preg_match("/[0-9]/i", $password)) {
        return false;
    }

    // Перед проверкой, что строка не состоит только нижнего или верхнего регистра, нужно удалить из нее цифры
    $passwordWithoutNumbers = preg_replace("/[^a-z]/i", '', $password);

    // Если пароль только в верхнем регистре или только в нижнем return false
    if (ctype_upper($passwordWithoutNumbers) === true || ctype_lower($passwordWithoutNumbers) === true) {
        return false;
    }

    return true;
}

/**
 * Проверка на совпадение паролей.
 * @param string $password Пароль
 * @param string $repeatPassword Повтор пароля
 * @return bool False в случае несовпадения паролей
 */
function checkPasswordMatch(string $password, string $repeatPassword): bool
{
    if (strcmp($password, $repeatPassword) !== 0) {
        return false;
    }

    return true;
}

/**
 * Добавление нового пользователя в таблицу users.
 * @param $connect mysqli Ресурс соединения
 * @param array $userData Данные для добавления
 * @return void
 */
function addNewUser($connect, array $userData)
{
    $sqlQuery = "INSERT INTO users (date_registration, email, login, password) VALUES (NOW(), ?, ?, ?);";

    executePrepareStmt($connect, $sqlQuery, $userData);
}

/**
 * Добавление ссылки на аватар в таблицу users.
 * @param $connect mysqli Ресурс соединения
 * @param string $avatarPath Путь к аватару
 * @param int $userId Id последнего добавленного пользователя
 * @return void
 */
function addUserAvatar($connect, string $avatarPath, int $userId)
{
    $sqlQuery = 'UPDATE users SET avatar = ? WHERE id = ?;';

    executePrepareStmt($connect, $sqlQuery, [$avatarPath, $userId]);
}

/**
 * Проверка на существование сессии с пользователем;
 * если пользователь не авторизован, отправляем на главную.
 * @return void
 */
function isUserLoggedIn()
{
    if (empty($_SESSION['user'])) {
        redirectOnPage('index.php');
    }
}

/**
 * Получение данных для идентификации пользователя на сайте по email.
 * @param $connect mysqli Ресурс соединения
 * @param string $email Email пользователя
 * @return array
 */
function getUserAuthorizationData($connect, string $email): array
{
    $sql = "SELECT * FROM users WHERE email = ?;";

    return fetchArrayPrepareStmt($connect, $sql, $email);
}

/**
 * Получение списка постов с сортировкой по дате добавления; выборка по авторам, на которых подписан авторизованный пользователь.
 * @param $connect mysqli Ресурс соединения
 * @param int $subscriberId Id авторизованного пользователя
 * @return array Массив постов с данными автора поста
 */
function getSubscribesPosts($connect, int $subscriberId): array
{
    $sql = "SELECT posts.id AS 'post_id',
                   category_id,
                   title,
                   posts.content,
                   quote_author,
                   image_path,
                   video_link,
                   website_link,
                   posts.date_add,
                   show_count,
                   users.id AS 'user_id',
                   login,
                   avatar,
                   COUNT(DISTINCT likes.id) AS 'likes_count',
				   COUNT(DISTINCT comments.id) AS 'comment_count',
                   COUNT(DISTINCT reposts.id) AS 'repost_count'
            FROM posts
                JOIN users ON users.id = posts.author_id
                LEFT JOIN likes ON posts.id = likes.post_id
                LEFT JOIN comments ON comments.post_id = posts.id
                LEFT JOIN reposts ON reposts.original_post_id = posts.id
            WHERE posts.author_id
             	IN (SELECT author_id
                          FROM subscribes
                          WHERE subscriber_id = ?)
                OR posts.author_id = ?
            GROUP BY posts.id
            ORDER BY posts.date_add DESC;";

    return fetchAllPrepareStmt($connect, $sql, [$subscriberId, $subscriberId]);
}

/**
 * Получение списка постов с сортировкой по дате добавления в выбранной категории;
 * выборка по авторам, на которых подписан авторизованный пользователь.
 * @param $connect mysqli Ресурс соединения
 * @param int $subscriberId Id авторизованного пользователя
 * @param int $categoryId Id категории, по которой происходит выборка
 * @return array Массив постов с данными автора поста
 */
function getSubscribesPostsByCategory($connect, int $subscriberId, int $categoryId): array
{
    $sql = "SELECT posts.id AS 'post_id',
                   category_id,
                   title,
                   posts.content,
                   quote_author,
                   image_path,
                   video_link,
                   website_link,
                   posts.date_add,
                   show_count,
                   users.id AS 'user_id',
                   login,
                   avatar,
                   COUNT(DISTINCT likes.id) AS 'likes_count',
				   COUNT(DISTINCT comments.id) AS 'comment_count',
                   COUNT(DISTINCT reposts.id) AS 'repost_count'
            FROM posts
                JOIN users ON users.id = posts.author_id
                LEFT JOIN likes ON posts.id = likes.post_id
                LEFT JOIN comments ON comments.post_id = posts.id
                LEFT JOIN reposts ON reposts.original_post_id = posts.id
            WHERE (posts.author_id
                  IN (SELECT author_id
                      FROM subscribes
                      WHERE subscriber_id = ?)
                  OR posts.author_id = ?)
                  AND category_id = ?
            GROUP BY posts.id
            ORDER BY date_add DESC;";

    return fetchAllPrepareStmt($connect, $sql, [$subscriberId, $subscriberId, $categoryId]);
}

/**
 * Получение хэштегов к конкретному посту по id поста.
 * @param $connect mysqli Ресурс соединения
 * @param int $postId Id поста
 * @return array
 */
function getPostHashtags($connect, int $postId): array
{
    $sql = "SELECT hashtag_content
            FROM hashtags
                JOIN posts_hashtags ON hashtags.id = posts_hashtags.hashtag_id
                JOIN posts ON posts_hashtags.post_id = posts.id
            WHERE posts.id = ?;";

    return array_column(fetchAllPrepareStmt($connect, $sql, $postId), 'hashtag_content');
}

/**
 * Получение данных по автору, включая количество постов и подписчиков, по id автора публикации.
 * @param $connect mysqli Ресурс соединения
 * @param int $authorId Id автора публикации
 * @param int $authorizedUserId Id авторизованного пользователя
 * @return array
 */
function getUserData($connect, int $authorId, int $authorizedUserId): array
{
    $sql = "SELECT users.id,
                   date_registration,
                   email,
                   login,
                   avatar,
                   COUNT(DISTINCT subscribes.id) AS 'subscribers',
				   COUNT(DISTINCT posts.id) AS 'count_posts',
                   (SELECT EXISTS (SELECT * FROM subscribes WHERE author_id = ? AND subscriber_id = ?)) AS 'is_subscribe'
            FROM users
            LEFT JOIN subscribes ON users.id = subscribes.author_id
            LEFT JOIN posts ON users.id = posts.author_id
            WHERE users.id = ?
            GROUP BY users.id;";

    return fetchArrayPrepareStmt($connect, $sql, [$authorId, $authorizedUserId, $authorId]);
}

/**
 * Получение основного контента по содержимому поста вместе с рейтингом для страницы показа поста.
 * @param $connect mysqli Ресурс соединения
 * @param int $postId Id поста
 * @return array
 */
function getContentDataForPostPage($connect, int $postId, int $authorizedUserId): array
{
    $sql = "SELECT  posts.id AS 'post_id',
                    category_id,
                    categories.class_name AS 'category_name',
                    title,
                    posts.content,
                    quote_author,
                    image_path,
                    video_link,
                    website_link,
                    posts.date_add,
                    show_count,
                    users.id AS 'user_id',
                    date_registration,
                    email,
                    login,
                    avatar,
                    COUNT(DISTINCT likes.id) AS 'likes_count',
                    COUNT(DISTINCT comments.id) AS 'comment_count',
                    COUNT(DISTINCT subscribes.id) AS 'author_subscribers',
                    author_count_post,
                    COUNT(DISTINCT reposts.id) AS 'repost_count',
                    (SELECT EXISTS (SELECT * FROM subscribes WHERE author_id = users.id AND subscriber_id = ?)) AS 'is_subscribe'
            FROM posts
                JOIN users ON posts.author_id = users.id
                JOIN categories ON posts.category_id = categories.id
                LEFT JOIN likes ON posts.id = likes.post_id
                LEFT JOIN comments ON comments.post_id = posts.id
                LEFT JOIN subscribes ON users.id = subscribes.author_id
                LEFT JOIN reposts ON reposts.original_post_id = posts.id
                JOIN
                    (SELECT users.id AS 'users_id',
                        	COUNT(DISTINCT posts.id) AS 'author_count_post'
                     FROM users
                         LEFT JOIN posts ON posts.author_id = users.id
                     GROUP BY users.id) AS author_posts
                ON author_posts.users_id = posts.author_id
            WHERE posts.id = ?
            GROUP BY posts.id;";

    return fetchArrayPrepareStmt($connect, $sql, [$authorizedUserId, $postId]);
}


/**
 * Получение комментариев к конкретному посту по id поста.
 * @param $connect mysqli Ресурс соединения
 * @param int $postId Id поста
 * @return array
 */
function getPostComments($connect, int $postId): array
{
    $sql = "SELECT posts.id AS 'posts_id',
                   users.id AS 'comment_author',
                   login,
                   avatar,
                   comments.date_add AS 'comment_date',
                   comments.content AS 'comment'
            FROM posts
                  LEFT JOIN comments ON comments.post_id = posts.id
                  JOIN users ON users.id = comments.user_id
            WHERE posts.id = ?
            ORDER BY comments.date_add DESC;";

    return fetchAllPrepareStmt($connect, $sql, $postId);
}

/**
 * Возвращает корректную форму множественного числа подписчиков автора
 * @param int $subscribersCount Количество подписчиков
 * @return string Корректная форма множественного числа
 */
function showSubscribersCount(int $subscribersCount): string
{
    return get_noun_plural_form($subscribersCount, "подписчик", "подписчика", "подписчиков");
}

/**
 * Возвращает корректную форму множественного числа количества публикаций автора
 * @param int $postsCount Количество публикаций
 * @return string Корректная форма множественного числа
 */
function showAuthorPostsCount(int $postsCount): string
{
    return get_noun_plural_form($postsCount, "публикация", "публикации", "публикаций");
}

/**
 * Определение, какого рода поисковый запрос: из поисковой строки или поиск по хэштегу
 * по тому, что пришло через параметр GET
 * @param string $searchQuery Поисковый запрос
 * @return string tag, если был поиск по хэштегу, queryString - поисковая строка
 */
function defineTypeSearchQuery(string $searchQuery): string
{
    if (substr($searchQuery, 0, 1) === '#') {
        return 'tag';
    }
    return 'queryString';
}

/**
 * Получение постов по результатам запроса из поисковой строки.
 * @param $connect mysqli Ресурс соединения
 * @param string $searchQuery Поисковый запрос
 * @return array
 */
function getSearchQueryResult($connect, string $searchQuery): array
{
    $sql = "SELECT  posts.id AS 'post_id',
                    title,
                    posts.content,
                    quote_author,
                    image_path,
                    video_link,
                    website_link,
                    posts.date_add,
                    category_id,
                    categories.class_name AS 'category_name',
                    users.id AS 'user_id',
                    login,
                    avatar,
                    COUNT(DISTINCT likes.id) AS 'likes_count',
                    COUNT(DISTINCT comments.id) AS 'comment_count',
                    MATCH(title, posts.content) AGAINST(?)as relevance
            FROM posts
                JOIN categories ON posts.category_id = categories.id
                JOIN users ON posts.author_id = users.id
                LEFT JOIN likes ON posts.id = likes.post_id
                LEFT JOIN comments ON comments.post_id = posts.id
            WHERE MATCH(title, posts.content) AGAINST(? IN NATURAL LANGUAGE MODE)
            GROUP BY posts.id
            ORDER BY relevance DESC;";

    return fetchAllPrepareStmt($connect, $sql, [$searchQuery, $searchQuery]);
}

/**
 * Получение постов по результатам поиска по хэштегу.
 * @param $connect mysqli Ресурс соединения
 * @param string $hashtag Хэштег
 * @return array
 */
function getTagSearchResult($connect, string $hashtag): array
{
    $sql = "SELECT posts.id AS 'post_id',
                   title,
                   posts.content,
                   quote_author,
                   image_path,
                   video_link,
                   website_link,
                   posts.date_add,
                   category_id,
                   categories.class_name AS 'category_name',
                   users.id AS 'user_id',
                   login,
                   avatar,
                   COUNT(DISTINCT likes.id) AS 'likes_count',
                   COUNT(DISTINCT comments.id) AS 'comment_count'
            FROM posts
                JOIN categories ON posts.category_id = categories.id
                JOIN users ON posts.author_id = users.id
                LEFT JOIN posts_hashtags ON posts_hashtags.post_id = posts.id
                LEFT JOIN hashtags ON hashtags.id = posts_hashtags.hashtag_id
                LEFT JOIN likes ON posts.id = likes.post_id
                LEFT JOIN comments ON comments.post_id = posts.id
            WHERE MATCH(hashtag_content) AGAINST(? IN NATURAL LANGUAGE MODE)
            GROUP BY posts.id
            ORDER BY posts.date_add DESC;";

    return fetchAllPrepareStmt($connect, $sql, $hashtag);
}

/**
 * Проверка существования поста в БД.
 * @param $connect mysqli Ресурс соединения
 * @param int $postId Id поста, который надо проверить на существование
 * @return bool false, если такого поста не существует
 */
function isPostExists($connect, int $postId): bool
{
    $sql = "SELECT * from posts WHERE id = ?;";

    if (!empty(fetchArrayPrepareStmt($connect, $sql, $postId))) {
        return true;
    }

    return false;
}

/**
 * Обновление количества просмотров у поста.
 * @param $connect mysqli Ресурс соединения
 * @param int $postId Id поста
 * @return void
 */
function updateShowCount($connect, int $postId)
{
    $sql = "UPDATE posts SET show_count = show_count + 1 WHERE id = ?;";

    executePrepareStmt($connect, $sql, [$postId]);
}

/**
 * Проверка существования лайка к посту от пользователя в БД.
 * @param $connect mysqli Ресурс соединения
 * @param int $userId Id пользователя, ставящего лайк
 * @param int $postId Id поста
 * @return bool false, если такой связи не существует
 */
function isLikeExists($connect, int $userId, int $postId): bool
{
    $sql = "SELECT * from likes WHERE user_id = ? AND post_id = ?;";

    if (!empty(fetchArrayPrepareStmt($connect, $sql, [$userId, $postId]))) {
        return true;
    }

    return false;
}

/**
 * Добавление лайка к посту.
 * @param $connect mysqli Ресурс соединения
 * @param int $userId Id текущего авторизованного пользователя
 * @param int $postId Id поста, которому надо добавить лайк
 * @return void
 */
function addLikeToPost($connect, int $userId, int $postId)
{
    $sql = "INSERT INTO likes SET user_id = ?, post_id = ?;";

    executePrepareStmt($connect, $sql, [$userId, $postId]);
}

/**
 * Проверка длины комментария.
 * @param string $comment Содержимое комментария
 * @return bool false, если длина комментария меньше 4
 */
function validateCommentLength(string $comment): bool
{
    if (iconv_strlen(trim($comment)) < 4) {
        return false;
    }

    return true;
}

/**
 * Добавление комментария к посту.
 * @param $connect mysqli Ресурс соединения
 * @param int $postId Id поста, к которому добавляется комментарий
 * @param int $userId Id пользователя, оставившего комментарий
 * @return void
 */
function addCommentToPost($connect, int $postId, int $userId, string $comment)
{
    $sql = "INSERT INTO comments (user_id, post_id, date_add, content) VALUES (?, ?, NOW(), ?);";

    executePrepareStmt($connect, $sql, [$userId, $postId, $comment]);
}

/**
 * Получение списка постов, добавленных пользователем с сортировкой по дате добавления.
 * @param $connect mysqli Ресурс соединения
 * @param int $userId Id пользователя
 * @return array
 */
function getUsersPosts($connect, int $userId): array
{
    $sql = "SELECT  posts.id AS 'post_id',
                    posts.category_id,
                    title,
                    posts.content,
                    quote_author,
                    image_path,
                    video_link,
                    website_link,
                    posts.date_add,
                    categories.name,
                    categories.class_name,
                    is_repost,
                    posts.original_author_id,
                    users.login AS 'original_author_login',
                    users.avatar AS 'original_author_avatar',
                    COUNT(DISTINCT likes.id) AS 'likes_count',
                    COUNT(DISTINCT reposts.id) AS 'repost_count'
            FROM posts
                JOIN categories ON posts.category_id = categories.id
                LEFT JOIN likes ON posts.id = likes.post_id
                LEFT JOIN reposts ON reposts.original_post_id = posts.id
                LEFT JOIN users ON users.id = posts.original_author_id
            WHERE posts.author_id = ?
            GROUP BY posts.id
            ORDER BY date_add DESC;";

    return fetchAllPrepareStmt($connect, $sql, $userId);
}

/**
 * Получение списка лайков ко всем постам пользователя для страницы профиля пользователя.
 * @param $connect mysqli Ресурс соединения
 * @param int $userId Id пользователя, чья страница профиля открыта
 * @return array
 */
function getLikesForUserProfilePage($connect, int $userId): array
{
    $sql = "SELECT  likes.user_id AS 'like_user_id',
                    likes.post_id AS 'like_post_id',
                    likes.date_add,
                    likes.id AS 'like_id',
                    posts.category_id,
                    categories.name AS 'category_name',
                    image_path,
                    video_link,
                    users.login,
                    avatar,
                    users.id AS 'users_id'
            FROM likes
                JOIN posts ON likes.post_id = posts.id
                JOIN users ON likes.user_id = users.id
                JOIN categories ON categories.id = posts.category_id
            WHERE posts.author_id = ?
            ORDER BY likes.date_add DESC;";

    return fetchAllPrepareStmt($connect, $sql, $userId);
}

/**
 * Получение списка пользователей, которые подписаны на автора.
 * @param $connect mysqli Ресурс соединения
 * @param int $userId Id автора
 * @param int $authorizedUserId Id авторизованного пользователя
 * @return array
 */
function getSubscribersList($connect, int $userId, int $authorizedUserId): array
{
    $sql = "SELECT  users.id AS 'user_id',
                    login,
                    avatar,
                    users.date_registration,
                    subscribers_count,
                    COUNT(DISTINCT posts.id) AS 'count_post',
                    (SELECT EXISTS (SELECT * FROM subscribes WHERE subscribes.author_id = user_id AND subscriber_id = ?)) AS 'is_subscribe'
            FROM users
                JOIN subscribes ON users.id = subscriber_id
                JOIN posts ON posts.author_id = users.id
                LEFT JOIN
                    (SELECT subscribes.author_id,
                            COUNT(DISTINCT subscribes.id) AS 'subscribers_count'
                    FROM subscribes
                    GROUP BY subscribes.author_id) AS subscribers_count
                 ON subscribers_count.author_id = users.id
            WHERE subscribes.author_id = ?
            GROUP BY users.id;";

    return fetchAllPrepareStmt($connect, $sql, [$authorizedUserId, $userId]);
}

/**
 * Проверяет, подписан ли авторизованный пользователь на профайл автора.
 * @param $connect mysqli Ресурс соединения
 * @param int $authorizedUserId Id авторизованного пользователя
 * @param int $userProfileId Id профайл другого пользователя
 * @return bool false, если авторизованный пользователь не подписан на автора
 */
function isUserSubscribe($connect, int $authorizedUserId, int $userProfileId): bool
{
    $sql = "SELECT * FROM subscribes WHERE author_id = ? AND subscriber_id = ?;";
    $result = fetchArrayPrepareStmt($connect, $sql, [$userProfileId, $authorizedUserId]);

    if (empty($result)) {
        return false;
    }
    return true;
}

/**
 * Проверка существования пользователя в БД.
 * @param $connect mysqli Ресурс соединения
 * @param int $userId Id пользователя, которого надо проверить на существование
 * @return bool false, если такого поста не существует
 */
function isUserExists($connect, int $userId): bool
{
    $sql = "SELECT * from users WHERE id = ?;";

    if (!empty(fetchArrayPrepareStmt($connect, $sql, $userId))) {
        return true;
    }

    return false;
}

/**
 * Подписаться на пользователя.
 * @param $connect mysqli Ресурс соединения
 * @param int $authorizedUserId Id авторизованного пользователя
 * @param int $userProfileId Id профайл другого пользователя
 * @return void
 */
function subscribeToUser($connect, int $authorizedUserId, int $userProfileId)
{
    $sql = "INSERT INTO subscribes (subscriber_id, author_id) VALUES (?, ?);";

    executePrepareStmt($connect, $sql, [$authorizedUserId, $userProfileId]);
}

/**
 * Отписаться от пользователя.
 * @param $connect mysqli Ресурс соединения
 * @param int $authorizedUserId Id авторизованного пользователя
 * @param int $userProfileId Id профайл другого пользователя
 * @return void
 */
function unsubscribeFromUser($connect, int $authorizedUserId, int $userProfileId)
{
    $sql = "DELETE FROM subscribes WHERE subscriber_id = ? AND author_id = ?;";

    executePrepareStmt($connect, $sql, [$authorizedUserId, $userProfileId]);
}

/**
 * Получение данных о посте для дальнейшего репоста.
 * @param $connect mysqli Ресурс соединения
 * @param int $postId Id поста
 * @return array
 */
function getPostData($connect, int $postId): array
{
    $sql = "SELECT author_id, category_id, title, content, quote_author, image_path, video_link, website_link
            FROM posts
            WHERE id = ?;";

    return fetchArrayPrepareStmt($connect, $sql, $postId);
}

/**
 * Получение массива id хэштегов к посту для добавления их при репосте.
 * @param $connect mysqli Ресурс соединения
 * @param int $postId Id оригинального поста
 * @return array
 */
function getHashtagsForRepost($connect, int $postId): array
{
    $sql = "SELECT hashtag_id FROM posts_hashtags WHERE post_id = ?;";

    return array_column(fetchAllPrepareStmt($connect, $sql, $postId), 'hashtag_id');
}

/**
 * Добавление репоста поста вместе хэштегами от него.
 * @param $connect mysqli Ресурс соединения
 * @param array $newPostData Массив данных из оригинального поста для репоста
 * @param int $originalPostId Id оригинального поста
 * @return void
 */
function addRepost($connect, array $newPostData, int $originalPostId, array $repostHashtags)
{
    $sqlAddPost = " INSERT INTO posts
                    (author_id, category_id, date_add, title, content, quote_author, image_path, video_link, website_link, is_repost, original_author_id)
                    VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?);";
    $sqlAddRelationship = " INSERT INTO reposts
                            (original_post_id, new_post_id)
                            VALUES (?, ?);";

    $sqlAddHashtags = "INSERT INTO posts_hashtags (post_id, hashtag_id) VALUE (?,?);";

    mysqli_query($connect, "START TRANSACTION");

    $stmtRepost = db_get_prepare_stmt($connect, $sqlAddPost, $newPostData);
    $repostResult = mysqli_stmt_execute($stmtRepost);

    // получение Id нового поста-репоста
    $newPostId = mysqli_insert_id($connect);

    $stmtRelationship = db_get_prepare_stmt($connect, $sqlAddRelationship, [$originalPostId, $newPostId]);
    $relationshipResult = mysqli_stmt_execute($stmtRelationship);

    if (!empty($repostHashtags)) {
        $hashtagsResult = false;

        foreach ($repostHashtags as $hashtag) {
            $stmtHashtags = db_get_prepare_stmt($connect, $sqlAddHashtags, [$newPostId, $hashtag]);
            $hashtagsResult = mysqli_stmt_execute($stmtHashtags);
        }

        if ($repostResult && $relationshipResult && $hashtagsResult) {
            mysqli_query($connect, "COMMIT");
        } else {
            mysqli_query($connect, "ROLLBACK");
        }
    } else {

        if ($repostResult && $relationshipResult) {
            mysqli_query($connect, "COMMIT");
        } else {
            mysqli_query($connect, "ROLLBACK");
        }
    }
}
