<?php
    $title = 'CVE Genesis';
    require('common.php');

    $signups_enabled = $are_signups_enabled();
    $entity = isset($_GET['type']) ? $_GET['type'] : Entities::None;
?>
<?php require('header.php') ?>
<?php
    if($entity == Entities::UserByAdmin)
    {
        if(!$is_admin)
            echo "<p>Error: You don't have permission to do that!</p>";

        else
            $echoUserBuilderForm();
    }

    else if($entity == Entities::UserByRegistration)
    {
        if(!$signups_enabled)
            echo "<p>Sorry, but signups are disabled at the moment. Try again later.</p>";
        else
            $echoUserBuilderForm();
    }

    else if($entity == Entities::CVENewOrEdit)
    {
        $id = isset($_GET['id']) ? $_GET['id'] : false;
        $echoCVEBuilderForm($id);
    }
?>
<?php require('footer.php') ?>
