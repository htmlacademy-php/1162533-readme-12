<?php
/* @var Closure $utils_url_to */
/* @var Closure $check_is_liked_post */

require('src/init.php');
require('src/helpers.php');
require('src/utils.php');
require('src/db.php');
require('src/validation-func.php');

init_check_auth('/');

$validations = [
    'search' => [
        function () {
            $value = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);

            if (!$value) {
                $value = null;
            } else {
                $value = htmlspecialchars($value);
            }
            return validation_result($value);
        }
    ]
];

$validation_result = validation_validate($validations);
$values = $validation_result['values'];

$search_query_text = $values['search'];

$con = get_db_connection();
$user = init_get_user();
$title = 'readme: Страница результатов поиска';

$founded_posts = [];

if ($search_query_text) {
    $value_query = trim($search_query_text);

    if (substr($value_query, 0, 1) === "#") {
        $value_query = substr($value_query, 1, strlen($value_query));
        $founded_posts = get_search_hashtag_results($con, $value_query);
    } else {
        $founded_posts = get_search_results($con, $value_query);
    }
}

$check_is_liked_post = function ($post_id) use ($con, $user) {
    return check_liked_post($con, $post_id, $user);
};

$page_content = include_template('search-results.php', [
    'search_query_text' => $search_query_text,
    'founded_posts' => $founded_posts,
    'to' => $utils_url_to,
    'user' => $user,
    'check_is_liked_post' => $check_is_liked_post
]);

$page = include_template('layout.php', [
    'page_content' => $page_content,
    'title' => $title,
    'user' => $user,
    'search_query_text' => $search_query_text
]);

print($page);
