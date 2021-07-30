<?php
require('src/init.php');
require('src/helpers.php');
require('src/utils.php');
require('src/validation-func.php');
require('src/db.php');

init_check_not_auth('/feed.php');

$con = get_db_connection();
$title = 'readme: регистрация';

$form_validations = [
    'registration-email' => [
        0 => function ($name) {
            return validate_filled($name);
        },
        1 => function($name) {
            return validate_email($name);
        },
        2 => function ($name) use ($con) {
            if (! check_email_in_db($con, $_POST[$name])) {
                return validation_result(null, false, 'Данный email уже используется другим пользователем');
            }
            return validation_result($_POST[$name]);
        }
    ],
    'registration-login' => [
        0 => function ($name) {
            return validate_filled($name);
        }
    ],
    'registration-password' => [
        0 => function ($name) {
            return validate_filled($name);
        },
        1 => function ($name) {
            return validate_password($name);
        }
    ],
    'registration-password-repeat' => [
        0 => function ($name) {
            return validate_filled($name);
        },
        1 => function($name) {
            return validate_passwords_repeat('registration-password', $name);
        }
    ],
    'userpic-file' => [
        0 => function($name) {
            return validate_photo($name);
        },
        1 => function ($name) {
            if (isset($_FILES[$name]) && $_FILES[$name]['error'] === 0) {
                return validation_result($_FILES[$name]);
            } elseif (!empty($_FILES[$name]['name']) && $_FILES[$name]['error'] !== 0) {
                return validation_result(null, false, 'Ошибка загрузки файла');
            }

            return validation_result($_FILES[$name]);
        }
    ]
];

$error_field_titles = [
    'registration-email' => 'Электронная почта',
    'registration-login' => 'Логин',
    'registration-password' => 'Пароль',
    'registration-password-repeat' => 'Повтор пароля',
    'userpic-file' => 'Аватар пользователя'
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $validation_result = validation_validate($form_validations, $error_field_titles);
    $errors = $validation_result['errors'];
    $values = $validation_result['values'];

    if (empty($errors)) {
        $file_url = null;

        $photo_path = '/uploads/users/';
        $file_url = save_image($values['userpic-file'], $photo_path);

        register_user($con, $values, $file_url);
        header("Location: /");
    }
}

$page_content = include_template('registration.php', [
    'errors' => $errors
]);

$page = include_template('layout.php', [
    'page_content' => $page_content,
    'title' => $title,
    'is_registration_page' => true
]);

print($page);
