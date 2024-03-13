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
	    <label for="username">Instructors Name</label>
                <input type="text" name="name" placeholder="Enter" required>
        <label for="username">Topic</label>
		<input type="text" name="course" placeholder="Enter" required>

        <label for="username">Overall how would you rate this presentation?</label>
        <select>  
            <option value="Select">Select</option>  
            <option value="Excellent">Excellent</option>   
            <option value="Good">Good</option>  
            <option value="Fair">Fair</option>  
            <option value="Poor">Poor</option>  
        </select>

        <!-- code for check boxes (check all) -->
        <label for="username">In your opinion, the presentor... (check all that apply)</label><br>
        <input type="checkbox" id="respected" name="opinions[]" value="Respected the participants">
        <label for="respected">Respected the participants</label><br>
        <input type="checkbox" id="not_respected" name="opinions[]" value="Did not respect the participants">
        <label for="not_respected">Did not respect the participants</label><br>
        <input type="checkbox" id="managed_well" name="opinions[]" value="Managed the group well">
        <label for="managed_well">Managed the group well</label><br>
        <input type="checkbox" id="managed_poorly" name="opinions[]" value="Managed the group poorly">
        <label for="managed_poorly">Managed the group poorly</label><br>
        <input type="checkbox" id="explained_clearly" name="opinions[]" value="Explained things clearly">
        <label for="explained_clearly">Explained things clearly</label><br>
        <input type="checkbox" id="made_confusing" name="opinions[]" value="Made things confusing">
        <label for="made_confusing">Made things confusing</label><br>
        <input type="checkbox" id="responsive" name="opinions[]" value="Was responsive to questions">
        <label for="responsive">Was responsive to questions</label><br>
        <input type="checkbox" id="enthusiasm" name="opinions[]" value="Exhibited enthusiasm for the topic">
        <label for="enthusiasm">Exhibited enthusiasm for the topic</label><br><br>

        <!-- code for check boxes (only one) -->
        <label for="username">After taking this training I feel like I have an increased understanding of domestic violence.</label><br>
        <input type="radio" id="true" name="understanding" value="True">
        <label for="true">True</label><br>
        <input type="radio" id="false" name="understanding" value="False">
        <label for="false">False</label><br>
        <input type="radio" id="significant" name="understanding" value="Came to this training with significant DV knowledge">
        <label for="significant">I came to this training with significant DV knowledge</label><br><br>

        <label for="username">Through this training I have learned new information or asquired a new skill and/or resource that I can apply in my work to improve my response to domestic violence.</label><br>
        <input type="radio" id="true" name="understanding" value="True">
        <label for="true">True</label><br>
        <input type="radio" id="false" name="understanding" value="False">
        <label for="false">False</label><br>
        <input type="radio" id="significant" name="understanding" value="Came to this training with significant DV knowledge">
        <label for="significant">I came to this training with significant DV knowledge</label><br><br>
	    <input type="submit" name="add_feedback" value="Add Feedback">
	</form>
    </body>
</html>