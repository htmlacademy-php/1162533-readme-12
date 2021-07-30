<?php
require('src/init.php');
require('src/helpers.php');
require('src/utils.php');
require('src/validation-func.php');
require('src/db.php');

init_check_not_auth('/feed.php');

$con = get_db_connection();
$title = 'Readme: блог, каким он должен быть';

$form_validations = [
    'login' => [
        0 => function ($name) {
            return validate_filled($name);
        },
        1 => function ($name) use ($con) {
            if (!empty($_POST[$name]) && !empty($_POST['password'])) {
                if (!check_user_author_data($con, $_POST[$name], $_POST['password'])) {
                    return validation_result(null, false, 'Вы ввели неверный email/пароль.');
                }
                return validation_result($_POST[$name]);
            }
            return validation_result($_POST[$name]);
        }
    ],
    'password' => [
        0 => function ($name) {
            return validate_filled($name);
        }
    ]
];

$error_field_titles = [
    'login' => 'Логин',
    'password' => 'Пароль'
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $validation_result = validation_validate($form_validations, $error_field_titles);
    $errors = $validation_result['errors'];
    $values = $validation_result['values'];


    if (empty($errors)) {
        $user_data = get_user_data($con, $values['login']);

        init_login($user_data, '/feed.php');
    }
}

$page = include_template('anonym.php', [
    'title' => $title,
    'errors' => $errors,
    'class' => 'footer--main'
]);
print($page);
