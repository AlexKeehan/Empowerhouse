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
    // Get date?
    if (isset($_SESSION['_id'])) {
        $person = retrieve_person($_SESSION['_id']);
    }
    $notRoot = $person->get_id() != 'vmsroot';
?>
<!DOCTYPE html>
<!-- start the html to display the input area -->
<html>
    <head>
	<?php require_once('universal.inc') ?>
        <title>Empower House VMS | Verify</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
	<!-- Start the Post so it can update the database -->
	<form method="post">
	    <label for="username">Enter Feedback</label>
                <input type="text" name="name" placeholder="Enter your name" required>
		<input type="text" name="course" placeholder="Enter the course ID" required>
		<input type="text" name="feedback" placeholder="Enter your feedback" required>
	    <input type="submit" name="add_feedback" value="Add Feedback">
        <label for="username">How would you rate Empowerhouse volunteer training?</label>
        <select>  
            <option value="Omveer">Excellent</option>   
            <option value="Dorilal">Good</option>  
            <option value="Sumit">Fair</option>  
            <option value="Vineet">Poor</option>  
        </select>
	</form>
    </body>
</html>