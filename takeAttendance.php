<?php
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
        
    include_once('database/dbPersons.php');
    include_once('domain/Person.php');

    if (isset($_SESSION['_id'])) {
        $person = retrieve_person($_SESSION['_id']);
    }
    $notRoot = $person->get_id() != 'vmsroot';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Empower House VMS | Verify</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <form method="post" action="takeAttendance-2.php"> <!-- Modified the action attribute -->
            <label for="courseName">Course Name</label>
            <select name="coursename" required>
                <option value="" disabled selected>Enter</option>
                <?php
                    include_once('database/dbinfo.php');
                    include_once('database/dbPersons.php');
                    $conn = connect();

                    $personId = $_SESSION['_id']; // Assuming this is the person's ID
                    // Query to fetch courses based on the person's ID from dbcoursesignup
                    $sql = "SELECT name FROM dbcourses;";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["name"] . "'>" . $row["name"] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No courses found</option>";
                    }

                    $conn->close();
                ?>
            </select><br>

            <input type="submit" name="Submit" value="Submit">
        </form>
    </body>
</html>

