CREATE DATABASE readme
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(128) NOT NULL UNIQUE,
    login VARCHAR(128),
    password CHAR(64) NOT NULL,
    avatar TEXT
);

CREATE TABLE post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    title TEXT,
    content TEXT,
    author VARCHAR(128),
    image TEXT,
    video TEXT,
    link TEXT,
    shown_count INT(11),
    user_id INT(11),
    content_type_id INT(11)
);

CREATE TABLE comment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    message TEXT,
    user_id INT(11),
    post_id INT(11)
);

CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    post_id INT(11)
);

CREATE TABLE subscription (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    follower_id INT(11)
);

CREATE TABLE message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    content TEXT,
    user_id INT(11),
    recipient_id INT(11)
);

CREATE TABLE hashtag (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title varchar(128) UNIQUE
);

CREATE TABLE content_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(128) UNIQUE,
    class_name VARCHAR(128) UNIQUE
);

CREATE TABLE PostHashtag (
    post_id INT(11),
    hashtag_id INT(11)
);
