<?php

define('MYSQL_HOST', '127.0.0.1:3306');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', 'root');
define('MYSQL_DB_NAME', 'readme');

/**
 * Подключение к базе данных
 * @return mysqli
 */
function get_db_connection()
{
    $con = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB_NAME);
    mysqli_set_charset($con, "utf8");

    if ($con == false) {
        print("Ошибка подключения: " . mysqli_connect_error());
        die();
    }

    return $con;
}

/**
 * Получение списка типов контента
 * @param mysqli $con
 * @return array
 */
function get_post_content_types($con)
{
    $sql_content_type = "SELECT * FROM content_type";
    $result_content_type = mysqli_query($con, $sql_content_type);
    $content_types = [];

    if ($result_content_type) {
        $content_types = mysqli_fetch_all($result_content_type, MYSQLI_ASSOC);
    }

    return $content_types;
}

/**
 * Получение списка популярных постов
 * @param mysqli $con
 * @param number $active_type_content_id айди выбранного типа контента
 * @return array
 */
function get_popular_posts($con, $active_type_content_id, $sort_type, $sort_direction, $limit, $offset)
{
    $active_type_content_id = $active_type_content_id ? $active_type_content_id : 1;
    $sql_post_popular = "
SELECT
    p.id,
    p.title,
    p.content,
    p.author,
    p.user_id,
    u.user_name,
    u.avatar,
    p.shown_count,
    u.login,
    c.class_name as type,
    p.date_add,
    (SELECT COUNT(1) FROM likes WHERE likes.post_id = p.id) AS likes_count,
    (SELECT COUNT(1) FROM comment WHERE comment.post_id = p.id) AS comments_count
FROM post p
JOIN user u ON p.user_id = u.id
JOIN content_type c ON p.content_type_id =  c.id
WHERE
? > 1 AND p.content_type_id = ?
OR
? = 1 AND p.content_type_id >= ?
ORDER BY ";

    switch ($sort_type) {
        case 'popular':
            $sql_post_popular .= " p.shown_count $sort_direction LIMIT ? OFFSET ?";
            break;

        case 'like':
            $sql_post_popular .= " likes_count $sort_direction LIMIT ? OFFSET ?";
            break;

        case 'date':
            $sql_post_popular .= " p.date_add $sort_direction LIMIT ? OFFSET ?";
            break;
    }

    $popular_posts = [];
    $stmt = db_get_prepare_stmt(
        $con,
        $sql_post_popular,
        [
            $active_type_content_id,
            $active_type_content_id,
            $active_type_content_id,
            $active_type_content_id,
            $limit,
            $offset
        ]);
    mysqli_stmt_execute($stmt);
    $result_popular_post = mysqli_stmt_get_result($stmt);

    if ($result_popular_post) {
        $popular_posts = mysqli_fetch_all($result_popular_post, MYSQLI_ASSOC);
    }

    return $popular_posts;
}

;

/**
 * Получение количества популярных постов
 * @param mysqli $con
 * @param int $active_type_content_id айди выбранного типа контента
 * @return int
 */
function get_popular_posts_count($con, $active_type_content_id)
{
    $active_type_content_id = $active_type_content_id ? $active_type_content_id : 1;

    $sql_post_popular = "
SELECT COUNT(p.id) as count FROM post p
WHERE
? > 1 AND p.content_type_id = ?
OR
? = 1 AND p.content_type_id >= ?";

    $stmt = db_get_prepare_stmt(
        $con,
        $sql_post_popular,
        [$active_type_content_id, $active_type_content_id, $active_type_content_id, $active_type_content_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result)['count'] : 0;
}

;

/**
 * Получение информации определенного поста
 * @param mysqli $con
 * @param int $post_id айди выбранного поста
 * @return array
 */
function get_post($con, $post_id)
{
    $sql_post = "
SELECT
    p.id,
    p.date_add,
    p.title,
    p.content,
    p.author,
    p.shown_count,
    p.user_id,
    p.repost,
    p.origin_author,
    p.content_type_id,
    c.class_name AS content_type_name,
    (SELECT COUNT(1) FROM likes WHERE likes.post_id = p.id) AS likes_count,
    (SELECT COUNT(1) FROM comment WHERE comment.post_id = p.id) AS comments_count,
    (SELECT COUNT(post.id) FROM post WHERE post.origin_post = p.id) AS reposts_count
FROM post p
JOIN content_type c ON p.content_type_id = c.id
WHERE p.id = ?";

    $stmt = db_get_prepare_stmt(
        $con,
        $sql_post,
        [$post_id]);
    mysqli_stmt_execute($stmt);
    $result_post = mysqli_stmt_get_result($stmt);

    return $result_post ? mysqli_fetch_assoc($result_post) : [];
}

;

/**
 * Получение хэштегов для поста
 * @param mysqli $con
 * @param int $post_id айди выбранного поста
 * @return array
 */
function get_post_hashtags($con, $post_id)
{
    $sql_hashtags = "SELECT ph.hashtag_id, h.title FROM posthashtag ph JOIN hashtag h ON ph.hashtag_id = h.id WHERE ph.post_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql_hashtags,
        [$post_id]);
    mysqli_stmt_execute($stmt);
    $result_hashtags = mysqli_stmt_get_result($stmt);

    return $result_hashtags ? mysqli_fetch_all($result_hashtags, MYSQLI_ASSOC) : [];
}

;

/**
 * Получение информации об авторе поста
 * @param mysqli $con
 * @param int $author_id айди автора поста
 * @return array
 */
function get_info_about_post_author($con, $author_id)
{
    $sql_author_info = "
SELECT
   *,
    (SELECT COUNT(subscription.id) FROM subscription WHERE subscription.user_id = u.id) AS count_followers,
    (SELECT COUNT(post.id) FROM post WHERE post.user_id = u.id) AS count_posts
FROM user u
WHERE u.id = ?
GROUP BY u.id";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql_author_info,
        [$author_id]);
    mysqli_stmt_execute($stmt);
    $result_author_info = mysqli_stmt_get_result($stmt);

    return $result_author_info ? mysqli_fetch_assoc($result_author_info) : [];
}

;

/**
 * Получение комментариев к посту
 * @param mysqli $con
 * @param int $post_id айди поста
 * @return array
 */
function get_comments_for_post($con, $post_id)
{
    $sql_comments = "
SELECT
    c.id,
    c.date_add,
    c.message,
    c.user_id,
    u.login AS author_login,
    u.user_name AS author_name,
    u.avatar AS author_avatar
FROM comment c
JOIN user u ON c.user_id = u.id
WHERE c.post_id = ?
ORDER BY c.date_add ASC";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql_comments,
        [$post_id]);
    mysqli_stmt_execute($stmt);
    $result_comments = mysqli_stmt_get_result($stmt);

    return $result_comments ? mysqli_fetch_all($result_comments, MYSQLI_ASSOC) : [];
}

;

/**
 * Получение данных пользователя по его email
 * @param mysqli $con
 * @param string $user_email email пользователя
 * @return array
 */
function get_user_data($con, $user_email)
{
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_email]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result) : [];
}

;

/**
 * Получение постов для пользователя
 * @param mysqli $con
 * @param string $user_id айди пользователя
 * @param int $active_type_content_id айди активного типа контента
 * @return array
 */
function get_posts_for_me($con, $user_id, $active_type_content_id = 1)
{
    $sql_posts = "
SELECT
    p.id,
    p.title,
    p.content,
    p.author,
    p.user_id,
    u.user_name,
    u.avatar,
    p.shown_count,
    u.login,
    c.class_name as type,
    p.date_add,
    (SELECT COUNT(1) FROM likes WHERE likes.post_id = p.id) AS likes_count,
    (SELECT COUNT(1) FROM comment WHERE comment.post_id = p.id) AS comments_count,
    (SELECT COUNT(post.id) FROM post WHERE post.origin_post = p.id) AS reposts_count
FROM post p
JOIN user u ON p.user_id = u.id
JOIN content_type c ON p.content_type_id =  c.id
JOIN subscription ON p.user_id = subscription.user_id
WHERE
(? > 1 AND p.content_type_id = ?
OR
? = 1 AND p.content_type_id >= ?)
AND
subscription.follower_id = ?
ORDER BY p.date_add DESC;";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql_posts,
        [
            $active_type_content_id,
            $active_type_content_id,
            $active_type_content_id,
            $active_type_content_id,
            $user_id
        ]);
    mysqli_stmt_execute($stmt);
    $result_posts = mysqli_stmt_get_result($stmt);

    return $result_posts ? mysqli_fetch_all($result_posts, MYSQLI_ASSOC) : [];
}

;

/**
 * Получение постов по ключевому слову
 * @param mysqli $con
 * @param string $search_value значение по которому производится поиск
 * @return array
 */
function get_search_results($con, $search_value)
{
    $sql = "SELECT
    p.*,
    u.login,
    u.user_name,
    u.avatar,
    ct.class_name AS type,
    (SELECT COUNT(1) FROM likes WHERE likes.post_id = p.id) AS likes_count,
    (SELECT COUNT(1) FROM comment WHERE comment.post_id = p.id) AS comments_count
FROM post p
JOIN user u ON u.id = p.user_id
JOIN content_type ct ON ct.id = p.content_type_id
WHERE MATCH(p.title, p.content) AGAINST(?)";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$search_value]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

;

/**
 * Получение постов по хэштегу
 * @param mysqli $con
 * @param string $search_value значение по которому производится поиск
 * @return array
 */
function get_search_hashtag_results($con, $search_value)
{
    $sql = "SELECT
    p.*,
    u.login,
    u.user_name,
    u.avatar,
    ct.class_name AS type,
    (SELECT COUNT(1) FROM likes WHERE likes.post_id = p.id) AS likes_count,
    (SELECT COUNT(1) FROM comment WHERE comment.post_id = p.id) AS comments_count
FROM post p
JOIN user u ON u.id = p.user_id
JOIN content_type ct ON ct.id = p.content_type_id
WHERE p.id IN (SELECT ph.post_id FROM posthashtag ph WHERE ph.hashtag_id = (SELECT h.id FROM hashtag h WHERE h.title = ?))
ORDER BY p.date_add DESC";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$search_value]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

;

/**
 * Получение списка постов принадлежащих пользователю
 * @param mysqli $con
 * @param int $user_id айди пользователя
 * @return array
 */
function get_user_posts($con, $user_id)
{
    $sql = "SELECT post.*,
content_type.class_name AS content_type_title,
user.login as origin_author_post,
user.avatar as origin_author_avatar,
(SELECT COUNT(likes.id) FROM likes WHERE likes.post_id = post.id) AS likes_count,
(SELECT COUNT(p.id) FROM post p WHERE p.origin_post = post.id) AS reposts_count
FROM post
LEFT JOIN user ON user.id = post.origin_author
JOIN content_type ON content_type.id = post.content_type_id
WHERE user_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

;

/**
 * Отправка комментария в бд
 * @param mysqli $con
 * @param array $values данные с формы (сообщение, айди пользователя, айди поста)
 * @return int айди нового комментария
 */
function send_comment($con, $values)
{
    $post = get_post($con, $values['post']);

    if (!empty($post)) {
        $data = [
            'message' => $values['message'],
            'user_id' => $values['user'],
            'post_id' => $values['post']
        ];

        $fields = [];
        $data_for_query = [];
        foreach ($data as $key => $item) {
            $fields[] = "{$key} = ?";
            $data_for_query[] = $item;
        }

        $fields_for_query = implode(', ', $fields);
        $sql = "INSERT INTO comment SET {$fields_for_query}";
        $stmt = db_get_prepare_stmt(
            $con,
            $sql,
            $data_for_query);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_get_result($stmt);
        return mysqli_insert_id($con);
    }
}

;

/**
 * Проверка подписки одного пользователя на другого
 * @param mysqli $con
 * @param int $user_id айди пользователя, на которого подписан
 * @param int $follower_id айди пользователя, который подписан
 * @return array
 */
function check_subscription($con, $user_id, $follower_id)
{
    $sql = "SELECT id FROM subscription WHERE user_id = ? AND follower_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id, $follower_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result) : [];
}

;

/**
 * Меняет подписку (добавляет или удаляет)
 * @param mysqli $con
 * @param array $values значения с формы (айди пользователя на которого подписываются, действие, айди пользователя который подписывается)
 * @return bool вовзращает true, если пользователь подписывается, и false если отписывается
 */
function change_subscription($con, $values)
{
    $sql_user = "SELECT id FROM user WHERE id = ?";
    $stmt_user = db_get_prepare_stmt(
        $con,
        $sql_user,
        [$values['user_id']]);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user_info = mysqli_fetch_assoc($result_user);

    if (!empty($user_info)) {
        $sql_subscription = "";
        switch ($values['action']) {
            case 'remove':
                $sql_subscription = "DELETE FROM subscription WHERE user_id = ? && follower_id = ?";
                break;

            case 'add':
                $sql_subscription = "INSERT INTO subscription SET user_id = ?, follower_id = ?";
                break;
        }

        $stmt_subscription = db_get_prepare_stmt(
            $con,
            $sql_subscription,
            [$values['user_id'], $values['follower_id']]);
        mysqli_stmt_execute($stmt_subscription);
        $result_subscription = mysqli_stmt_get_result($stmt_subscription);

        if ($result_user && $result_subscription) {
            mysqli_query($con, "COMMIT");
        } else {
            mysqli_query($con, "ROLLBACK");
        }

        return mysqli_stmt_errno($stmt_subscription) == 0 AND $values['action'] == 'add';
    }
}

;

/**
 * Проверка лайка авторизованного пользователя на посте
 * @param mysqli $con
 * @param int $user_id айди пользователя
 * @param int $post_id айди поста
 * @return array
 */
function check_like($con, $user_id, $post_id)
{
    $sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id, $post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result) : [];
}

;

/**
 * Выполнение запросов на добавление/удаления лайка с поста
 * @param mysqli $con
 * @param array $values массив значений айди поста и айди пользователя
 * @return bool
 */
function change_likes($con, $values)
{
    $post = get_post($con, $values['post_id']);

    if (!empty($post)) {
        $like = check_like($con, $values['user_id'], $values['post_id']);
        $sql = !empty($like) ? "DELETE FROM likes WHERE user_id = ? AND post_id = ?" : "INSERT INTO likes SET user_id = ?, post_id = ?";

        $stmt = db_get_prepare_stmt(
            $con,
            $sql,
            [$values['user_id'], $values['post_id']]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_get_result($stmt);
        return mysqli_stmt_errno($stmt) == 0;
    }
}

;

/**
 * Возвращает список лайков
 * @param mysqli $con
 * @param int $user_id айди пользователя
 * @return array
 */
function get_likes_list($con, $user_id)
{
    $sql = "SELECT likes.*,
user.id, user.login, user.avatar, post.content,
(SELECT title FROM content_type WHERE content_type.id = post.content_type_id) as content_type
FROM likes
JOIN user ON user.id = likes.user_id
JOIN post ON post.id = likes.post_id
WHERE likes.post_id IN (SELECT post.id FROM post WHERE post.user_id = ?)
ORDER BY likes.date_add DESC";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

;

/**
 * Возвращает список пользователей, на которых подписан выбранный пользователь
 * @param mysqli $con
 * @param int $user_id айди пользователя
 * @return array
 */
function get_user_subscriptions($con, $user_id)
{
    $sql = "SELECT ss.*, u.id as user_id, u.date_add as user_date_add, u.avatar, u.login,
(SELECT COUNT(post.id) FROM post WHERE post.user_id = ss.user_id) as post_count,
(SELECT COUNT(subscription.id) FROM subscription WHERE subscription.user_id = ss.user_id) as subscription_count
    FROM subscription ss
    JOIN user u ON u.id = ss.user_id
    WHERE ss.follower_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

;

/**
 * Возвращает информацию определенного пользователя
 * @param mysqli $con
 * @param int $user_id айди пользователя
 * @return array
 */
function get_user_info($con, $user_id)
{
    $sql = "SELECT login, email, avatar FROM user WHERE id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result) : [];
}

;

/**
 * Возвращает список подписчиков
 * @param mysqli $con
 * @param int $user_id айди пользователя
 * @return array
 */
function get_followers($con, $user_id)
{
    $sql = "SELECT follower_id, email, login
FROM subscription
JOIN user ON user.id = follower_id
WHERE user_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

/**
 * Возвращает список диалогов пользователя
 * @param mysqli $con
 * @param int $user_id айди пользователя
 * @return array
 */
function get_message_users($con, $user_id)
{
    $sql = "SELECT
        grps.user_id,
        login,
        avatar,
        m.content,
        m.sender_id,
        grps.last_message,
        m.was_read,
        (SELECT COUNT(id) AS messages_count
        FROM message
        WHERE was_read = 0 AND recipient_id = ? AND sender_id = user_id
        GROUP BY sender_id) as unreaded_messages_count
        FROM message m
        INNER JOIN (SELECT MAX(date_add) AS last_message,
        IF(recipient_id = ?, sender_id, recipient_id) AS user_id
        FROM message
        WHERE sender_id = ? OR recipient_id = ?
        GROUP BY user_id) grps
        ON m.date_add = grps.last_message
        INNER JOIN user
        ON user.id = user_id
        ORDER BY last_message DESC";

    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id, $user_id, $user_id, $user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

;

/**
 * Возвращает количество непрочитанных сообщений для конкретного получателя
 * @param mysqli $con
 * @param int $user_id айди пользователя (получатель)
 * @return int
 */
function get_unreaded_messages_count($con, $user_id)
{
    $sql = "SELECT COUNT(message.id) AS messages_count FROM message WHERE recipient_id = ? AND was_read = 0";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_assoc($result)['messages_count'] : 0;
}

;

/**
 * Возвращает список сообщений для конкретных отправителя и получателя
 * @param mysqli $con
 * @param int $user_id айди пользователя (отправитель или получатель)
 * @param int $owner_id айди пользователя (получатель или отправитель)
 * @return array
 */
function get_messages($con, $user_id, $owner_id)
{
    $sql = "SELECT message.*, avatar, login
FROM message
JOIN user ON user.id = message.sender_id
WHERE (sender_id = ? AND recipient_id = ?) OR (sender_id = ? AND recipient_id = ?)";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id, $owner_id, $owner_id, $user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

;

/**
 * Добавляет новое сообщение в бд
 * @param mysqli $con
 * @param string $content текст сообщения
 * @param int $sender_id айди отправителя
 * @param int $recipient_id айди получателя
 */
function send_message($con, $content, $sender_id, $recipient_id)
{
    $sql_user = "SELECT id from user WHERE id = ?";
    $stmt_user = db_get_prepare_stmt(
        $con,
        $sql_user,
        [$recipient_id]);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user_id = $result_user ? mysqli_fetch_assoc($result_user) : null;

    if ($result_user && $user_id['id'] !== $sender_id) {
        $sql = "INSERT INTO message SET content = ?, sender_id = ?, recipient_id = ?";
        $stmt = db_get_prepare_stmt(
            $con,
            $sql,
            [$content, $sender_id, $recipient_id]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_get_result($stmt);
    }
}

;

/**
 * Обновляе статус прочтения сообщения на прочтено
 * @param mysqli $con
 * @param int $recipient_id айди получателя
 * @param int $sender_id айди отправителя
 */
function read_messages($con, $recipient_id, $sender_id)
{
    $sql = "UPDATE message SET was_read = 1 WHERE sender_id = ? AND recipient_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$sender_id, $recipient_id]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
}

;

/**
 * Возвращает id добавленного поста
 * @param mysqli $con
 * @param array $post данные поста
 * @param int|string $post_type_id айди типа контента поста
 * @param int|string $user_id автор поста
 * @param string $file_url ссылка на прикрепленный файл
 * @return int|string
 */
function save_post($con, $post, $post_type_id, $user_id, $file_url = null)
{
    $data = [
        'id' => null,
        'date_add' => date('Y-m-d H:i:s'),
        'title' => $post['post-heading'],
        'content' => '',
        'author' => null,
        'shown_count' => 0,
        'user_id' => $user_id,
        'content_type_id' => $post_type_id
    ];

    switch ($post['active-tab']) {
        case 'photo':
            if ($file_url) {
                $data['content'] = $file_url;
            } else {
                $data['content'] = $post['photo-url']['photo-url'];
            }
            break;

        case 'video':
            $data['content'] = $post['video-url'];
            break;

        case 'text':
            $data['content'] = $post['post-text'];
            break;

        case 'quote':
            $data['content'] = $post['cite-text'];
            $data['author'] = $post['quote-author'];
            break;

        case 'link':
            $data['content'] = $post['post-link'];
            break;
    }

    $fields = [];
    $data_for_query = [];
    foreach ($data as $key => $item) {
        $fields[] = "{$key} = ?";
        array_push($data_for_query, $item);
    }

    $fields_for_query = implode(', ', $fields);
    $sql = "INSERT INTO post SET {$fields_for_query}";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        $data_for_query);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
    return mysqli_insert_id($con);
}

;

/**
 * Сохраняет хэштеги в бд
 * @param mysqli $con
 * @param array $hashtags массив хэштегов
 * @param int|string $post_id айди поста
 */
function save_tags($con, $hashtags, $post_id)
{
    $new_unique_hashtags = array_unique((explode(' ', htmlspecialchars($hashtags))));
    $sql_hashtags_db = "SELECT * FROM hashtag";
    $result_hashtags_db = mysqli_query($con, $sql_hashtags_db);

    if ($result_hashtags_db) {
        $hashtags_by_db = mysqli_fetch_all($result_hashtags_db, MYSQLI_ASSOC);

        foreach ($new_unique_hashtags as $hashtag) {
            $hashtag_value = substr($hashtag, 1, strlen($hashtag));
            $hashtag_id = null;
            $repeat_hashtag_key = array_search($hashtag_value, array_column($hashtags_by_db, 'title'));

            if ($repeat_hashtag_key) {
                $hashtag_id = $hashtags_by_db[$repeat_hashtag_key]['id'];
            } else {
                $sql_hashtag_title = "INSERT INTO hashtag SET title = ?";
                $stmt = db_get_prepare_stmt(
                    $con,
                    $sql_hashtag_title,
                    [$hashtag_value]);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_get_result($stmt);
                $hashtag_id = mysqli_insert_id($con);
            }

            $sql_add_post_hashtag = "INSERT INTO posthashtag SET post_id = ?, hashtag_id = ?";
            $stmt_post_hashtags = db_get_prepare_stmt(
                $con,
                $sql_add_post_hashtag,
                [$post_id, $hashtag_id]);
            mysqli_stmt_execute($stmt_post_hashtags);
            mysqli_stmt_get_result($stmt_post_hashtags);
        }
    }
}

;

/**
 * Проверяет существование email в бд
 * @param mysqli $con
 * @param string $email email введенный пользователем при попытке регистрации
 * @return bool
 */
function check_email_in_db($con, $email)
{
    $sql = "SELECT id, email FROM user WHERE email = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$email]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && empty(mysqli_fetch_all($result, MYSQLI_ASSOC))) {
        return true;
    }

    return false;
}

;

/**
 * Добавляет нового пользователя в бд
 * @param mysqli $con
 * @param array $post массив с данными пользовтателя
 * @param string $file_url ссылка на аватар пользователя
 * @return int|string
 */
function register_user($con, $post, $file_url = null)
{
    $data = [
        'id' => null,
        'date_add' => date('Y-m-d H:i:s'),
        'email' => $post['registration-email'],
        'login' => $post['registration-login'],
        'password' => password_hash($post['registration-password'], PASSWORD_DEFAULT),
        'user_name' => null,
        'avatar' => $file_url
    ];

    $fields = [];
    $data_for_query = [];
    foreach ($data as $key => $item) {
        $fields[] = "{$key} = ?";
        array_push($data_for_query, $item);
    }

    $fields_for_query = implode(', ', $fields);
    $sql = "INSERT INTO user SET {$fields_for_query}";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        $data_for_query);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
    return mysqli_insert_id($con);
}

;

/**
 * Проверяет что пользователь с таким email существует в базе
 * @param mysqli $con
 * @param string $email email пользователя при авторизации
 * @return bool
 */
function check_user_email($con, $email)
{
    $sql = "SELECT id FROM user WHERE email = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$email]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($result && !empty($user_data)) {
        return true;
    }

    return false;
}

;

/**
 * Проверяет данные пользователя в бд при его авторизации на сайте
 * @param mysqli $con
 * @param string $email email пользователя при авторизации
 * @param string $password пароль пользователя при авторизации
 * @return bool
 */
function check_user_author_data($con, $email, $password)
{
    $sql = "SELECT id, email, password FROM user WHERE email = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$email]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($result && !empty($user_data) && password_verify($password, $user_data[0]['password'])) {
        return true;
    }

    return false;
}

;

/**
 * Возвращает название иконки исходя из того, поставил польсозователь лайе на данный пост или нет
 * @param mysqli $con
 * @param int|string $post_id айди поста
 * @param array $user массив данных авторизованного пользователя
 * @return string
 */
function check_liked_post($con, $post_id, $user): string
{
    return !empty(check_like($con, $user['id'], $post_id)) ? 'icon-heart-active' : 'icon-heart';
}

;


/**
 * Возвращает id добавленного репостнутого поста
 * @param mysqli $con
 * @param array $post данные поста
 * @return int|string
 */
function save_repost_post($con, $post)
{
    $data = [
        'title' => $post['title'],
        'content' => $post['content'],
        'author' => $post['author'],
        'shown_count' => 0,
        'user_id' => $post['user_id'],
        'content_type_id' => $post['content_type_id'],
        'repost' => $post['repost'],
        'origin_author' => $post['origin_author'],
        'origin_post' => $post['origin_post']
    ];

    $fields = [];
    $data_for_query = [];
    foreach ($data as $key => $item) {
        $fields[] = "{$key} = ?";
        array_push($data_for_query, $item);
    }

    $fields_for_query = implode(', ', $fields);
    $sql = "INSERT INTO post SET {$fields_for_query}";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        $data_for_query);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
    return mysqli_insert_id($con);
}

;
