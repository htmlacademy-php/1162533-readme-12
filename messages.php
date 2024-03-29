<?php

require('src/init.php');
require('src/helpers.php');
require('src/db.php');
require('src/utils.php');
require('src/validation-func.php');

init_check_auth('/');

$validations = [
    'active_dialog' => [
        function () {
            $value = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT);

            if (!$value) {
                $value = null;
            }
            return validation_result($value);
        }
    ]
];

$validation_result = validation_validate($validations);
$values = $validation_result['values'];

$con = get_db_connection();
$user = init_get_user();
$title = 'readme: личные сообщения';

$message_users = get_message_users($con, $user['id']);
$active_dialog = $values['active_dialog'];
$message_list = get_messages($con, $active_dialog, $user['id']);
$unreaded_dialogs_count = get_unreaded_messages_count($con, $user['id']);

$check_current_user = array_search($active_dialog, array_column($message_users, 'user_id'));

if ($active_dialog && $check_current_user === false) {
    $active_user_data = get_user_info($con, $active_dialog);

    $message_users[] = [
        'user_id' => $active_dialog,
        'login' => $active_user_data['login'],
        'avatar' => $active_user_data['avatar'],
        'content' => '',
        'sender_id' => '',
        'last_message' => '',
        'was_read' => 1,
        'unreaded_messages_count' => null
    ];
}

$form_validations = [
    'message' => [
        0 => function ($name) {
            return validate_filled($name);
        }
    ]
];

$error_field_titles = [
    'message' => 'Сообщение'
];

$errors = [];

if ($active_dialog) {
    read_messages($con, $user['id'], $active_dialog);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validation_result = validation_validate($form_validations, $error_field_titles);
    $errors = $validation_result['errors'];
    $values = $validation_result['values'];

    if (empty($errors)) {
        send_message($con, $_POST['message'], $user['id'], $_POST['recipient_id']);
        header('Location: /messages.php?user=' . $active_dialog);
    }
}

$page_content = include_template('messages.php', [
    'message_users' => $message_users,
    'active_dialog' => $active_dialog,
    'user' => $user,
    'message_list' => $message_list,
    'errors' => $errors
]);

$page = include_template('layout.php', [
    'page_content' => $page_content,
    'user' => $user,
    'title' => $title,
    'unreaded_dialogs_count' => $unreaded_dialogs_count
]);
print($page);
