<?php

require('vendor/autoload.php');

session_start();

/**
 * @param array $user_data ['id' => int, 'user_name' => string, 'avatar' => string]
 */
function init_login(array $user_data, string $address): void
{
    $_SESSION['is_auth'] = 1;
    $_SESSION['user_name'] = $user_data['login'];
    $_SESSION['avatar'] = $user_data['avatar'];
    $_SESSION['id'] = $user_data['id'];

    init_redirect($address);
};

/**
 * Что бы уменьшить вероятность конфликта в данных сессии лучше работать с конкретными параметрами
 */
function init_logout(string $address): void
{
    unset($_SESSION['is_auth'], $_SESSION['user_name'], $_SESSION['avatar'], $_SESSION['id']);
    init_redirect($address);
};

function init_is_auth(): bool
{
    return !empty($_SESSION['is_auth'] ?? null);
};

/**
 * @param string $address
 * @return bool
 */
function init_check_auth(string $address = null): void
{
    if (!init_is_auth()) {
        init_redirect($address);
    }
};

/**
 * @param string $address
 * @return bool
 */
function init_check_not_auth(string $address): void
{
    if (init_is_auth()) {
        init_redirect($address);
    }
};

/**
 * @param string $address
 * @param bool $force
 */
function init_redirect(string $address): void
{
    header("Location: $address");
    die();
};

/**
 * @return array
 */
function init_get_user(): array
{
    return [
        'is_auth' => $_SESSION['is_auth'] ?? null,
        'user_name' => $_SESSION['user_name'] ?? null,
        'avatar' => $_SESSION['avatar'] ?? null,
        'id' => $_SESSION['id'] ?? null,
    ];
}
