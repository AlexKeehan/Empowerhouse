<?php
/** 
 * Page for Course Mangagement: Update, Add, or Remove a Course from dbCourses
 * @ Author Emily Lambert
 * @ Version April 5 2024
 **/
 //Include necessary files
include_once('database/dbCourses.php'); 
include_once('database/dbPersons.php');
include_once('domain/Person.php');

// Check if user is logged in and has appropriate access level
session_cache_expire(30);
session_start();
if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
    // Redirect if user does not have sufficient access level
    header('Location: logout.php');
    die();
}

// Get current user information
if (isset($_SESSION['_id'])) {
    $person = retrieve_person($_SESSION['_id']);
}

// Check user type
$admin = $person->get_type() == 'admin';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require('universal.inc'); ?>
    <title>Course Management | Empowerhouse VMS</title>
</head>
<body>
    <?php require('header.php'); ?>
    <h1>Course Management</h1>
    <main class='dashboard'>
        <p>Welcome back, <?php echo $person->get_first_name() ?>!</p> 
        <div id="dashboard">
            <?php if ($_SESSION['access_level'] >= 2): ?>
                <div class="dashboard-item" data-link="addCourse.php">
                    <img src="images/new-event.svg">
                    <span>Add Course</span>
                </div>
                <div class="dashboard-item" data-link="updateCourse.php">
                    <img src="images/pen-to-square.svg">
                    <span>Update Course</span>
                </div>
                <div class="dashboard-item" data-link="removeCourse.php">
                    <img src="images/delete.svg">
                    <span>Remove Course</span>
                </div>
            <?php endif ?>
            <div class="dashboard-item" data-link="index.php">
                <img src="images/arrow-left.svg">
                <span>Back to Dashboard</span>
            </div>
        </div>
    </main>
</body>
</html>
