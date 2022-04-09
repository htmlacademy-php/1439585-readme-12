CREATE DATABASE readme
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  date_registration TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(128) NOT NULL UNIQUE,
  login VARCHAR(128) NOT NULL,
  password VARCHAR(225) NOT NULL,
  avatar VARCHAR(128)
);

CREATE TABLE subscribes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  subscriber_id INT UNSIGNED NOT NULL,
  author_id INT UNSIGNED NOT NULL,
  CONSTRAINT subscriber
  FOREIGN KEY (subscriber_id) REFERENCES users (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
);

CREATE TABLE messeges (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  sender_id INT UNSIGNED NOT NULL,
  recipient_id INT UNSIGNED NOT NULL,
  date_send TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  content TEXT NOT NULL,
  CONSTRAINT sender
  FOREIGN KEY (sender_id) REFERENCES users (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
);

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  name VARCHAR(15) NOT NULL,
  class_name VARCHAR(12) NOT NULL
);

CREATE TABLE posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  author_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  title VARCHAR(255) NOT NULL,
  content TEXT,
  quote_author VARCHAR(128),
  image_path VARCHAR(128),
  video_link VARCHAR(128),
  website_link VARCHAR(2048),
  show_count INT UNSIGNED DEFAULT 0,
  CONSTRAINT post_author
  FOREIGN KEY (author_id) REFERENCES users (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT,
  CONSTRAINT post_category
  FOREIGN KEY (category_id) REFERENCES categories (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT,
	FULLTEXT KEY search_content(title, content)
);

CREATE TABLE hashtags (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  hashtag_content VARCHAR(64) NOT NULL UNIQUE,
  FULLTEXT KEY search_hashtag(hashtag_content)
);

CREATE TABLE posts_hashtags (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  post_id INT UNSIGNED NOT NULL,
  hashtag_id INT UNSIGNED NOT NULL,
  CONSTRAINT post_hashtag
  FOREIGN KEY (post_id) REFERENCES posts (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT,
  CONSTRAINT hashtags_id
  FOREIGN KEY (hashtag_id) REFERENCES hashtags (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
);

CREATE TABLE likes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  post_id INT UNSIGNED NOT NULL,
  CONSTRAINT user_like
  FOREIGN KEY (user_id) REFERENCES users (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT,
  CONSTRAINT liked_post
  FOREIGN KEY (post_id) REFERENCES posts (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
);

CREATE TABLE comments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  post_id INT UNSIGNED NOT NULL,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  content VARCHAR(128) NOT NULL,
  CONSTRAINT comment_user
  FOREIGN KEY (user_id) REFERENCES users (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT,
  CONSTRAINT commented_post
  FOREIGN KEY (post_id) REFERENCES posts (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
);
