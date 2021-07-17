<?php
define("SPACE_SYMBOL_COUNT", 1);
define("ELLIPSIS_SYMBOL_COUNT", 3);

/**
 * Возвращает дату в нужном формате
 * @param date $date
 * @return string
 */
function format_publication_date($date)
{
    date_default_timezone_set('Europe/Moscow');
    $cur_date = date_create("now");
    $diff = date_diff($cur_date, date_create($date));
    $minutes = date_interval_format($diff, "%i");
    $hours = date_interval_format($diff, "%h");
    $days = date_interval_format($diff, "%d");
    $months = date_interval_format($diff, "%m");
    $years = date_interval_format($diff, "%y");

    if ($years > 0) {
        return $years . ' ' .
            get_noun_plural_form($years, 'год', 'года', 'лет') . ' назад';
    }
    if ($months > 0 && $months < 12) {
        return $months . ' ' .
            get_noun_plural_form($months, 'месяц', 'месяца', 'месяцев') . ' назад';
    } elseif ($days > 6) {
        return floor($days / 7) . ' ' .
            get_noun_plural_form(floor(($days / 7)), ' неделю', ' недели', ' недель') . ' назад';
    } elseif ($days > 0) {
        return $days . ' ' .
            get_noun_plural_form($days, 'день', 'дня', 'дней') . ' назад';
    } elseif ($hours > 0) {
        return $hours . ' ' .
            get_noun_plural_form($hours, 'час', 'часа', 'часов') . ' назад';
    } elseif ($minutes > 0) {
        return $minutes . ' ' .
            get_noun_plural_form($minutes, 'минуту', 'минуты', 'минут') . ' назад';
    } elseif ($minutes <= 0) {
        return 'только что';
    } else {
        return '';
    }
};

/**
 * Возвращает страницу "не найдено"
 * @param string $user_name
 */
function not_found_page($user_name)
{
    $page_content = include_template('not-found-page.php');
    $page = include_template('layout.php', [
        'page_content' => $page_content,
        'user_name' => $user_name,
        'title' => 'readme: страница не найдена',
    ]);

    print($page);
    http_response_code(404);
    exit();
}

function format_register_date($date)
{
    date_default_timezone_set('Europe/Moscow');
    $cur_date = date_create("now");
    $diff = date_diff($cur_date, date_create($date));
    $minutes = date_interval_format($diff, "%i");
    $hours = date_interval_format($diff, "%h");
    $days = date_interval_format($diff, "%d");
    $months = date_interval_format($diff, "%m");
    $years = date_interval_format($diff, "%y");

    if ($years > 0) {
        return $years . ' ' .
            get_noun_plural_form($years, 'год', 'года', 'лет') . ' на сайте';
    }
    if ($months > 0 && $months < 12) {
        return $months . ' ' .
            get_noun_plural_form($months, 'месяц', 'месяца', 'месяцев') . ' на сайте';
    } elseif ($days > 6) {
        return floor($days / 7) . ' ' .
            get_noun_plural_form(floor(($days / 7)), ' неделю', ' недели', ' недель') . ' на сайте';
    } elseif ($days > 0) {
        return $days . ' ' .
            get_noun_plural_form($days, 'день', 'дня', 'дней') . ' на сайте';
    } elseif ($hours > 0) {
        return $hours . ' ' .
            get_noun_plural_form($hours, 'час', 'часа', 'часов') . ' на сайте';
    } elseif ($minutes > 0) {
        return $minutes . ' ' .
            get_noun_plural_form($minutes, 'минуту', 'минуты', 'минут') . ' на сайте';
    } elseif ($minutes <= 0) {
        return 'новый пользователь';
    } else {
        return '';
    }
};

function get_text_count_followers($count)
{
    return get_noun_plural_form($count, 'подписчик', 'подписчика', 'подписчиков');
};

function get_text_count_publications($count)
{
    return get_noun_plural_form($count, 'публикация', 'публикации', 'публикаций');
};

function get_text_count_shown($count)
{
    return $count . " " . get_noun_plural_form($count, 'просмотр', 'просмотра', 'просмотров');
};

function get_post_val($name)
{
    return count($_POST) && $_POST[$name] ? htmlspecialchars($_POST[$name]) : '';
};

function upload_file($file_url, $path)
{
    $image_content = file_get_contents($file_url);
    $file_name = basename($file_url);
    $file_path = __DIR__ . $path;

    if (!file_exists($file_path)) {
        mkdir($file_path, 0777, true);
    }

    file_put_contents($file_path . $file_name, $image_content);

    return $path .  $file_name;
};

function save_image($file, $path)
{
    $file_name = $file['name'];
    $file_path = __DIR__ . $path;

    if (!file_exists($file_path)) {
        mkdir($file_path, 0777, true);
    }

    move_uploaded_file($file['tmp_name'], $file_path . $file_name);

    return $path . $file_name;
};


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
};

function save_tags($con, $hashtags, $post_id)
{
    $new_unique_hashtags = array_unique((explode(' ', htmlspecialchars($hashtags))));
    $sql_hashtags_db = "SELECT * FROM hashtag";
    $result_hashtags_db = mysqli_query($con, $sql_hashtags_db);
    $hashtags_by_db = [];

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
};

function check_email_in_db($con, $email) {
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
};

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
};

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
};

function cut_text($text, $count_symbols = 300)
{
    $word_list = explode(" ", $text);
    $symbols_sum = 0;
    $new_word_list = null;

    if (mb_strlen($text, 'utf-8') <= $count_symbols) {
        return '<p>' . $text . '</p>';
    }

    foreach ($word_list as $word) {
        $symbols_sum += mb_strlen($word, 'utf-8') + SPACE_SYMBOL_COUNT;

        if ($symbols_sum + ELLIPSIS_SYMBOL_COUNT >= $count_symbols) {
            $new_word_list[] = '...';
            break;
        }

        $new_word_list[] = $word;
    }

    return '<p>' . implode(' ', $new_word_list) . '</p>' . '<a class="post-text__more-link" href="#">Читать далее</a>';
};

function get_domain($url)
{
    return parse_url($url)['host'] ?? $url;
};

function get_youtube_video_miniature(string $youtube_url): string
{
    $id = extract_youtube_id($youtube_url);
    $src = 'http://img.youtube.com/vi/'.$id.'/0.jpg';

    return $src;
};

$utils_url_to = function (string $where, array $get = []): string
{
    $result = '/' . trim($where, '/') . '.php';
    $params = [];

    foreach ($get as $param => $value) {
        $params[] = "$param=$value";
    }

    $result .= (count($params) > 0 ? '?' : '') . implode('&', $params);

    return $result;
};

function check_liked_post($con, $post_id, $user): string
{
    return !empty(check_like($con, $user['id'], $post_id)) ? 'icon-heart-active' : 'icon-heart';
};

function new_follower_notification($sender, $recipient, $follower, $mailer)
{
    $site_name = 'Readme';
    $subject = 'У вас новый подписчик';
    $message = new Swift_Message($subject);
    $message->setFrom($sender, $site_name);

    $user_name = $recipient['login'];
    $follower_name = $follower['user_name'];
    $follower_id = $follower['id'];
    $link = !empty($_SERVER['HTTPS']) ? 'https' : 'http' . '://' . $_SERVER['HTTP_HOST'] . "/profile.php?user_id=" . $follower_id;

    $body = <<<MESS
Здравствуйте, {$user_name}.
На вас подписался новый пользователь {$follower_name}.
Вот ссылка на его профиль: {$link}.
MESS;

    $message->setTo($recipient['email']);
    $message->setBody($body, 'text/html');
    $result = $mailer->send($message);

    return $result;
}

function new_post_notification($sender, $recipients, $author, $post_title, $mailer)
{
    $site_name = 'Readme';
    $subject = 'Новая публикация от пользователя ' . $author['user_name'];
    $message = new Swift_Message($subject);
    $message->setFrom($sender, $site_name);

    foreach ($recipients as $recipient) {
        $author_name = $author['user_name'];
        $user_name = $recipient['login'];
        $link = !empty($_SERVER['HTTPS']) ? 'https' : 'http' . '://' . $_SERVER['HTTP_HOST'] . "/profile.php?user_id=" . $author['id'];
        $body = <<<MESS
Здравствуйте, {$user_name}.
Пользователь {$author_name} только что опубликовал новую запись „{$post_title}“.
Посмотрите её на странице пользователя: {$link}.
MESS;

        $message->setTo($recipient['email']);
        $message->setBody($body, 'text/html');
        $mailer->send($message);
    }
}

function get_message_sent_time($date) {

    if (!$date) {
        return '';
    }

    date_default_timezone_set('Europe/Moscow');
    $cur_date = date_create("now");
    $diff = date_diff($cur_date, date_create($date))->days;
    return $diff > 0 ? date_format(date_create($date), 'd F') : date_format(date_create($date), 'H:m');
};
