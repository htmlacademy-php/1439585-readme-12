/*список типов контента для поста*/
INSERT INTO categories(name,class_name)
	VALUES
		('Текст','text'),
		('Цитата', 'quote'),
		('Картинка', 'photo'),
		('Видео', 'video'),
		('Ссылка', 'link');

/*придумайте пару пользователей*/
INSERT INTO users
	SET email = 'katewho@test.ru', login = 'Катерина', password = SHA2('pass1Kate', 224), avatar = '/img/cat_02.jpg';
INSERT INTO users
	SET email = 'muha@test.ru', login = 'Andrei Muha', password = SHA2('pass2Muha', 224), avatar = '/img/cat_01.jpg';

/*добавление пользователей из существующего массива с постами*/
INSERT INTO users
	SET id = 3, email = 'larisa@test.ru', login = 'Лариса', password = SHA2('pass3Larisa', 224), avatar = '/img/userpic-larisa.jpg';
INSERT INTO users
	SET id = 4, email = 'vladik@test.ru', login = 'Владик', password = SHA2('pass4Vladik', 224), avatar = '/img/userpic-medium.jpg';
INSERT INTO users
	SET id = 5, email = 'viktor@test.ru', login = 'Виктор', password = SHA2('pass5Viktor', 224), avatar = '/img/userpic-mark.jpg';

/*существующий список постов*/
INSERT INTO posts
	SET author_id = 3, category_id = 2, title = 'Цитата', content = 'Мы в жизни любим только раз, а после ищем лишь похожих', show_count = 17;
INSERT INTO posts
	SET author_id = 4, category_id = 1, title = 'Игра престолов', content = 'Не могу дождаться начала финального сезона своего любимого сериала!', show_count = 11;
INSERT INTO posts
	SET author_id = 5, category_id = 3, title = 'Наконец, обработал фотки!', image_path = '/img/rock-medium.jpg', show_count = 9;
INSERT INTO posts
	SET author_id = 3, category_id = 3, title = 'Моя мечта', image_path = '/img/coast-medium.jpg', show_count = 10;
INSERT INTO posts
	SET author_id = 4, category_id = 5, title = 'Лучшие курсы', website_link = 'www.htmlacademy.ru', show_count = 12;
INSERT INTO posts
	SET author_id = 4, category_id = 1, title = 'Все лгут. Поисковики, Big Data и Интернет', content = 'Однако этот набор данных – не единственный инструмент для понимания нашего мира, предоставляемый интернетом. Вскоре я понял, что есть и другие золотоносные цифровые жилы. Я скачал всю Википедию, покопался в профилях Facebook и прошерстил Stormfront. Кроме того, PornHub, один из крупнейших порнографических сайтов интернета, дал мне свои полные данные по анонимному поиску и просмотрам видео, которые совершали люди со всего мира.', show_count = 15;
INSERT INTO posts
	SET author_id = 4, category_id = 1, title = 'Все лгут. Поисковики, Big Data', content = 'Сначала должен признаться: я не собираюсь давать точное определение того, что такое «большие данные». Почему? Потому что это, по сути, довольно расплывчатое понятие', show_count = 5;

/*дополнительный список постов, добавленный для тестирования*/
INSERT INTO posts
  SET author_id = 3, category_id = 4, title = 'Видосик', video_link = 'https://youtu.be/dnIX06dmNts', show_count = 25;
INSERT INTO posts
  SET author_id = 5, category_id = 5, title = 'Мануал', website_link = 'https://www.php.net/manual/ru/', show_count = 5;
INSERT INTO posts
  SET author_id = 2, category_id = 5, title = 'Вагонетки можно купить тут', website_link = 'maxmaster.ru', show_count = 6;

/*придумайте пару комментариев к разным постам*/
INSERT INTO comments
	SET user_id = 1, post_id = 3, content = 'Воу! Какая красота!';
INSERT INTO comments
	SET user_id = 2, post_id = 7, content = 'А из какой это книги или статьи?';
	INSERT INTO comments
	SET user_id = 5, post_id = 7, content = 'Это ты где прочел?';

/*дополнительный список комментов, добавленный для тестирования*/
INSERT INTO comments
	SET user_id = 1, post_id = 4, content = 'Я бы с удовольствием там побывала!';
INSERT INTO comments
	SET user_id = 3, post_id = 7, content = 'Опять ты за свое...';
INSERT INTO comments
	SET user_id = 2, post_id = 2, content = 'Блин, заждался уже их';
INSERT INTO comments
	SET user_id = 1, post_id = 9, content = 'Очень интересно, но временами непонятно';
INSERT INTO comments
	SET user_id = 4, post_id = 9, content = 'Согласен';

/*получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента;*/
SELECT login, title, name, class_name, content, quote_author, image_path, video_link, website_link, date_add, show_count
FROM users
JOIN posts ON users.id = posts.author_id
JOIN categories ON posts.category_id = categories.id
ORDER BY show_count DESC;

/*получить список постов для конкретного пользователя;*/
SELECT login, title, name, class_name, content, quote_author, image_path, video_link, website_link, date_add, show_count FROM users
JOIN posts ON users.id = posts.author_id
JOIN categories ON posts.category_id = categories.id
WHERE users.id  = 3;

/*получить список комментариев для одного поста, в комментариях должен быть логин пользователя;*/
SELECT login, title, comments.content, comments.date_add FROM comments
JOIN posts ON comments.post_id = posts.id
JOIN users ON users.id = posts.author_id
WHERE posts.id  = 3;

/*добавить лайк к посту;*/
INSERT INTO likes SET user_id = 3, post_id = 4;
INSERT INTO likes SET user_id = 5, post_id = 4;
INSERT INTO likes SET user_id = 1, post_id = 6;
INSERT INTO likes SET user_id = 3, post_id = 3;
INSERT INTO likes SET user_id = 2, post_id = 2;

/*дополнительный список лайков, добавленный для тестирования*/
INSERT INTO likes SET user_id = 2, post_id = 3;
INSERT INTO likes SET user_id = 2, post_id = 4;
INSERT INTO likes SET user_id = 1, post_id = 5;
INSERT INTO likes SET user_id = 1, post_id = 7;
INSERT INTO likes SET user_id = 3, post_id = 7;

/*подписаться на пользователя*/
INSERT INTO subscribes SET subscriber_id = 1, author_id = 4;
INSERT INTO subscribes SET subscriber_id = 5, author_id = 4;
INSERT INTO subscribes SET subscriber_id = 4, author_id = 5;

/*добавление хэштегов и связи хэхтега к посту*/
INSERT INTO hashtags SET id = 1, hashtag_content = 'bigdata';
INSERT INTO posts_hashtags SET post_id = 6, hashtag_id = 1;
INSERT INTO posts_hashtags SET post_id = 7, hashtag_id = 1;

INSERT INTO hashtags SET id = 2, hashtag_content = 'море';
INSERT INTO posts_hashtags SET post_id = 4, hashtag_id = 2;

INSERT INTO hashtags SET id = 3, hashtag_content = 'обработкафото';
INSERT INTO posts_hashtags SET post_id = 3, hashtag_id = 3;

INSERT INTO hashtags SET id = 4, hashtag_content = 'природа';
INSERT INTO posts_hashtags SET post_id = 4, hashtag_id = 4;
INSERT INTO posts_hashtags SET post_id = 3, hashtag_id = 4;

/*добавление сообщений*/
INSERT INTO messages SET sender_id = 3, recipient_id = 1, dialog_id = 1, content = 'Катю, поедешь с нами на озеро? Мы тут с ребятами поплавать собрались';
INSERT INTO messages SET sender_id = 1, recipient_id = 3, dialog_id = 1, content = 'Да, с удовольствием! во сколько выдвигаемся? Заберете меня??))';
INSERT INTO messages SET sender_id = 2, recipient_id = 5, dialog_id = 2, content = 'Привет, слушай, здоровские фотки получились!';
INSERT INTO messages SET sender_id = 5, recipient_id = 2, dialog_id = 2, content = 'Привет! Спасибо, старался, наконец-то время появилось на это....А то поездка была давно, а обработал только сейчас.';
