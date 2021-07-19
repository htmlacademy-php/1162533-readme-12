<?php
/* @var Closure $utils_url_to */

require('init.php');
require('db.php');
require('helpers.php');
require('utils.php');
require('validation-func.php');

init_check_auth('/');

$validations = [
    'content_id' => [
        function ($name) {
            $value = filter_input(INPUT_GET, 'content_id', FILTER_VALIDATE_INT);

            if (!$value) {
                $value = 1;
            }
            return validation_result($value);
        }
    ]
];

$validation_result = validation_validate($validations);
$values = $validation_result['values'];

$con = get_db_connection();
$user = init_get_user();
$page_name = 'feed';
$title = 'Readme: моя лента';
$content_types = get_post_content_types($con);
$active_type_content_id = $values['content_id'];
$posts = get_posts_for_me($con, $user['id'], $active_type_content_id);
$unreaded_dialogs_count = get_unreaded_messages_count($con, $user['id']);

$check_is_liked_post = function($post_id) use ($con, $user)
{
    return check_liked_post($con, $post_id, $user);
};

$page_content = include_template('feed.php', [
    'active_type_content_id' => $active_type_content_id,
    'content_types' => $content_types,
    'posts' => $posts,
    'actual_user_id' => $user['id'],
    'to' => $utils_url_to,
    'check_is_liked_post' => $check_is_liked_post
]);

$page = include_template('layout.php', [
    'title' => $title,
    'user' => $user,
    'page_content' => $page_content,
    'page_name' => $page_name,
    'unreaded_dialogs_count' => $unreaded_dialogs_count
]);

print($page);
