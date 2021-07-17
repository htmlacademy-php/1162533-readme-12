<?php
require('init.php');
require('helpers.php');
require('db.php');

$con = get_db_connection();
$path = $_SERVER['HTTP_REFERER'];

if (!empty($_GET) &&
    !empty($_GET['post_id']) &&
    !empty($_GET['user_id'])) {
    if (change_likes($con, $_GET)) {
        header("Location: $path");
    } else {
        print_r('Не получилось произвести действия с лайками');
    }
}


