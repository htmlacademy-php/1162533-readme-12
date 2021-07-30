/* Запросы на добавление типов контента для поста */
INSERT INTO content_type (title, class_name, label) VALUES ('all', 'post-all', 'Все');
INSERT INTO content_type (title, class_name, label) VALUES ('text', 'post-text', 'Текст');
INSERT INTO content_type (title, class_name, label) VALUES ('photo', 'post-photo', 'Фото');
INSERT INTO content_type (title, class_name, label) VALUES ('video', 'post-video', 'Видео');
INSERT INTO content_type (title, class_name, label) VALUES ('quote', 'post-quote', 'Цитата');
INSERT INTO content_type (title, class_name, label) VALUES ('link', 'post-link', 'Ссылка');

/* Запросы на добавление пользователей */
INSERT INTO user (email, login, password, user_name, avatar) VALUES ('first.user@mail.ru', 'first.user', 'first111', 'Катерина', 'https://i.ibb.co/71dJ1H2/first.jpg');
INSERT INTO user (email, login, password, user_name, avatar) VALUES ('second.user@mail.ru', 'second.user', 'second222', 'Игорь', 'https://i.ibb.co/BwfffR1/second.jpg');

/* Запросы на добавление постов */
INSERT INTO post
    (title, content, author, shown_count, user_id, content_type_id)
    VALUES ('Любовь внутри тебя', 'Ты говоришь, что тебя никто не любит, но самом деле это не так, потому что ощущение любви находится внутри тебя.',
    '', '5', '1', '2');
INSERT INTO post
    (title, content, author, shown_count, user_id, content_type_id)
    VALUES ('Прекрасное далеко!', 'https://i.ibb.co/B350kK4/background.jpg', '', '8', '2', '3');

INSERT INTO post
    (title, content, author, shown_count, user_id, content_type_id)
    VALUES ('Лисичка-сестричка', 'https://youtu.be/WJwi_hgJbpM', '', '2', '1', '4');

INSERT INTO post
    (title, content, author, shown_count, user_id, content_type_id)
    VALUES ('Цитата', 'Каждый хочет изменить человечество, но никто не задумывается о том, как изменить себя.',
    'Лев Толстой', '6', '2', '5');

INSERT INTO post
    (title, content, author, shown_count, user_id, content_type_id)
    VALUES ('Лучшие курсы', 'www.htmlacademy.ru',
    '', '2', '1', '6');

/* Запросы на добавление комментариев к постам */
INSERT INTO comment (message, user_id, post_id) VALUES ('Да вы философ!', '1', '1');
INSERT INTO comment (message, user_id, post_id) VALUES ('Вы заставили меня задуматься...', '2', '1');
INSERT INTO comment (message, user_id, post_id) VALUES ('Красивое фото!', '1', '2');
INSERT INTO comment (message, user_id, post_id) VALUES ('А где это снято?', '2', '2');
INSERT INTO comment (message, user_id, post_id) VALUES ('Хороший монтаж!', '1', '3');
INSERT INTO comment (message, user_id, post_id) VALUES ('Вау! Просто захватывающе!', '2', '3');
INSERT INTO comment (message, user_id, post_id) VALUES ('Все-таки какие вещи он писал!', '1', '4');
INSERT INTO comment (message, user_id, post_id) VALUES ('Мой любимый автор!', '2', '4');
INSERT INTO comment (message, user_id, post_id) VALUES ('Я учился там. Рекомендую!', '1', '5');
INSERT INTO comment (message, user_id, post_id) VALUES ('Хм, стоит попробовать.', '2', '5');

/* Запросы на добавление хештегов */
INSERT INTO hashtag SET title = 'nature';
INSERT INTO hashtag SET title = 'щикарныйвид';
INSERT INTO hashtag SET title = 'цитатавеликихлюдей';
INSERT INTO hashtag SET title = 'полезныйресурс';
INSERT INTO hashtag SET title = 'мысливслух';
INSERT INTO hashtag SET title = 'познавательноевидео';
INSERT INTO hashtag SET title = 'видео';

/* Запросы на добавление хештегов к постам */
INSERT INTO posthashtag (post_id, hashtag_id) VALUES ('1', '5');
INSERT INTO posthashtag (post_id, hashtag_id) VALUES ('2', '1');
INSERT INTO posthashtag (post_id, hashtag_id) VALUES ('2', '2');
INSERT INTO posthashtag (post_id, hashtag_id) VALUES ('3', '6');
INSERT INTO posthashtag (post_id, hashtag_id) VALUES ('3', '7');
INSERT INTO posthashtag (post_id, hashtag_id) VALUES ('4', '3');
INSERT INTO posthashtag (post_id, hashtag_id) VALUES ('5', '4');

/* Запрос на получение списка постов с сортировкой по популярности вместе с именами авторов и типом контента */
SELECT p.id, p.title, p.content, p.author, p.shown_count, u.login, c.title, p.date_add
FROM post p JOIN user u ON p.user_id = u.id
JOIN content_type c ON p.content_type_id = c.id
ORDER BY shown_count DESC;

/* Запрос на получение списка постов для конкретного пользователя (н-р пользователь 1) */
SELECT * FROM post WHERE user_id = '1';

/* Запрос на получение списка комментариев для одного поста (н-р для поста 3), в комментариях должен быть логин пользователя; */
SELECT c.id, c.date_add, c.message, u.login FROM comment c JOIN user u ON c.user_id = u.id  WHERE post_id = '3';

/* Запрос на добавление лайка к посту (например от пользователя 2 на пост 1) */
INSERT INTO likes (user_id, post_id) VALUES ('2', '1');
/* Запрос на подписку пользователя (например пользователь 2 подписывается на пользователя 1) */
INSERT INTO subscription (user_id, follower_id) VALUES ('1', '2');
