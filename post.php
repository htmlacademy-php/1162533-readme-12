<?php
/* @var Closure $utils_url_to */
/* @var Closure $check_is_liked_post */
/* @var Closure $check_subs */

require('init.php');
require('helpers.php');
require('utils.php');
require('db.php');
require('validation-func.php');

init_check_auth('/');

$validations = [
    'ID' => [
        function ($name) {
            $value = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);

            if (!$value) {
                $value = NULL;
            }
            return validation_result($value);
        }
    ],
    'show_comments' => [
        function ($name) {
            $is_parameter = isset($_GET['show_comments']);

            return validation_result($is_parameter);
        }
    ],
];

$validation_result = validation_validate($validations);
$values = $validation_result['values'];

$con = get_db_connection();
$user = init_get_user();
$title = 'readme: пост';
$post_id = $values['ID'];

if (!$post_id) {
    not_found_page($user['user_name']);
}

$post = get_post($con, $post_id);

if (!$post) {
    not_found_page($user['user_name']);
}

$author_info = get_info_about_post_author($con, $post['user_id']);
$hashtags = get_post_hashtags($con, $post_id);
$comments = get_comments_for_post($con, $post_id);
$comments_length = count($comments);

if ($values['ID'] &&
    !$values['show_comments']) {
    $comments = array_slice($comments, 0, 2);
}

$check_is_liked_post = function($post_id) use ($con, $user)
{
    return check_liked_post($con, $post_id, $user);
};

$check_subs = function ($user_id) use ($con, $user)
{
    return !empty(check_subscription($con, $user_id, $user['id']));
};

$form_validations = [
    'message' => [
        0 => function ($name) {
            return validate_filled($name);
        }
    ]
];

$error_field_titles = [
    'message' => 'Комментарий'
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $validation_result = validation_validate($form_validations, $error_field_titles);
    $errors = $validation_result['errors'];
    $values = $validation_result['values'];

    if (empty($errors)) {
        send_comment($con, $_POST);
        header('Location: /post.php?ID=' . $post_id);
    }
}

$page_content = include_template('post-details.php', [
    'post' => $post,
    'author_info' => $author_info,
    'hashtags' => $hashtags,
    'comments' => $comments,
    'comments_length' => $comments_length,
    'to' => $utils_url_to,
    'user' => $user,
    'check_is_liked_post' => $check_is_liked_post,
    'check_subs' => $check_subs,
    'post_id' => $post_id,
    'errors' => $errors
]);

$page = include_template('layout.php', [
    'page_content' => $page_content,
    'user' => $user,
    'title' => $title
]);
print($page);
