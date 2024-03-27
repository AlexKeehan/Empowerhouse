<?php
/**
*@ Authors Chris Cronin & Alex Keehan
*@ Version March 27 2024
**/

//these two lines are the code snippet needed by all .php files to connect to the database
require_once('database/dbinfo.php'); //or another .php file which in turn includes dbinfo.php
$con = connect();

//defining the start and end dates for semesters in one location so it is easier to change them if needed
//format is yyyy-mm-dd, include the dashes but exclude the year here. Year will be added dynamically further down
$springStartDate = "-01-01";
$springEndDate = "-02-28";
$summerStartDate = "-05-01";
$summerEndDate = "-06-30";
$fallStartDate = "-09-01";
$fallEndDate = "-10-31";

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

    require_once('database/dbTrainingPeriods.php');
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $semester = $_POST["training-period"];
        $year = $_POST["yeardropdown"];

        $_SESSION['semester'] = $_POST['training-period'];
        $_SESSION['year'] = $_POST['yeardropdown'];
        //does not require error checking for adding to past years because the year dropdown is now dynamic
        //may require error checking for adding training periods that've already passed within the year
        //but that could also be allowed, it wouldn't cause any problems to add them and it could have use for fixing missing data

        //calculating start and end dates given the semester and year values
        switch($semester){
            case "Spring":
                //echo "spring";
                $startDate = "$year$springStartDate"; //these are accepted by mysql for date data type
                $endDate = "$year$springEndDate";
                break;
            case "Summer":
                //echo "summer";
                $startDate = "$year$summerStartDate";
                $endDate = "$year$summerEndDate";
                break;
            case "Fall":
                //echo "fall";
                $startDate = "$year$fallStartDate";
                $endDate = "$year$fallEndDate";
                break;
            default : //default should never be reached
                //echo "default";
                die("Error: invalid Semester, default reached during switch statement");
                break;
            }

        /**
         * id is null because it auto-increments
         * semester is either Spring, Summer or Fall
         * startdate and endate were calculated earlier and are formatted so that mysql accepts them as input for the date data type
         */
        $query = [
            $semester,
            $year,
            $startDate,
            $endDate
        ];
        // Insert new training period into dbTrainingPeriods
        $result = add_trainingperiod($query);
        if (!$result) {
            echo "Training Period is already present in database";
        } else {
            //after hitting submit, route to next php if training period insertion is successful
            header('Location: addTrainingPeriod.php');
        }
    }

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
        <?php require_once('header.php')?> <!--check if this is actually used-->
        <h1>Select Training Period</h1>
        <main>
            <h2>Available Training Periods</h2>
            <form method="post">
                <select name="training-period"> <!--if any further training periods are added, add them to the list-->
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
                let furthestYear = currentYear+10; //10 years into the future
                while (currentYear <= furthestYear) { //dynamically creates a dropdown list of years from "current year" to "current year + 10"
                    let dateOption = document.createElement('option');          
                    dateOption.text = currentYear;      
                    dateOption.value = currentYear;        
                    dateDropdown.add(dateOption);      
                    currentYear += 1;    
                }
                </script>
                </select>
                <input type="submit" name="Submit">
            </form> 
        </main>
    </body>
</html>
