<?php

require('src/init.php');
require('src/helpers.php');
require('src/db.php');
require('src/utils.php');
require('src/validation-func.php');

init_check_auth('/');

$con = get_db_connection();
$user = init_get_user();

$validations_params = [
    'ID' => [
        function () {
            $value = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);

            if (!$value) {
                $value = null;
            }
            return validation_result($value);
        }
    ],
];

$validation_result = validation_validate($validations_params);
$values = $validation_result['values'];

$post_id = $values['ID'];
$post_info = get_post($con, $post_id);

if (!empty($post_info)) {
    $post_info['origin_author'] = $post_info['user_id'];
    $post_info['user_id'] = $user['id'];
    $post_info['repost'] = 1;
    $post_info['origin_post'] = $post_info['id'];

    save_repost_post($con, $post_info);
}

header('Location: profile.php?user_id=' . $user['id']);
