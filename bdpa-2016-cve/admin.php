<?php
    $title = 'CVE Admin';
    require('common.php');

    // Bounced
    if(!$is_admin)
        $redirect_to('index.php');

    $has_taken_action = isset($_SESSION['action']);

    $available_logs = [];
    $registered_users = [];
    $cves = [];

    $possible_actions = [
        'disablesu',
        'enablesu',
        'unlockUser',
        'unbanUser',
        'banUser',
        'deleteUser',
        'demoteUser',
        'promoteUser',
        'acceptCVE',
        'rejectCVE',
        'deleteCVE',
        'clearLogs'
    ];

    // If one of the actions in $possible_actions exists in $_GET, then some action is
    // going to be taken!
    $is_taking_action = count(array_diff($possible_actions, array_keys($_GET))) != count($possible_actions);

    if($is_taking_action)
    {
        $actions = array_intersect($possible_actions, array_keys($_GET));

        foreach($actions as $action)
        {
            $action_val = $_GET[$action];

            $logEvent("Admin {$user->username} is taking action '$action' ($action_val)");
            
            if($action == 'disablesu')
            {
                $mysqli->query('DELETE FROM log WHERE Log_details = "'
                               . SystemMessages::SignupsEnabled . '" or Log_details = "' . SystemMessages::SignupsDisabled . '"');
                $logEvent(SystemMessages::SignupsDisabled);
            }

            if($action == 'enablesu')
            {
                $mysqli->query('DELETE FROM log WHERE Log_details = "'
                               . SystemMessages::SignupsEnabled . '" or Log_details = "' . SystemMessages::SignupsDisabled . '"');
                $logEvent(SystemMessages::SignupsEnabled);
            }

            if($action == 'unlockUser')
                $mysqli->query("UPDATE login SET Login_count=0 WHERE idLogin=$action_val");

            if($action == 'unbanUser' || $action == 'demoteUser')
                $mysqli->query('UPDATE login SET Login_status=\''. LoginStatus::User ."' WHERE idLogin=$action_val");

            if($action == 'banUser')
                $mysqli->query('UPDATE login SET Login_status=\''. LoginStatus::Banned ."' WHERE idLogin=$action_val");

            if($action == 'deleteUser')
                $mysqli->query("DELETE FROM login WHERE idLogin=$action_val");

            if($action == 'promoteUser')
                $mysqli->query('UPDATE login SET Login_status=\''. LoginStatus::Admin ."' WHERE idLogin=$action_val");

            if($action == 'acceptCVE')
                $mysqli->query('UPDATE cve SET CVE_status=\''. CVEStatus::Approved ."' WHERE idCVE=$action_val");

            if($action == 'rejectCVE')
                $mysqli->query('UPDATE cve SET CVE_status=\''. CVEStatus::Denied ."' WHERE idCVE=$action_val");

            if($action == 'deleteCVE')
                $mysqli->query("DELETE FROM cve WHERE idCVE=$action_val");

            if($action == 'clearLogs')
                $mysqli->query('DELETE FROM log');
        }

        $_SESSION['action'] = true;
        $redirect_to('admin.php');
    }

    $signups_enabled = $are_signups_enabled();

    $res_logs = $mysqli->query('SELECT * FROM log');
    $res_users = $mysqli->query('SELECT * FROM login');
    $res_cves = $mysqli->query('SELECT * FROM cve JOIN category ca ON (cve.CVE_cat = ca.idCategory) ORDER BY CVE_status DESC');

    while(($row_log = $res_logs->fetch_assoc()) !== NULL)
    {
        $row_log = (object) $row_log;
        $available_logs[] = (object) [
            'datecreated' => $row_log->Log_datecreated,
            'details' => $row_log->Log_details
        ];
    }

    while(($row_user = $res_users->fetch_assoc()) !== NULL)
    {
        $row_user = (object) $row_user;
        $registered_users[] = (object) [
            'id' => $row_user->idLogin,
            'username' => $row_user->Login_id,
            'is_locked' => $row_user->Login_count >= MAX_LOGIN_ATTEMPTS,
            'is_admin' => $row_user->Login_status == LoginStatus::Admin,
            'is_root_admin' => $row_user->idLogin == ROOT_ADMIN_ID,
            'is_banned' => $row_user->Login_status == LoginStatus::Banned
        ];
    }

    while(($row_cve = $res_cves->fetch_assoc()) !== NULL)
    {
        $row_cve = (object) $row_cve;
        $cves[] = (object) [
            'id' => $row_cve->idCVE,
            'name' => $row_cve->CVE_id,
            'severity' => $row_cve->CVE_severity,
            'category' => $row_cve->category_name,
            'status' => $row_cve->CVE_status
        ];
    }
?>
<?php require('header.php') ?>
        <?= $has_taken_action ? '<p>Action Completed!</p>' : '' ?>
        <h3>CVEs + Pending</h3>
        <ul>
        <?php
            if(count($cves)):
                foreach($cves as $cve):
                    $cve_is_pending = $cve->status == CVEStatus::Pending;
                    $cve_is_approved = $cve->status == CVEStatus::Approved;
                    $cve_is_denied = $cve->status == CVEStatus::Denied;
        ?>
            <li>
                <?= $cve_is_pending ? '&lt;pending&gt; ' : '' ?>
                <?= $cve_is_approved ? '&lt;approved&gt; ' : '' ?>
                <?= $cve_is_denied ? '&lt;denied&gt; ' : '' ?>
                <a href="view.php?id=<?= $cve->id ?>"><?= $cve->name ?></a> (severity: <?= $cve->severity ?>)
                [
                    <?= $cve_is_pending
                        ? "<a href=\"admin.php?acceptCVE={$cve->id}\">Accept</a> | <a href=\"admin.php?rejectCVE={$cve->id}\">Reject</a> |"
                        : ''
                    ?>
                    <a href="admin.php?deleteCVE=<?= $cve->id ?>">Delete</a>
                ]
            </li>
        <?php
                endforeach;
            else:
        ?>
            <li>None</li>
        <?php
            endif;
        ?>
        </ul>

        <h3>Users</h3>
        <ul>
        <?php
            if(count($registered_users)):
                foreach($registered_users as $registered_user):
        ?>
            <li>
                <?= $registered_user->is_root_admin ? '&lt;root admin&gt; ' : '' ?>
                <?= $registered_user->is_admin && !$registered_user->is_root_admin ? '&lt;admin&gt; ' : '' ?>
                <?= $registered_user->username ?>
                <?php
                    $items = [];

                    if($registered_user->is_locked)
                        $items[] = "<a href=\"admin.php?unlockUser={$registered_user->id}\">Unlock</a>";

                    if(!$registered_user->is_admin)
                    {
                        $items[] = $registered_user->is_banned
                            ? "<a href=\"admin.php?unbanUser={$registered_user->id}\">Unban</a>"
                            : "<a href=\"admin.php?banUser={$registered_user->id}\">Ban</a>";

                        $items[] = "<a href=\"admin.php?deleteUser={$registered_user->id}\">Delete</a>";
                    }

                    if($is_root_admin && !$registered_user->is_root_admin)
                    {
                        $items[] = $registered_user->is_admin
                            ? "<a href=\"admin.php?demoteUser={$registered_user->id}\">Demote</a>"
                            : "<a href=\"admin.php?promoteUser={$registered_user->id}\">Promote</a>";
                    }

                    if(count($items))
                        echo '[ ', implode(' | ', $items), ' ]';
                ?>
            </li>
        <?php
                endforeach;
            else:
        ?>
            <li>No Users</li>
        <?php
            endif;
        ?>
        </ul>

        <h3>Logs</h3>
        <ul>
        <?php
            if(count($available_logs)):
                foreach($available_logs as $log):
        ?>
            <li><?= $log->datecreated ?>: <?= $log->details ?></li>
        <?php
                endforeach;
            else:
        ?>
            <li>No Logs</li>
        <?php
            endif;
        ?>
        </ul>
    
        <p><a href="admin.php?clearLogs">Clear logs</a> (will enable signups)</p>
        <p><?= $signups_enabled ? '<a href="admin.php?disablesu">Disable Signups</a>' : '<a href="admin.php?enablesu">Enable signups</a>' ?></p>

<?php require('footer.php') ?>
