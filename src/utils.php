<?php
define("POPULAR_POSTS_LIMIT", 9);
define("SPACE_SYMBOL_COUNT", 1);
define("ELLIPSIS_SYMBOL_COUNT", 3);

/**
 * @param array $user_data ['id' => int, 'user_name' => string, 'avatar' => string]
 */
function init_login(array $user_data, string $address): void
{
    $_SESSION['is_auth'] = 1;
    $_SESSION['user_name'] = $user_data['login'];
    $_SESSION['avatar'] = $user_data['avatar'];
    $_SESSION['id'] = $user_data['id'];

    init_redirect($address);
}

/**
 * Что бы уменьшить вероятность конфликта в данных сессии лучше работать с конкретными параметрами
 */
function init_logout(string $address): void
{
    unset($_SESSION['is_auth'], $_SESSION['user_name'], $_SESSION['avatar'], $_SESSION['id']);
    init_redirect($address);
}

function init_is_auth(): bool
{
    return !empty($_SESSION['is_auth'] ?? null);
}

/**
 * @param string $address
 * @return bool
 */
function init_check_auth(string $address = null): void
{
    if (!init_is_auth()) {
        init_redirect($address);
    }
}

/**
 * @param string $address
 * @return bool
 */
function init_check_not_auth(string $address): void
{
    if (init_is_auth()) {
        init_redirect($address);
    }
}

/**
 * @return array
 */
function init_get_user(): array
{
    return [
        'is_auth' => $_SESSION['is_auth'] ?? null,
        'user_name' => $_SESSION['user_name'] ?? null,
        'avatar' => $_SESSION['avatar'] ?? null,
        'id' => $_SESSION['id'] ?? null,
    ];
}

/**
 * Возвращает число и склонение подписчиков
 * @param int $count
 * @return string
 */
function format_text_followers($count)
{
    return get_noun_plural_form($count, 'подписчик', 'подписчика', 'подписчиков');
}

/**
 * Возвращает число и склонение публикаций
 * @param int $count
 * @return string
 */
function format_text_publications($count)
{
    return get_noun_plural_form($count, 'публикация', 'публикации', 'публикаций');
}

/**
 * Возвращает дату добавления записи в нужном формате
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
    } elseif ($minutes >= 60) {
        return $hours . ' ' .
            get_noun_plural_form($hours, 'час', 'часа', 'часов') . ' назад';
    } elseif ($minutes > 0) {
        return $minutes . ' ' .
            get_noun_plural_form($minutes, 'минуту', 'минуты', 'минут') . ' назад';
    } elseif ($minutes <= 0) {
        return 'только что';
    }

    return '';
}

/**
 * Возвращает страницу "не найдено"
 * @param string $user_name
 * @param string $unreaded_dialogs_count
 */
function not_found_page($user_name, $unreaded_dialogs_count)
{
    $page_content = include_template('not-found-page.php');
    $page = include_template('layout.php', [
        'page_content' => $page_content,
        'user_name' => $user_name,
        'title' => 'readme: страница не найдена',
        'unreaded_dialogs_count' => $unreaded_dialogs_count
    ]);

    print($page);
    http_response_code(404);
    exit();
}

/**
 * Возвращает количество времени на сайте в нужном формате
 * @param date $date
 * @return string
 */
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
    }

    return '';
}

/**
 * Возвращает количество подписчиков  в нужном формате
 * @param number $count
 * @return string
 */
function get_text_count_followers($count)
{
    return get_noun_plural_form($count, 'подписчик', 'подписчика', 'подписчиков');
}

/**
 * Возвращает количество публикаций в нужном формате
 * @param number $count
 * @return string
 */
function get_text_count_publications($count)
{
    return get_noun_plural_form($count, 'публикация', 'публикации', 'публикаций');
}

/**
 * Возвращает количество просмотров в нужном формате
 * @param number $count
 * @return string
 */
function get_text_count_shown($count)
{
    return $count . " " . get_noun_plural_form($count, 'просмотр', 'просмотра', 'просмотров');
}

/**
 * Возвращает значение поля
 * @param string $name
 * @return string
 */
function get_post_val($name)
{
    return !empty($_POST) && !empty($_POST[$name]) ? htmlspecialchars($_POST[$name]) : '';
}

/**
 * Возвращает ссылку на загруженный файл, который был получен по ссылке
 * @param string $file_url
 * @param string $path
 * @return string
 */
function upload_file($file_url, $path)
{
    $image_content = file_get_contents($file_url);
    $file_name = basename($file_url);
    $file_path = realpath(__DIR__ . '/..') . $path;

    if (!file_exists($file_path)) {
        mkdir($file_path, 0777, true);
    }

    file_put_contents($file_path . $file_name, $image_content);

    return $path . $file_name;
}

/**
 * Возвращает ссылку на загруженный файл
 * @param array $file
 * @param string $path
 * @return string
 */
function save_image($file, $path)
{
    $file_name = $file['name'];
    $file_path = realpath(__DIR__ . '/..') . $path;

    if (!file_exists($file_path)) {
        mkdir($file_path, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $file_path . $file_name)) {
        return $path . $file_name;
    }

    return '';
}

/**
 * Обрезает строку и добавляет ссылку Читать далее, если ее длина превышает заданный лимит символов
 * @param string $text
 * @param int $count_symbols
 * @return string
 */
function cut_text($text, $url_to, $count_symbols = 300)
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

    return '<p>' . implode(
        ' ',
        $new_word_list
    ) . '</p>' . '<a class="post-text__more-link" href="' . $url_to . '">Читать далее</a>';
}

/**
 * Возвращает домен ссылки
 * @param string $url
 * @return string
 */
function get_website_favicon($url)
{
    return "https://www.google.com/s2/favicons?domain=" . parse_url($url)['host'] ?? $url;
}

/**
 * Возвращает ссылку на превью видео
 * @param string $youtube_url
 * @return string
 */
function get_youtube_video_miniature(string $youtube_url): string
{
    $id = extract_youtube_id($youtube_url);
    return 'http://img.youtube.com/vi/' . $id . '/0.jpg';
}

/**
 * Возвращает ссылку с заданными параметрами
 * @param string $where
 * @param array $get
 * @return string
 */
function utils_url_to(string $where, array $get = []): string
{
    $result = '/' . trim($where, '/') . '.php';
    $params = [];

    foreach ($get as $param => $value) {
        $params[] = "$param=$value";
    }

    $result .= (count($params) > 0 ? '?' : '') . implode('&', $params);

    return $result;
}

/**
 * Отправляет уведомлениe о новом подписчике
 * @param string $sender почта отправителя писем
 * @param array $recipient массив с данными получателя
 * @param array $follower массив с данными подписчика
 * @param object $mailer Объект Swift_Mailer
 * @return string|int
 */
function new_follower_notification($sender, $recipient, $follower, $mailer)
{
    $site_name = 'Readme';
    $subject = 'У вас новый подписчик';
    $message = new Swift_Message($subject);
    $message->setFrom($sender, $site_name);

    $user_name = $recipient['login'];
    $follower_name = $follower['user_name'];
    $follower_id = $follower['id'];
    $link = !empty($_SERVER['HTTPS'])
        ? 'https'
        : 'http' . '://' . $_SERVER['HTTP_HOST'] . "/profile.php?user_id=" . $follower_id;

    $body = <<<MESS
Здравствуйте, {$user_name}.
На вас подписался новый пользователь {$follower_name}.
Вот ссылка на его профиль: {$link}.
MESS;

    $message->setTo($recipient['email']);
    $message->setBody($body, 'text/html');

    try {
        $result = $mailer->send($message);
    } catch (Exception $e) {
        $result = 0;
    }

    return $result;
}

/**
 * Отправляет уведомления подписчикам о новом посте
 * @param string $sender почта отправителя писем
 * @param array $recipients массив с данными получателей
 * @param array $author массив с данными автора поста
 * @param string $post_title заголовок поста
 * @param object $mailer Объект Swift_Mailer
 * @return string|int
 */
function new_post_notification($sender, $recipients, $author, $post_title, $mailer)
{
    $site_name = 'Readme';
    $subject = 'Новая публикация от пользователя ' . $author['user_name'];
    $message = new Swift_Message($subject);
    $message->setFrom($sender, $site_name);

    foreach ($recipients as $recipient) {
        $author_name = $author['user_name'];
        $user_name = $recipient['login'];
        $link = !empty($_SERVER['HTTPS'])
            ? 'https'
            : 'http' . '://' . $_SERVER['HTTP_HOST'] . "/profile.php?user_id=" . $author['id'];
        $body = <<<MESS
Здравствуйте, {$user_name}.
Пользователь {$author_name} только что опубликовал новую запись „{$post_title}“.
Посмотрите её на странице пользователя: {$link}.
MESS;

        $message->setTo($recipient['email']);
        $message->setBody($body, 'text/html');

        try {
            $result = $mailer->send($message);
        } catch (Exception $e) {
            $result = 0;
        }

        return $result;
    }
}

/**
 * Вовзращает дату последнего полученного сообщения в заданном формате
 * @param date $date дата сообщения
 * @return string
 */
function get_message_sent_time($date)
{

    if (!$date) {
        return '';
    }

    date_default_timezone_set('Europe/Moscow');
    $cur_date = date_create("now");
    $diff = date_diff($cur_date, date_create($date))->days;
    return $diff > 0 ? date_format(date_create($date), 'd F') : date_format(date_create($date), 'H:m');
}

/**
 * Redirect browser to same page
 */
function init_redirect_to_referer()
{
    init_redirect($_SERVER['HTTP_REFERER'] ?? '/');
}

/**
 * Redirect browser to current page
 */
function init_redirect($address)
{
    Header('Location: ' . $address);
}

/**
 * Создает транспорт сообщений
 * @return object
 */
function create_transport_messages()
{
    $transport = (new Swift_SmtpTransport('smtp.mail.ru', 465))
        ->setUsername('readme1162533@mail.ru')
        ->setPassword('22tYrpRIupM^')
        ->setEncryption('SSL');

    return new Swift_Mailer($transport);
}
