<?php

require_once('helpers.php');
require_once('config/site_config.php');

/**
 * Обрезает текст до указанной длины и добавляет в конце троеточие и ссылку на полный текст.
 * @param string $cardContent Текст для обрезания
 * @param integer $lenght Длина строки, установаленная по умолчанию
 * @return string
 */
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

/**
 * Обрезает превью ссылки для поста-ссылки на странице популярного
 * @param string $siteLink Ссылка для обрезания
 * @return string
 */
function cutPreviewLink($siteLink): string
{
    $siteLink = htmlspecialchars($siteLink);

    return mb_substr($siteLink, 0, 30);
}

/**
 * Корректировка URL-ссылки, в зависимости от наличия http:// в ней
 * @param string $siteLink Ссылка для проверки
 * @return string
 */
function correctSiteUrl($siteLink): string
{
    $needleChars = 'http';

    if (strpos($siteLink, $needleChars) === false) {
        $siteLink =  'http://' . $siteLink;
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

/**
 * Выполнение запроса к БД и извлечение результата в ассоциативный массив.
 * @param string $sqlQuery SQL запрос с данными, которые нужно получить из БД
 * @param $connect mysqli Ресурс соединения
 * @return array
 */
function fetchAll(string $sqlQuery, $connect): array
{
    $resultSqlQuery = $connect->query($sqlQuery);
    if ($resultSqlQuery) {
        return $resultSqlQuery->fetch_all(MYSQLI_ASSOC);
    }
    echo sprintf("Ошибка получения данных. %d %s", $connect->errno, $connect->error);
    die;
}

/**
 * Возвращает массив данных из подготовленного sql-выражения
 * как для выражения с 1 плейсхолдером, так и с несколькими.
 * @param $connect mysqli Ресурс соединения
 * @param string $sqlQuery SQL запрос с плейсхолдерами
 * @param mixed $val Значения для вставки вместо плейсхолдеров
 * @return array
 */
function fetchPrepareStmt($connect, string $sqlQuery, $val): array
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
 * Возвращает массив с подсчитанным количеством лайков и комментариев к посту.
 * @param $connect mysqli Ресурс соединения
 * @param string $sqlQuery SQL запрос на получение количества лайков и комментраниев
 * @param array $cards Массив с содержимым поста
 * @return array
 */
function ratingCount($connect, string $sqlQuery, array $cards): array
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
            $errors[$keyAllField] =  $requiredFields[$keyAllField] . " Это поле должно быть заполнено.";
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
 * @return bool Возвращает false, если не корректны сылка или mime-тип
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
 * Добавление хэштегов нового поста в таблицу хештегов и связей
 * @param $connect mysqli Ресурс соединения
 * @param int $post_id Id поста, к которому относятся данные теги
 * @param array $hashtags Массив тегов на добавление
 * @return void
 */
function addPostsHashtags($connect, int $post_id, array $hashtags)
{

    $sqlHashtags = 'INSERT INTO hashtags (hashtag_content) VALUES (?);';
    $sqlPostsHashtags = 'INSERT INTO posts_hashtags (post_id, hashtag_id) VALUES (?, ?);';

    foreach ($hashtags as $tags) {

        $isTagExsist = fetchPrepareStmt($connect, "SELECT * FROM hashtags WHERE hashtag_content=?;", $tags);

        if (!empty($isTagExsist)) {
            $tags_id = $isTagExsist[0]['id'];
        } else {
            $stmtTags = db_get_prepare_stmt($connect, $sqlHashtags, (array) $tags);
            mysqli_stmt_execute($stmtTags);
            $tags_id = mysqli_insert_id($connect);
        }

        $intPostId = (int) $post_id;
        $intTagsId = (int) $tags_id;
        $stmtPH = db_get_prepare_stmt($connect, $sqlPostsHashtags, ["$intPostId", "$intTagsId"]);
        mysqli_stmt_execute($stmtPH);
    }
}

/**
 * Добавление в БД нового текстового поста.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param string $userId Id юзера, добавляющего пост
 * @param string $categoryId Id категории типа поста
 * @return void
 */
function addNewTextPost($connect, array $requiredFields, string $userId, string $categoryId)
{
    $sql = "INSERT INTO posts (author_id, category_id, date_add, title, content ) VALUES ($userId, $categoryId, NOW(), ?, ?);";

    return addMainPostContent($connect, $requiredFields, $sql);
}

/**
 * Добавление в БД нового поста-цитаты.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param string $userId Id юзера, добавляющего пост
 * @param string $categoryId Id категории типа поста
 * @return void
 */
function addNewQuotePost($connect, array $requiredFields, string $userId, string $categoryId)
{
    $sql = "INSERT INTO posts (author_id, category_id, date_add, title, content, quote_author) VALUES ($userId, $categoryId, NOW(), ?, ?, ?);";

    return addMainPostContent($connect, $requiredFields, $sql);
}

/**
 * Добавление в БД нового поста с фото.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param string $userId Id юзера, добавляющего пост
 * @param string $categoryId Id категории типа поста
 * @param string $imageName Ссылка на полный путь к файлу-изображения
 * @return void
 */
function addNewPhotoPost($connect, array $requiredFields, string $userId, string $categoryId, $imageName)
{
    $sql = "INSERT INTO posts (author_id, category_id, date_add, title, image_path) VALUES ($userId, $categoryId, NOW(), ?, ?);";

    return addMainPostContent($connect, $requiredFields, $sql, $imageName);
}

/**
 * Добавление в БД нового видео-поста.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param string $userId Id юзера, добавляющего пост
 * @param string $categoryId Id категории типа поста
 * @return void
 */
function addNewVideoPost($connect, array $requiredFields, string $userId, string $categoryId)
{

    $sql = "INSERT INTO posts (author_id, category_id, date_add, title, video_link) VALUES ($userId, $categoryId, NOW(), ?, ?);";

    return addMainPostContent($connect, $requiredFields, $sql);
}

/**
 * Добавление в БД нового поста-ссылки.
 * @param $connect mysqli Ресурс соединения
 * @param array $requiredFields Обязательные для заполнения поля
 * @param string $userId Id юзера, добавляющего пост
 * @param string $categoryId Id категории типа поста
 * @return void
 */
function addNewLinkPost($connect, array $requiredFields, string $userId, string $categoryId)
{
    $sql = "INSERT INTO posts (author_id, category_id, date_add, title, website_link) VALUES ($userId, $categoryId, NOW(), ?, ?);";

    return addMainPostContent($connect, $requiredFields, $sql);
}

/**
 * Получение списка всех категорий
 * @param $connect mysqli Ресурс соединения
 * @return array
 */
function getCategoryList($connect): array
{
    $sqlCategories = "SELECT * FROM categories;";
    $categories = fetchAll($sqlCategories, $connect);

    return $categories;
}

/**
 * Получение всех существующих карточек постов с рейтингом
 * @param $connect mysqli Ресурс соединения
 * @return array
 */
function getAllCardsContent($connect): array
{
    // sql-запрос на получение всех постов объедененный с рейтингом
    $sqlCardsContent = "SELECT *
                        FROM
                            (SELECT posts.id AS post_id,
                                login,
                                avatar,
                                title,
                                category_id,
                                content,
                                quote_author,
                                image_path,
                                video_link,
                                website_link,
                                date_add,
                                show_count
                            FROM users
                            JOIN posts
                                ON users.id = posts.author_id
                            ORDER BY  show_count DESC) AS cards
                        JOIN
                            (SELECT posts.id,
                                COUNT(DISTINCT likes.id) AS 'likes_count', COUNT(DISTINCT comments.id) AS 'comment_countя'
                            FROM posts
                            JOIN users
                                ON users.id = posts.author_id
                            LEFT JOIN likes
                                ON posts.id = likes.post_id
                            LEFT JOIN comments
                                ON comments.post_id = posts.id
                            GROUP BY  posts.id) AS ratings
                            ON cards.post_id = ratings.id
                        ORDER BY  show_count DESC;";

    $cards = fetchAll($sqlCardsContent, $connect);

    return $cards;
}

/**
 * Получение карточек постов с рейтингом по конкретной категории
 * @param $connect mysqli Ресурс соединения
 * @param mixed $categoryId Id категории, по которой происходит выборка
 * @return array
 */
function getCardsByCategory($categoryId, $connect): array
{
    // sql-запрос на получение постов по конкретной категории объедененный с рейтингом
    $sqlCardsOnCategory = "SELECT *
    FROM
        (SELECT posts.id AS post_id,
                login,
                avatar,
                title,
                category_id,
                content,
                quote_author,
                image_path,
                video_link,
                website_link,
                date_add,
                show_count
        FROM users
            JOIN posts ON users.id = posts.author_id
        ORDER BY show_count DESC) AS cards
    JOIN
        (SELECT posts.id,
                COUNT(DISTINCT likes.id) AS 'likes_count',
                COUNT(DISTINCT comments.id) AS 'comment_count'
        FROM posts
            JOIN users ON users.id = posts.author_id
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN comments ON comments.post_id = posts.id
        GROUP BY posts.id) AS ratings
        ON cards.post_id = ratings.id
    WHERE cards.category_id = $categoryId
    ORDER BY show_count DESC;";

    $cards = fetchAll($sqlCardsOnCategory, $connect);

    return $cards;
}

/**
 * Переадресация пользователя на нужную нам страницу.
 * @param string $page Путь на страницу для перенаправления
 * @return void
 */
function redirectOnPage(string $page)
{
    header("Location: /{$page}");
    exit();
}

/**
 * Проверка существования пользователя в БД.
 * @param $connect mysqli Ресурс соединения
 * @param string $userEmail email нового пользователя
 * @return bool False если такого пользователя еще не существует
 */
function checkEmailExists($connect, string $userEmail): bool
{
    $sqlQuery = 'SELECT email FROM users WHERE email = ?';
    $userExists = fetchPrepareStmt($connect, $sqlQuery, $userEmail);

    if (!empty($userExists)) {
        return true;
    }

    return false;
}

/**
 * Проверяет, является ли введенный email корректным
 * @param string $userEmail email нового пользователя
 * @return bool False если мейл не корректен
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
 * пароль должен быть не менее 6 символов, а также содержать в себе цифры и латинские буквы, в верхннем и нижнем регистре.
 * @param string $password Проверяеммый пароль
 * @return bool False если не пароль не соответствует требованиям
 */
function isPasswordCorrect(string $password): bool
{
    // Условия, по которым пароль должен соответствовать взяты из видео в ТЗ на сайте: https://www.youtube.com/watch?v=NexC8QPTNpM

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
    $passwordWhithoutNumbers = preg_replace("/[^a-z]/i", '', $password);

    // Если пароль только в верхнем регистре или только в нижнем return false
    if (ctype_upper($passwordWhithoutNumbers) === true || ctype_lower($passwordWhithoutNumbers) === true) {
        return false;
    }

    return true;
}

/**
 * Проверка на совпадение паролей
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

    $stmt = db_get_prepare_stmt($connect, $sqlQuery, $userData);
    mysqli_stmt_execute($stmt);
}

/**
 * Добавление ссылки на аватар в таблицу users
 * @param $connect mysqli Ресурс соединения
 * @param string $avatarPath Путь к аватару
 * @param int $user_id Id последнего добавленного пользователя
 * @return void
 */
function addUserAvatar($connect, string $avatarPath, int $user_id)
{
    $sqlQuery = 'UPDATE users SET avatar = ? WHERE id = ?';

    $stmt = db_get_prepare_stmt($connect, $sqlQuery, [$avatarPath, $user_id]);
    mysqli_stmt_execute($stmt);
}
