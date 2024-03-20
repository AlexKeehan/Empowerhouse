<<?php
    session_cache_expire(30);
    session_start();

    date_default_timezone_set("America/New_York");
    
    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        if (isset($_SESSION['change-password'])) {
            header('Location: changePassword.php');
        } else {
            header('Location: logout.php');
        }
        die();
    }
    include
    include_once('database/dbPersons.php');
    include_once('domain/Person.php');
    include_once('database/dbEvaluations.php');
    // Get date?
    if (isset($_SESSION['_id'])) {
        $person = retrieve_person($_SESSION['_id']);
    }
    $notRoot = $person->get_id() != 'vmsroot';
    $first_name = $person->get_first_name();
    $last_name = $person->get_last_name();
    $name = $last_name . " ". $first_name;
    ?>
    <!Doctype html>
    <html>
        <head>
        <?php require_once('universal.inc') ?>
            <title>Empower House VMS | Verify</title>
        </head>
        <body>
            <?php 
            require_once('header.php');
            include_once('database/dbEvaluations.php');
            $evals = get_evalutaion_by_Instructor("Test");
            $eval = $evals[0];
            echo "<h1> Select an evaluation to view: </h1>";
            echo "<p>". $eval["Topic"] . "</p>";
            ?>
        </body>
    </html>