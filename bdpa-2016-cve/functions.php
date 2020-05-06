<?php
    $logEvent = function($details) use ($mysqli)
    {
        $mysqli->query("INSERT INTO log (Log_details, Log_datecreated) VALUES (\"$details\", now())");
    };

    $redirect_to = function($uri)
    {
        header("Location: $uri");
        exit();
    };

    $are_signups_enabled = function() use ($mysqli)
    {
        $res_enabled = $mysqli->query('SELECT * FROM log WHERE Log_details = "' . SystemMessages::SignupsEnabled . '"')->num_rows;
        $res_disabled = $mysqli->query('SELECT * FROM log WHERE Log_details = "' . SystemMessages::SignupsDisabled . '"')->num_rows;

        return $res_disabled - $res_enabled <= 0;
    };

    $echoUserBuilderForm = function()
    {

    };

    $echoCVEBuilderForm = function()
    {

    };
