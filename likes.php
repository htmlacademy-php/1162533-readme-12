<?php
require('src/init.php');
require('src/helpers.php');
require('src/db.php');
require('src/utils.php');

$con = get_db_connection();

if (!empty($_GET) &&
    !empty($_GET['post_id']) &&
    !empty($_GET['user_id'])) {
    if (change_likes($con, $_GET)) {
        init_redirect_to_referer();
    } else {
        print_r('Не получилось произвести действия с лайками');
    }
}


