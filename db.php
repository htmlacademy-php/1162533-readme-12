<?php

define('MYSQL_HOST', '127.0.0.1:3306');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', 'root');
define('MYSQL_DB_NAME', 'readme');

/**
 * Подключение к базе данных
 * @return mysqli
 */

function get_db_connection() {
    $con = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD,MYSQL_DB_NAME);
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
function get_post_content_types($con) {
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
 * @param number $active_type_content_id
 * @return array
 */
function get_popular_posts($con, $active_type_content_id, $sort_type, $sort_direction, $limit, $offset) {
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
        [$active_type_content_id,
            $active_type_content_id,
            $active_type_content_id,
            $active_type_content_id,
            $limit,
            $offset]);
    mysqli_stmt_execute($stmt);
    $result_popular_post = mysqli_stmt_get_result($stmt);

    if ($result_popular_post) {
        $popular_posts = mysqli_fetch_all($result_popular_post, MYSQLI_ASSOC);
    }

    return $popular_posts;
};

function get_popular_posts_count($con, $active_type_content_id) {
    $active_type_content_id = $active_type_content_id ? $active_type_content_id : 1;

    $sql_post_popular = "
SELECT COUNT(p.id) as count FROM post p
WHERE
? > 1 AND p.content_type_id = ?
OR
? = 1 AND p.content_type_id >= ?";

    $count = 0;
    $stmt = db_get_prepare_stmt(
        $con,
        $sql_post_popular,
        [$active_type_content_id, $active_type_content_id, $active_type_content_id, $active_type_content_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $count = mysqli_fetch_assoc($result);
    }

    return $count;
};

function get_post($con, $post_id) {
    $sql_post = "
SELECT
    p.id,
    p.date_add,
    p.title,
    p.content,
    p.author,
    p.shown_count,
    p.user_id,
    c.class_name AS content_type_name,
    (SELECT COUNT(1) FROM likes WHERE likes.post_id = p.id) AS likes_count,
    (SELECT COUNT(1) FROM comment WHERE comment.post_id = p.id) AS comments_count
FROM post p
JOIN content_type c ON p.content_type_id = c.id
WHERE p.id = ?";

    $stmt = db_get_prepare_stmt(
        $con,
        $sql_post,
        [$post_id]);
    mysqli_stmt_execute($stmt);
    $result_post = mysqli_stmt_get_result($stmt);
    $post = null;

    if ($result_post) {
        $post = mysqli_fetch_all($result_post, MYSQLI_ASSOC);
    }

    return $post ? $post[0] : $post;
};

function get_post_hashtags($con, $post_id) {
    $sql_hashtags = "SELECT ph.hashtag_id, h.title FROM posthashtag ph JOIN hashtag h ON ph.hashtag_id = h.id WHERE ph.post_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql_hashtags,
        [$post_id]);
    mysqli_stmt_execute($stmt);
    $result_hashtags = mysqli_stmt_get_result($stmt);
    $hashtags = null;

    if ($result_hashtags) {
        $hashtags = mysqli_fetch_all($result_hashtags, MYSQLI_ASSOC);
    }

    return $hashtags;
};

function get_info_about_post_author($con, $author_id) {
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
    $author = null;

    if ($result_author_info) {
        $author = mysqli_fetch_assoc($result_author_info);
    }

    return $author;
};

function get_comments_for_post($con, $post_id) {
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
    $comments = null;

    if ($result_comments) {
        $comments = mysqli_fetch_all($result_comments, MYSQLI_ASSOC);
    }

    return $comments;
};

function get_user_data($con, $user_email) {
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_email]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = null;

    if ($result) {
        $user_data = mysqli_fetch_assoc($result);
    }

    return $user_data;
};

function get_posts_for_me($con, $user_id, $active_type_content_id = 1) {
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
    (SELECT COUNT(1) FROM comment WHERE comment.post_id = p.id) AS comments_count
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
    $posts = [];
    $stmt = db_get_prepare_stmt(
        $con,
        $sql_posts,
        [
            $active_type_content_id,
            $active_type_content_id,
            $active_type_content_id,
            $active_type_content_id,
            $user_id]);
    mysqli_stmt_execute($stmt);
    $result_posts = mysqli_stmt_get_result($stmt);

    if ($result_posts) {
        $posts = mysqli_fetch_all($result_posts, MYSQLI_ASSOC);
    }

    return $posts;
};

function get_search_results($con, $search_value) {
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
    $posts = [];

    if ($result) {
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $posts;
};

function get_search_hashtag_results($con, $search_value) {
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
    $posts = [];

    if ($result) {
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $posts;
};

function get_user_posts($con, $user_id) {
    $sql = "SELECT post.*, content_type.class_name AS content_type_title,
(SELECT COUNT(likes.id) FROM likes WHERE likes.post_id = post.id) AS likes_count
FROM post
JOIN content_type ON content_type.id = post.content_type_id
WHERE user_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $posts = [];

    if ($result) {
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $posts;
};

function send_comment($con, $values) {
    $post = get_post($con, $values['post']);

    if(!empty($post)) {
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
};

function check_subscription($con, $user_id, $follower_id) {
    $sql = "SELECT id FROM subscription WHERE user_id = ? AND follower_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id, $follower_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $is_subscription = [];

    if ($result) {
        $is_subscription = mysqli_fetch_assoc($result);
    }

    return $is_subscription;
};

function change_subscription($con, $values) {
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
        }
        else {
            mysqli_query($con, "ROLLBACK");
        }

        return mysqli_stmt_errno($stmt_subscription) == 0 AND $values['action'] == 'add';
    }
};

function check_like($con, $user_id, $_post_id) {
    $sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id, $_post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $like = [];

    if ($result) {
        $like = mysqli_fetch_assoc($result);
    }

    return $like;
};

function change_likes($con, $values) {
    $post = get_post($con, $values['post_id']);

    if(!empty($post)) {
        $like = check_like($con, $values['user_id'], $values['post_id']);
        $sql = "";

        if (!empty($like)) {
            $sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
        } else {
            $sql = "INSERT INTO likes SET user_id = ?, post_id = ?";
        }

        $stmt = db_get_prepare_stmt(
            $con,
            $sql,
            [$values['user_id'], $values['post_id']]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_get_result($stmt);
        return mysqli_stmt_errno($stmt);
    }
};

function get_likes_list($con, $user_id) {
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
    $likes = [];

    if ($result) {
        $likes = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $likes;
};

function get_user_subscriptions($con, $user_id) {
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
    $subscriptions = [];

    if ($result) {
        $subscriptions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $subscriptions;
};

function get_user_info($con, $user_id) {
    $sql = "SELECT login, email, avatar FROM user WHERE id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_info = [];

    if ($result) {
        $user_info = mysqli_fetch_assoc($result);
    }

    return $user_info;
};

function get_followers($con, $user_id) {
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
    $followers = [];

    if ($result) {
        $followers = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $followers;
}

function get_message_users($con, $user_id) {
    $sql = "SELECT
        user_id,
        login,
        avatar,
        content,
        sender_id,
        last_message,
        was_read,
        (SELECT COUNT(id) AS messages_count
        FROM message
        WHERE was_read = 0 AND recipient_id = ? AND sender_id = user_id
        GROUP BY sender_id) as unreaded_messages_count
        FROM message
        INNER JOIN (SELECT MAX(date_add) AS last_message,
        IF(recipient_id = ?, sender_id, recipient_id) AS user_id
        FROM message
        WHERE sender_id = ? OR recipient_id = ?
        GROUP BY user_id) grps
        ON message.date_add = grps.last_message
        INNER JOIN user
        ON user.id = user_id
        ORDER BY last_message DESC";

    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$user_id, $user_id, $user_id, $user_id]);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $messages = [];

    if ($result) {
        $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $messages;
};


function get_messages($con, $user_id, $owner_id) {
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
    $messages = [];

    if ($result) {
        $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $messages;
};

function send_message($con, $content, $sender_id, $recipient_id) {
    $sql_user = "SELECT id from user WHERE id = ?";
    $stmt_user = db_get_prepare_stmt(
        $con,
        $sql_user,
        [$recipient_id]);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user_id = $result_user ? mysqli_fetch_assoc($result_user) : null;

    if($result_user && $user_id['id'] !== $sender_id) {
        $sql = "INSERT INTO message SET content = ?, sender_id = ?, recipient_id = ?";
        $stmt = db_get_prepare_stmt(
            $con,
            $sql,
            [$content, $sender_id, $recipient_id]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_get_result($stmt);
    }
};

function read_messages($con, $recipient_id, $sender_id) {
    $sql = "UPDATE message SET was_read = 1 WHERE sender_id = ? AND recipient_id = ?";
    $stmt = db_get_prepare_stmt(
        $con,
        $sql,
        [$sender_id, $recipient_id]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
}
