<?php
    $title = 'CVE Reset Credentials';
    require('common.php');

    // Bounced
    if(!$is_logged_in || !$user->status == LoginStatus::UserNeverLoggedIn)
        $redirect_to('index.php');

    if($submission)
    {
        $password = isset($_POST['password']) ? $mysqli->escape_string($_POST['password']) : null;
        $confirmp = isset($_POST['confirm']) ? $mysqli->escape_string($_POST['confirm']) : null;

        if(empty($password) || empty($confirmp))
            $form_failure = AuthFailureType::MissingPassword;

        else if($password != $confirmp)
            $form_failure = AuthFailureType::PasswordNoMatch;

        else if(!preg_match('/[a-z]+/', $password)
                || !preg_match('/[A-Z]+/', $password)
                || !preg_match('/[0-9]+/', $password)
                || strlen($password) <= 10)
            $form_failure = AuthFailureType::PasswordInsecure;

        else
        {
            $mysqli->query("UPDATE login SET Login_pw = '{$password}', Login_status = '".LoginStatus::User."' WHERE Login_id = '{$user->username}' LIMIT 1");
            $_SESSION['user']['status'] = LoginStatus::User;
            $redirect_to('index.php');
        }
    }
?>
<?php
        if($form_failure):
?>
        <p>The following errors occurred:</p>
        <ul>
            <?= $form_failure == AuthFailureType::PasswordInsecure ? '<li>Your password is not secure enough. Please obey the instructions.</li>' : '' ?>
            <?= $form_failure == AuthFailureType::PasswordNoMatch ? '<li>The passwords you entered do not match.</li>' : '' ?>
            <?= $form_failure == AuthFailureType::MissingPassword ? '<li>You must enter a password!</li>' : '' ?>
        </ul>
<?php endif; ?>
<p>Your password must be longer than 10 characters and include an upper and lower-case letter and a number.</p>
<form method="POST" action="virgin.php">
    <label>New Password:</label><input type="password" name="password" id="password" /><br />
    <label>Confirm Password:</label><input type="password" name="confirm" id="confirm" /><br />
    <input type="submit" name="submit" />
</form>
