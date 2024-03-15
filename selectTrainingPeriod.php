<?php
    // Page for an Admin to select a Training Period.
    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    } 
    // Require admin privileges
    if ($accessLevel < 2) {
        header('Location: login.php');
        echo 'bad access level';
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $added_year = isset($_POST['year']) ? $_POST['year'] : false;
        $year = date("Y");

        if (!preg_match("/^(\d{4})$/", $added_year, $year)) {
            echo 'Incorrectly Formatted Year';
            //header("Location: selectTrainingPeriod.php");
            die();
        }
        else if ((int)$year > (int)$added_year) {
            echo 'Cannot Add Training Periods To Past Years';
            die();
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Empowerhouse VMS | Create Event</title>
    </head>
    <body>
        <?php require_once('header.php')
        ?>
        <h1>Select Training Period</h1>
        <main>
            <h2>Available Training Periods</h2>
            <form method="post">
                <select name="training-periods">
                <option value="first">Jan 1 - Feb 28</option>
                <option value="second">May 1 - Jun 30</option>
                <option value="third">Sep 1 - Oct 31</option>
                </select>
            <h3>Select Year</h3>
            <form method="post">
                <input type="text" id="year" name="year" required placeholder="Enter Year">
            <input type="submit" name="Submit">
            </form>
        </main>
    </body>
</html>
