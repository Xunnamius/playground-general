<?php
    const MAX_LOGIN_ATTEMPTS = 3;
    const ROOT_ADMIN_ID = 1;

    session_start();
    $mysqli = new mysqli('localhost', 'root', 'root', 'CVETrack') or die('some shit happened');

    $is_logged_in = !empty($_SESSION['user']);
    $submission = isset($_POST['submit']);

    $user = $is_logged_in ? $_SESSION['user'] : [];
    $user = (object) $user;

    $is_admin = $is_logged_in ? $user->is_admin : false;
    $is_root_admin = $is_admin ? $user->id == ROOT_ADMIN_ID : false;

    require('functions.php');
    require('enums.php');

    if($is_logged_in && $user->status == LoginStatus::UserNeverLoggedIn && !stristr($_SERVER['REQUEST_URI'], 'virgin.php'))
        $redirect_to('virgin.php');
