<?php
require('init.php');
require('helpers.php');
require('db.php');
require('utils.php');
require('init-swift-mailer.php');

$con = get_db_connection();
$path = $_SERVER['HTTP_REFERER'];
$user = init_get_user();

if (!empty($_GET) &&
    !empty($_GET['user_id']) &&
    !empty($_GET['follower_id']) &&
    !empty($_GET['action'])) {

    if (change_subscription($con, $_GET)) {
        $recipient_info = get_user_info($con, $_GET['user_id']);
        new_follower_notification('keks@phpdemo.ru', $recipient_info, $user, $mailer);
    }
}

header("Location: $path");


