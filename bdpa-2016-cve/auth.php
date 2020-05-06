<?php
    $title = 'CVE Login';
    require('common.php');

    $is_logging_in = $submission;
    $is_logging_out = isset($_GET['logout']);

    $login_attempts_remaining = -1;
    $login_failure = AuthFailureType::None;

    // Bounced
    if(!$is_logging_out && $is_logged_in)
        $redirect_to('index.php');

    if($is_logging_out)
    {
        setcookie(session_name(), '', time()-7000000);
        unset($_SESSION);
        session_destroy();
        $redirect_to('index.php');
    }

    if($is_logging_in)
    {
        $username = isset($_POST['username']) ? $mysqli->escape_string($_POST['username']) : null;
        $password = isset($_POST['password']) ? $mysqli->escape_string($_POST['password']) : null;

        if(empty($username) || empty($password))
        {
            if(empty($username))
                $login_failure |= AuthFailureType::MissingUsername;

            if(empty($password))
                $login_failure |= AuthFailureType::MissingPassword;
        }

        else
        {
            $res = $mysqli->query("SELECT * FROM login WHERE Login_id = '$username' LIMIT 1");

            if(!$res->num_rows)
            {
                // That username doesn't exist
                $login_failure = AuthFailureType::DoesNotExist;
                $logEvent("User '$username' failed to initiated a login (user does not exist)");
            }

            else
            {
                $this_user = (object) $res->fetch_assoc();

                if($this_user->Login_count >= MAX_LOGIN_ATTEMPTS)
                {
                    $login_failure = AuthFailureType::Locked;
                    $logEvent("User '{$this_user->Login_id}' failed to initiated a login (locked)");
                }

                if($this_user->Login_pw !== $password)
                {
                    // That username+password combo doesn't exist
                    $login_failure = AuthFailureType::BadCredentials;
                    $logEvent("User '{$this_user->Login_id}' failed to initiated a login (password mismatch)");

                    $this_user->Login_count += 1;
                    $login_attempts_remaining = MAX_LOGIN_ATTEMPTS - $this_user->Login_count;

                    $mysqli->query("UPDATE login SET Login_count = {$this_user->Login_count} WHERE Login_id = '$username' LIMIT 1");

                    if($this_user->Login_count >= MAX_LOGIN_ATTEMPTS)
                    {
                        $login_failure = AuthFailureType::Locked;
                        $logEvent("User '{$this_user->Login_id}' was locked for more than MAX_LOGIN_ATTEMPTS bad attempts");
                    }
                }

                else if($this_user->Login_status == LoginStatus::Banned)
                {
                    $login_failure = AuthFailureType::Banned;
                    $logEvent("User '{$this_user->Login_id}' failed to initiated a login (banned)");
                }

                else
                {
                    $_SESSION['user'] = [
                        'id' => $this_user->idLogin,
                        'username' => $this_user->Login_id,
                        'status' => $this_user->Login_status,
                        'is_admin' => $this_user->Login_status == LoginStatus::Admin,
                        'count' => 0
                    ];

                    $logEvent("User '{$this_user->Login_id}' initiated a SUCCESSFUL login after {$this_user->Login_count} attempts");

                    // reset count to 0 to clear all previous attempts if they successfully login
                    $mysqli->query("UPDATE login SET Login_count = 0 WHERE Login_id = '$username' LIMIT 1");
                    
                    if($this_user->Login_status == LoginStatus::UserNeverLoggedIn)
                        $redirect_to('virgin.php');
                    else
                        $redirect_to('index.php');
                }
            }
        }
    }
?>
<?php require('header.php') ?>
<?php
        if($login_failure):
?>
        <p>The following errors occurred:</p>
        <ul>
            <?= $login_failure == AuthFailureType::BadCredentials
                ? "<li>The username/password do not match (you have {$login_attempts_remaining} attempts remaining)</li>"
                : ''
            ?>
            <?= $login_failure == AuthFailureType::DoesNotExist ? '<li>The is not an account registered with this service</li>' : '' ?>
            <?= $login_failure == AuthFailureType::Banned ? '<li>This account is banned and cannot login</li>' : '' ?>
            <?= $login_failure == AuthFailureType::Locked ? '<li>This account is locked and must be unlocked by an administrator</li>' : '' ?>
            <?= $login_failure & AuthFailureType::MissingUsername ? '<li>You forgot to specify a username!</li>' : '' ?>
            <?= $login_failure & AuthFailureType::MissingPassword ? '<li>You forgot to specify a password!</li>' : '' ?>
        </ul>
<?php endif; ?>
        <form method="POST" action="auth.php">
            <label>Username:</label><input type="text" name="username" id="username" /><br />
            <label>Password:</label><input type="password" name="password" id="password" /><br />
            <input type="submit" name="submit" />
        </form>
<?php require('footer.php') ?>
