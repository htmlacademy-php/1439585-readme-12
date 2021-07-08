/*список типов контента для поста*/
INSERT INTO categories(name,class_name)
	VALUES
		('Текст','post-text'),
		('Цитата', 'post-quote'),
		('Картинка', 'post-photo'),
		('Видео', 'post-video'),
		('Ссылка', 'post-link')

/*придумайте пару пользователей*/ 
INSERT INTO users 
	SET email = 'katewho@test.ru', login = 'Катерина', password = 'pass1kate', avatar = '';
INSERT INTO users 
	SET email = 'muha@test.ru', login = 'Andrei Muha', password = 'pass2muha', avatar = '';

/*добавление пользователей из существующего массива с постами*/ 
INSERT INTO users 
	SET id = '3', email = 'larisa@test.ru', login = 'Лариса', password = 'pass3larisa', avatar = 'userpic-larisa-small.jpg';
INSERT INTO users 
	SET id = '4', email = 'vladik@test.ru', login = 'Владик', password = 'pass4vladik', avatar = 'userpic.jpg';
INSERT INTO users 
	SET id = '5', email = 'viktor@test.ru', login = 'Виктор', password = 'pass5viktor', avatar = 'userpic-mark.jpg';

/*существующий список постов*/
INSERT INTO posts
	SET autor_id = '3', category_id = '2', title = 'Цитата', content = 'Мы в жизни любим только раз, а после ищем лишь похожих';
INSERT INTO posts
	SET autor_id = '4', category_id = '1', title = 'Игра престолов', content = 'Не могу дождаться начала финального сезона своего любимого сериала!';
INSERT INTO posts
	SET autor_id = '5', category_id = '3', title = 'Наконец, обработал фотки!', image_path = 'rock-medium.jpg';
INSERT INTO posts
	SET autor_id = '3', category_id = '3', title = 'Моя мечта', image_path = 'coast-medium.jpg';
INSERT INTO posts
	SET autor_id = '4', category_id = '5', title = 'Лучшие курсы', website_link = 'www.htmlacademy.ru';
INSERT INTO posts
	SET autor_id = '4', category_id = '1', title = 'Все лгут. Поисковики, Big Data и Интернет', content = 'Однако этот набор данных – не единственный инструмент для понимания нашего мира, предоставляемый интернетом. Вскоре я понял, что есть и другие золотоносные цифровые жилы. Я скачал всю Википедию, покопался в профилях Facebook и прошерстил Stormfront. Кроме того, PornHub, один из крупнейших порнографических сайтов интернета, дал мне свои полные данные по анонимному поиску и просмотрам видео, которые совершали люди со всего мира.';
INSERT INTO posts
	SET autor_id = '4', category_id = '1', title = 'Все лгут. Поисковики, Big Data', content = 'Сначала должен признаться: я не собираюсь давать точное определение того, что такое «большие данные». Почему? Потому что это, по сути, довольно расплывчатое понятие';
	
/*придумайте пару комментариев к разным постам*/
INSERT INTO comments
	SET user_id = '1', post_id = '3', content = 'Воу! Какая красота!';
INSERT INTO comments
	SET user_id = '2', post_id = '7', content = 'А из какой это книги или статьи?';

/*получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента;*/
SELECT login, title, name, class_name, content, quote_autor, image_path, video_link, website_link, date_add
FROM users 
JOIN posts ON users.id = posts.autor_id 
JOIN categories ON posts.category_id = categories.id 
ORDER BY show_count DESC;

/*получить список постов для конкретного пользователя;*/
SELECT login, title, name, class_name, content, quote_autor, image_path, video_link, website_link, date_add, show_count FROM users 
JOIN posts ON users.id = posts.autor_id 
JOIN categories ON posts.category_id = categories.id 
WHERE users.id  = '3';

/*получить список комментариев для одного поста, в комментариях должен быть логин пользователя;*/
SELECT login, title, comments.content, comments.date_add FROM comments
JOIN posts ON comments.post_id = posts.id 
JOIN users ON users.id = posts.autor_id 
WHERE posts.id  = '3';

/*добавить лайк к посту;*/
INSERT INTO likes SET user_id = '3', post_id = '4';

/*подписаться на пользователя*/
INSERT INTO subscribes SET subscriber_id = '1', autor_id = '4';