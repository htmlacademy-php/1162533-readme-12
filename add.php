<?php
/* @var Closure $utils_url_to */
/* @var Closure $get_tabs_link */

require('src/init.php');
require('src/helpers.php');
require('src/utils.php');
require('src/validation-func.php');
require('src/db.php');

init_check_auth('/');

$validations_parameters = [
    'active-tab' => [
        function ($name) {
            $value = filter_input(INPUT_POST, 'active-tab', FILTER_SANITIZE_STRING);
            $value_tab = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING);

            if (!$value) {
                if (!$value_tab) {
                    $value = 'photo';
                } else {
                    $value = $value_tab;
                }
            }
            return validation_result($value);
        }
    ]
];

$validation_parameters_result = validation_validate($validations_parameters);
$values_parameters = $validation_parameters_result['values'];

$con = get_db_connection();
$user = init_get_user();
$title = 'readme: добавление публикации';
$mailer = create_transport_messages();
$content_types = get_post_content_types($con);
$active_tab = $values_parameters['active-tab'];
$unreaded_dialogs_count = get_unreaded_messages_count($con, $user['id']);

$post_tabs = [
    'photo' => 'фото',
    'video' => 'видео',
    'text' => 'текст',
    'quote' => 'цитата',
    'link' => 'ссылка',
];

$post_form_title = [
    'photo' => 'Форма добавления фото',
    'video' => 'Форма добавления видео',
    'text' => 'Форма добавления текста',
    'quote' => 'Форма добавления цитаты',
    'link' => 'Форма добавления ссылки',
];

$form_validations = [
    'photo' => [
        'post-heading' => [
            0 => function ($name) {
                return validate_filled($name);
            }
        ],
        'photo-url' => [
            0 => function($name) {
                return validate_url($name);
            },
            1 => function($name) {
                return validate_upload_photo($name, 'userpic-file-photo');
            },
            2 => function($name) {
                if (isset($_FILES['userpic-file-photo']) && $_FILES['userpic-file-photo']['error'] === 0) {
                    return validation_result([$name => ($_POST[$name] ?? null), 'userpic-file-photo' => $_FILES['userpic-file-photo']]);
                }
                return validation_result([$name => ($_POST[$name] ?? null), 'userpic-file-photo' => null]);
            }
        ],
        'post-tags' => [
            0 => function ($name) {
                return validate_hashtags($name);
            }
        ],
        'userpic-file-photo' => [
            0 => function($name) {
                return validate_photo($name);
            }
        ]
    ],
    'video' => [
        'post-heading' => [
            0 => function ($name) {
                return validate_filled($name);
            }
        ],
        'video-url' => [
            0 => function ($name) {
                return validate_filled($name);
            },
            1 => function($name) {
                return validate_url($name);
            },
            2 => function($name) {
                return validate_youtube($name);
            }
        ],
        'post-tags' => [
            0 => function ($field) {
                return validate_hashtags($field);
            }
        ]
    ],
    'text' => [
        'post-heading' => [
            0 => function ($name) {
                return validate_filled($name);
            }
        ],
        'post-text' => [
            0 => function ($name) {
                return validate_filled($name);
            }
        ],
        'post-tags' => [
            0 => function ($name) {
                return validate_hashtags($name);
            }
        ]
    ],
    'quote' => [
        'post-heading' => [
            0 => function ($name) {
                return validate_filled($name);
            }
        ],
        'cite-text' => [
            0 => function ($name) {
                return validate_filled($name);
            }
        ],
        'quote-author' => [
            0 => function ($name) {
                return validate_filled($name);
            }
        ],
        'post-tags' => [
            0 => function ($name) {
                return validate_hashtags($name);
            }
        ]
    ],
    'link' => [
        'post-heading' => [
            0 => function ($name) {
                return validate_filled($name);
            }
        ],
        'post-link' => [
            0 => function ($name) {
                return validate_filled($name);
            },
            1 => function($name) {
                return validate_url($name);
            }
        ],
        'post-tags' => [
            0 => function ($name) {
                return validate_hashtags($name);
            }
        ],
    ]
];

$error_field_titles = [
    'post-heading' => 'Заголовок',
    'post-tags' => 'Теги',
    'photo-url' => 'Ссылка из интернета',
    'userpic-file-photo' => 'Файл',
    'video-url' => 'Ссылка Youtube',
    'post-text' => 'Текст поста',
    'cite-text' => 'Текст цитаты',
    'quote-author' => 'Автор цитаты',
    'post-link' => 'Ссылка'
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $validation_result = validation_validate($form_validations[$active_tab], $error_field_titles);
    $errors = $validation_result['errors'];
    $values = $validation_result['values'];
    $values['active-tab'] = $active_tab;

    if (empty($errors)) {
        $file_url = null;

        if ($active_tab === 'photo') {

            $photo_path = '/uploads/';

            if (isset($values['photo-url']['userpic-file-photo'])) {
                $file_url = save_image($values['photo-url']['userpic-file-photo'], $photo_path);
            } else {
                $file_url = upload_file($values['photo-url']['photo-url'], $photo_path);
            }
        }

        $post_type_id = $content_types[array_search($active_tab, array_column($content_types, 'title'))]['id'];
        $post_id = save_post($con, $values, $post_type_id, $user['id'], $file_url);

        if($post_id) {
            $follower_list = get_followers($con, $user['id']);

            if(!empty($follower_list)) {
                new_post_notification('readme1162533@mail.ru', $follower_list, $user, $_POST['post-heading'], $mailer);
            }
        }

        if (!empty($values['post-tags'])) {
            save_tags($con, $values['post-tags'], $post_id);
        }

        $URL = $utils_url_to('post', ['ID' => $post_id]);
        header("Location: $URL");
    }
}

$get_tabs_link = function($name) use($utils_url_to) {
    return $utils_url_to('add', ['tab' => $name]);
};

$form_fields = include_template('adding-posts/adding-post-' . $active_tab  . '.php', [
    'active_tab' => $active_tab,
    'errors' => $errors
]);

$active_form = include_template('adding-posts/adding-post-form.php', [
    'active_tab' => $active_tab,
    'errors' => $errors,
    'form_title' => $post_form_title[$active_tab],
    'fields' => $form_fields
]);

$page_content = include_template('adding-post.php', [
    'post_tabs' => $post_tabs,
    'active_tab' => $active_tab,
    'errors' => $errors,
    'active_form' => $active_form,
    'get_tabs_link' => $get_tabs_link
]);

$page = include_template('layout.php', [
    'page_content' => $page_content,
    'title' => $title,
    'user' => $user,
    'unreaded_dialogs_count' => $unreaded_dialogs_count
]);

print($page);
