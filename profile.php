<?php
/* @var Closure $utils_url_to */
/* @var Closure $post_hashtags */
/* @var Closure $post_comments */
/* @var Closure $check_show_comments */
/* @var Closure $check_is_liked_post */
/* @var Closure $check_subs */

require('src/init.php');
require('src/helpers.php');
require('src/db.php');
require('src/utils.php');
require('src/validation-func.php');

init_check_auth('/');

$con = get_db_connection();
$user = init_get_user();
$title = 'readme: профиль';

$validations = [
    'user_id' => [
        function () use ($user) {
            $value = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

            if (!$value) {
                $value = $user['id'];
            }
            return validation_result($value);
        }
    ],
    'tab' => [
        function () {
            $value = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING);

            if (!$value) {
                $value = 'posts';
            }
            return validation_result($value);
        }
    ],
    'post_id' => [
        function () {
            $value = filter_input(INPUT_GET, 'post_id', FILTER_VALIDATE_INT);

            if (!$value) {
                $value = null;
            }
            return validation_result($value);
        }
    ],
    'all_comments' => [
        function () {
            $is_parameter = isset($_GET['all_comments']);

            return validation_result($is_parameter);
        }
    ],
    'show_comments' => [
        function () {
            $is_parameter = isset($_GET['show_comments']);

            return validation_result($is_parameter);
        }
    ],
    'add_comment' => [
        function () {
            $value = filter_input(INPUT_POST, 'add_comment', FILTER_SANITIZE_STRING);

            if (!$value) {
                $value = '';
            }
            return validation_result($value);
        }
    ],
    'post' => [
        function () {
            $value = filter_input(INPUT_POST, 'post', FILTER_VALIDATE_INT);

            if (!$value) {
                $value = null;
            }
            return validation_result($value);
        }
    ],
    'user' => [
        function () {
            $value = filter_input(INPUT_POST, 'user', FILTER_VALIDATE_INT);

            if (!$value) {
                $value = null;
            }
            return validation_result($value);
        }
    ]
];

$validation_result = validation_validate($validations);
$values = $validation_result['values'];

$user_id = $values['user_id'];
$active_tab = $values['tab'];
$is_your_profile = $user_id == $user['id'];
$is_subscription = $is_your_profile ? false : !empty(check_subscription($con, $user_id, $user['id']));
$user_info = get_info_about_post_author($con, $user_id);
$user_posts = get_user_posts($con, $user_id);
$likes_list = get_likes_list($con, $user_id);
$subscriptions_list = get_user_subscriptions($con, $user_id);
$error_post_comment = 0;
$unreaded_dialogs_count = get_unreaded_messages_count($con, $user['id']);

$post_hashtags = function ($post_id) use ($con) {
    return get_post_hashtags($con, $post_id);
};

$post_comments = function ($post_id) use ($con, $error_post_comment, $values) {
    $comments = get_comments_for_post($con, $post_id);

    if ($error_post_comment > 0) {
        return [
            'length' => count($comments),
            'comments' => $comments
        ];
    }

    if ($values['post_id'] &&
        $values['post_id'] == $post_id &&
        $values['all_comments']) {
        return [
            'length' => count($comments),
            'comments' => $comments
        ];
    }

    return [
        'length' => count($comments),
        'comments' => array_slice($comments, 0, 2)
    ];
};

$check_show_comments = function ($post_id) use ($values) {
    if ($values['post_id'] &&
        $values['post_id'] == $post_id &&
        $values['show_comments']) {
        return true;
    }

    return false;
};

$check_is_liked_post = function ($post_id) use ($con, $user) {
    return check_liked_post($con, $post_id, $user);
};

$check_subs = function ($user_id) use ($con, $user) {
    return !empty(check_subscription($con, $user_id, $user['id']));
};

$form_validations = [
    'message' => [
        0 => function ($name) {
            return validate_filled($name);
        },
        1 => function ($name) {
            return validate_length($name, 4);
        }
    ],
];

$error_field_titles = [
    'message' => 'Комментарий'
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($values['add_comment'] &&
        $values['post'] &&
        $values['user']
    ) {
        $post_id = $values['post'];
        $validation_form_result = validation_validate($form_validations, $error_field_titles);
        $errors_form = $validation_form_result['errors'];
        $values_form = $validation_form_result['values'];

        if (empty($errors_form)) {
            send_comment($con, $_POST);

            $href = $utils_url_to('profile', [
                'user_id' => $user_id,
                'post_id' => $post_id,
                'show_comments' => '',
                'all_comments' => '',
                'tab' => 'posts'
            ]);

            header("Location: $href");
        } else {
            $error_post_comment = $post_id;
        }
    }
}

$page_content = include_template('profile/profile.php', [
    'user_info' => $user_info,
    'user_posts' => $user_posts,
    'actual_user_avatar' => $user['avatar'],
    'actual_user_id' => $user['id'],
    'user_id' => $user_id,
    'is_your_profile' => $is_your_profile,
    'is_subscription' => $is_subscription,
    'errors' => $errors,
    'error_post_comment' => $error_post_comment,
    'active_tab' => $active_tab,
    'likes_list' => $likes_list,
    'subscriptions_list' => $subscriptions_list,
    'post_hashtags' => $post_hashtags,
    'post_comments' => $post_comments,
    'check_show_comments' => $check_show_comments,
    'check_is_liked_post' => $check_is_liked_post,
    'check_subs' => $check_subs,
    'to' => $utils_url_to
]);

$page = include_template('layout.php', [
    'page_content' => $page_content,
    'user' => $user,
    'title' => $title,
    'unreaded_dialogs_count' => $unreaded_dialogs_count
]);
print($page);
