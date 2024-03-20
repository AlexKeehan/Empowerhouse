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
	    <label for="instructorName">Instructors Name</label>
        <select name="instructorname" placeholder="Enter" required>
            <option value="" disabled selected>Enter</option>
            <?php
                include_once('database/dbinfo.php');
                include_once('domain/Person.php');
                include_once('database/dbPersons.php');
                $conn = connect();

                // Query to fetch active trainers' names
                $sql = "SELECT first_name, last_name FROM dbpersons WHERE type = 'trainer' AND status = 'Active'";
                $result = $conn->query($sql);

                // If trainers found, populate dropdown menu
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["first_name"] . " " . $row["last_name"] . "'>" . $row["first_name"] . " " . $row["last_name"] . "</option>";
                    }
                } else {
                    echo "<option value=''>No active trainers found</option>";
                }

                // Close connection
                $conn->close();
            ?>
        </select><br>
        <label for="topic">Topic</label>
		<input type="text" name="topic" placeholder="Enter" required>

        <label for="overallrating">Overall how would you rate this presentation?</label>
        <select name = "overallrating">
            <option value="" disabled selected>Enter</option>
            <option value="excellent">Excellent</option>
            <option value="good">Good</option>  
            <option value="fair">Fair</option>
            <option value="poor">Poor</option>
        </select>

        <!-- code for check boxes (check all) -->
        <label for="username">In your opinion, the presentor... (check all that apply)</label><br>
        <input type="checkbox" id="respected" name="opinions[]" value="Respected the participants">
        <label for="respected">Respected the participants</label><br>
        <input type="checkbox" id="managed_well" name="opinions[]" value="Managed the group well">
        <label for="managed_well">Managed the group well</label><br>
        <input type="checkbox" id="explained_clearly" name="opinions[]" value="Explained things clearly">
        <label for="explained_clearly">Explained things clearly</label><br>
        <input type="checkbox" id="responsive" name="opinions[]" value="Was responsive to questions">
        <label for="responsive">Was responsive to questions</label><br>
        <input type="checkbox" id="enthusiasm" name="opinions[]" value="Exhibited enthusiasm for the topic">
        <label for="enthusiasm">Exhibited enthusiasm for the topic</label><br><br>

        <!-- code for check boxes (only one) -->
        <label for="increasednderstanding">After taking this training I feel like I have an increased understanding of domestic violence.</label><br>
        <input type="radio" id="true" name="increasednderstanding" value="True">
        <label for="increasednderstanding">True</label><br>
        <input type="radio" id="false" name="increasednderstanding" value="False">
        <label for="increasednderstanding">False</label><br>
        <input type="radio" id="Significant Knowledge" name="increasednderstanding" value="Significant Knowledge">
        <label for="SignificantKnowledge1">Came to training with significant DV knowledge</label><br><br>

        <label for="learnedNewInfo">Through this training I have learned new information or aquired a new skill and/or resource that I can apply in my work to improve my response to domestic violence.</label><br>
        <input type="radio" id="true" name="learnedNewInfo" value="True">
        <label for="learnedNewInfo">True</label><br>
        <input type="radio" id="false" name="learnedNewInfo" value="False">
        <label for="learnedNewInfo">False</label><br>
        <input type="radio" id="Significant Knowledge" name="learnedNewInfo" value="Significant Knowledge">
        <label for="learnedNewInfo">Came to training with significant DV knowledge</label><br><br>
        
        <label for="improved">How could this training be improved?</label>
        <input type="text" name="improved" placeholder="Enter">

        <label for="interesting">What information did you find most helpful or interesting?</label>
		<input type="text" name="interesting" placeholder="Enter">

	    <input type="submit" name="add_feedback" value="Add Feedback">
	</form>
    </body>
</html>

<?php
    include_once('database/dbinfo.php');
    include_once('domain/Person.php');
    include_once('database/dbPersons.php');


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $instructorName = $_POST['instructorname'];
        $topic = $_POST['topic'];
        $OverallRating = $_POST['overallrating'];

        $respectsParticipants = '';
        $manageGroup = '';
        $clarityExplanation = '';
        $responsiveToQuestions = '';
        $enthusiasmForTopic = '';

        // Check if opinions array exists in form data
        if(isset($_POST['opinions'])) {
            $opinions = $_POST['opinions'];
            // Set checkbox values if they exist in the form data
            $respectsParticipants = in_array('Respected the participants', $opinions) ? "yes" : "no";
            $manageGroup = in_array('Managed the group well', $opinions) ? "yes" : "no";
            $clarityExplanation = in_array('Explained things clearly', $opinions) ? "yes" : "no";
            $responsiveToQuestions = in_array('Was responsive to questions', $opinions) ? "yes" : "no";
            $enthusiasmForTopic = in_array('Exhibited enthusiasm for the topic', $opinions) ? "yes" : "no";
        }

        $increasedUnderstanding = $_POST['increasednderstanding'];
        $learnedNewInfo = $_POST['learnedNewInfo'];
        $improved = $_POST['improved'];
        $interesting = $_POST['interesting'];

        // Create database connection
        $conn = connect();

        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        // Check if the user is a volunteer
        $isVolunteer = false;
        if ($person && $person->get_type() === 'volunteer') {
            $isVolunteer = true;
        }
        // If not a volunteer, display error message and stop further execution
        if (!$isVolunteer) {
            echo "Error: You do not have the proper permission to submit.";
        } else {
            // Prepare SQL statement
            $sql = "INSERT INTO dbevaluations (InstructorName, Topic, OverallRating, RespectsParticipants, ManageGroup, ClarityExplanation, ResponsiveToQuestions, EnthusiasmForTopic, IncreasedUnderstanding, LearnedNewInfo, Improvements, HelpfullInformation)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            // Prepare and bind parameters
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssss", $instructorName, $topic, $OverallRating, $respectsParticipants, $manageGroup, $clarityExplanation, $responsiveToQuestions, $enthusiasmForTopic, $increasedUnderstanding, $learnedNewInfo, $improved, $interesting);
        
            // Execute the statement
            if ($stmt->execute()) {
                echo "New record inserted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            // Close statement
            $stmt->close();
            
        }

        //Close connection
        $conn->close();
    }
?>