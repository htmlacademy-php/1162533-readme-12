<?php
/* @var Closure $utils_url_to */
/* @var Closure $get_sort_link */

define("POPULAR_POSTS_LIMIT", 9);

require('src/init.php');
require('src/helpers.php');
require('src/utils.php');
require('src/validation-func.php');
require('src/db.php');

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
    ],
    'page' => [
        function ($name) {
            $value = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

            if (!$value) {
                $value = 1;
            }
            return validation_result($value);
        }
    ],
    'sort_type' => [
        function ($name) {
            $value = filter_input(INPUT_GET, 'sort_type', FILTER_SANITIZE_STRING);

            if (!$value) {
                $value = 'popular';
            }

            return validation_result($value);
        }
    ],
    'sort_direction' => [
        function ($name) {
            $value = filter_input(INPUT_GET, 'sort_direction', FILTER_SANITIZE_STRING);
            $value = $value === 'ASC' ? 'ASC' : 'DESC';

            return validation_result($value);
        }
    ]
];

$validation_result = validation_validate($validations);
$values = $validation_result['values'];

$active_type_content_id = $values['content_id'];
$page = $values['page'];
$sort_type = $values['sort_type'];
$sort_direction =$values['sort_direction'];

$con = get_db_connection();
$user = init_get_user();
$page_name = 'popular';
$title = 'readme: популярное';
$content_types = get_post_content_types($con);
$offset = ($page - 1) * POPULAR_POSTS_LIMIT;
$unreaded_dialogs_count = get_unreaded_messages_count($con, $user['id']);

$popular_posts = get_popular_posts(
    $con,
    $active_type_content_id,
    $sort_type,
    $sort_direction,
    POPULAR_POSTS_LIMIT,
    $offset);
$popular_posts_count = get_popular_posts_count($con, $active_type_content_id);

$get_sort_link = function($sort) use ($active_type_content_id, $sort_type, $sort_direction, $utils_url_to) {
    $direction = $sort_type == $sort ? ($sort_direction === 'ASC' ? 'DESC' : 'ASC') : $sort_direction;

    return $utils_url_to('popular', [
        'content_id' => $active_type_content_id,
        'sort_type' => $sort,
        'sort_direction' => $direction
    ]);
};

$check_is_liked_post = function($post_id) use ($con, $user)
{
    return check_liked_post($con, $post_id, $user);
};

$page_content = include_template('main.php', [
    'popular_posts' => $popular_posts,
    'popular_posts_count' => $popular_posts_count,
    'content_types' => $content_types,
    'active_type_content_id' => $active_type_content_id,
    'limit' => POPULAR_POSTS_LIMIT,
    'page' => $page,
    'sort_type' => $sort_type,
    'sort_direction' => $sort_direction,
    'get_sort_link' => $get_sort_link,
    'to' => $utils_url_to,
    'actual_user_id' => $user['id'],
    'check_is_liked_post' => $check_is_liked_post
]);
$page = include_template('layout.php', [
    'page_content' => $page_content,
    'user' => $user,
    'title' => $title,
    'page_name' => $page_name,
    'unreaded_dialogs_count' => $unreaded_dialogs_count
]);
print($page);
