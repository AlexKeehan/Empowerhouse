<?php
//require_once 'database\dbinfo.php'; //need to get this to point to dbinfo in the correct folder
$con = mysqli_connect("localhost","duplicate","duplicate","duplicate");
//having problems including dbinfo.php, so for the demo I hardcoded the connection here
//make sure the values are replaced with the correct info, it should match dbinfo.php
//include_once('database/dbinfo.php');
//include_once(dirname(__FILE__).'/database/dbinfo.php');

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

    //my code, comment out if not ready in time
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //echo "hello world"; //okay this works for debugging
        $semester = $_POST["training-period"];
        $year = $_POST["yeardropdown"];
        //does not require error checking for adding to past years because the year dropdown is now dynamic
        //may require error checking for adding training periods that've already passed
        //but that could also be allowed, it wouldn't cause any problems to add them

        //calculating start and end dates given the semester and year values
        //these values are hardcoded, we should redo this later to make it dynamic
        //if we create relative dates for each semester and then use those in the form, it would only need changing 1 place
        switch($semester){
            case "Spring":
                //echo "spring";
                $startDate = "$year-01-01";
                $endDate = "$year-02-28";
                break;
            case "Summer":
                //echo "summer";
                $startDate = "$year-05-01";
                $endDate = "$year-06-30";
                break;
            case "Fall":
                //echo "fall";
                $startDate = "$year-09-01";
                $endDate = "$year-10-31";
                break;
            default :
                //echo "default";
                break;
            }

        $query = "INSERT INTO `dbtrainingperiods` (`id`, `name`, `startdate`, `enddate`) VALUES (NULL, '$semester $year', '$startDate', '$endDate')";
        try{
            //echo "checkpoint";
            $result= mysqli_query($con, $query);
        } catch (Exception $e) {
            echo "training-period already present in database";
        }
        //echo "hello world";
        //header("Location: addTrainingPeriod.php");
    }
    /*
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $added_year = isset($_POST['year']) ? $_POST['year'] : false;
        $year = date("Y");

        $difference = (int)$added_year - (int)$year;

        if (!preg_match("/^(\d{4})$/", $added_year, $year)) {
             $_SESSION['error'] = 'Incorrectly Formatted Year';
        }
        else if ($difference < 0) {
            $_SESSION['error'] = 'Cannot Add Training Periods To Past Years';
        }
        else {
            header("Location: addTrainingPeriod.php");
        }
    }*/

    if ($_SESSION['error'] != "") {
        echo $_SESSION['error'];
        $_SESSION['error'] = "";
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Empowerhouse VMS | Create Event</title>
    </head>
    <body>
        <?php require_once('header.php')?>
        

        <h1>Select Training Period</h1>
        <main>
            <h2>Available Training Periods</h2>
            <form method="post">
                <select name="training-period">
                <option value="Spring">Jan 1 - Feb 28</option>
                <option value="Summer">May 1 - Jun 30</option>
                <option value="Fall">Sep 1 - Oct 31</option>
                </select>
            <h3>Select Year</h3>
                <form method="post">
                <select name="yeardropdown" id="yeardropdown">
                <script>
                let dateDropdown = document.getElementById('yeardropdown'); 
                let currentYear = new Date().getFullYear();    
                let furthestYear = currentYear+50; //50 years into the future
                while (currentYear <= furthestYear) {      
                    let dateOption = document.createElement('option');          
                    dateOption.text = currentYear;      
                    dateOption.value = currentYear;        
                    dateDropdown.add(dateOption);      
                    currentYear += 1;    
                }
                </script>
                </select>
                <input type="submit" name="Submit">

            <!--
                <h3>Select Year</h3>
            <form method="post">
                <input type="text" id="year" name="year" required placeholder="Enter Year">
            <input type="submit" name="Submit">
        -->
            </form> 
        </main>
    </body>
</html>
