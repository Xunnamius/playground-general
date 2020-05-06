<!DOCTYPE html>
    <head>
        <title><?= isset($title) ? $title : '(unknown page)' ?></title>
    </head>
    <body>
        <h1>Header Content Here</h1>
<?php
        if($is_logged_in):
?>
        <nav>
            [Logged in as: <?= $user->username ?>]
            <a href="index.php">Home</a> |
            <a href="auth.php?logout">Logout</a> |
            <a href="new?type=<?= Entities::CVENewOrEdit ?>">New CVE</a>
            <?= $is_admin ? '| <a href="admin.php">Admin Panel</a>' : ''?>
        </nav>
<?php else: ?>
        <nav>
            <a href="index.php">Home</a> |
            <a href="auth.php">Login</a> |
            <a href="new.php?type=<?= Entities::UserByRegistration ?>">Register</a>
        </nav>
<?php endif; ?>
