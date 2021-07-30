<?php
require('src/init.php');
require('src/helpers.php');
require('src/db.php');
require('src/utils.php');

$con = get_db_connection();
$user = init_get_user();
$mailer = create_transport_messages();

if (!empty($_GET) &&
    !empty($_GET['user_id']) &&
    !empty($_GET['follower_id']) &&
    !empty($_GET['action'])) {

    if (change_subscription($con, $_GET)) {
        $recipient_info = get_user_info($con, $_GET['user_id']);
        new_follower_notification('readme1162533@mail.ru', $recipient_info, $user, $mailer);
    }
}

init_redirect_to_referer();


