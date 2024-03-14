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
        <input type="text" id="instructorName" name="instructorName" placeholder="Enter" required>
        <label for="topic">Topic</label>
		<input type="text" name="topic" placeholder="Enter" required>

        <label for="username">Overall how would you rate this presentation?</label>
        <select>
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
        <label for="understanding">After taking this training I feel like I have an increased understanding of domestic violence.</label><br>
        <input type="radio" id="true" name="understanding" value="True">
        <label for="true1">True</label><br>
        <input type="radio" id="Significant Knowledge" name="newinfo" value="True">
        <label for="SignificantKnowledge1">Came to training with significant DV knowledge</label><br><br>

        <label for="newinfo">Through this training I have learned new information or aquired a new skill and/or resource that I can apply in my work to improve my response to domestic violence.</label><br>
        <input type="radio" id="true" name="newinfo" value="True">
        <label for="true2">True</label><br>
        <input type="radio" id="false" name="newinfo" value="False">
        <label for="false2">False</label><br>
        <input type="radio" id="Significant Knowledge" name="newinfo" value="True">
        <label for="SignificantKnowledge2">Came to training with significant DV knowledge</label><br><br>
        
	    <input type="submit" name="add_feedback" value="Add Feedback">
	</form>
    </body>
</html>

<?php
    require_once('database/dbinfo.php');


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $instructorName = $_POST['instructorName'];
        $topic = $_POST['topic'];
        $courseID = $_POST['courseID'];
        $overallRating = $_POST['overallRating'];
        $respectsParticipants = $_POST['respectsParticipants'] == 'True' ? 1 : 0;
        $manageGroup = $_POST['manageGroup'] == 'True' ? 1 : 0;
        $clarityExplanation = $_POST['clarityExplanation'] == 'True' ? 1 : 0;
        $responsiveToQuestions = $_POST['responsiveToQuestions'] == 'True' ? 1 : 0;
        $enthusiasmForTopic = $_POST['enthusiasmForTopic'] == 'True' ? 1 : 0;
        $increasedUnderstanding = $_POST['increasedUnderstanding'];
        $learnedNewInfo = $_POST['learnedNewInfo'];

        // Create database connection
        $conn = connect();

        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Prepare SQL statement
        $sql = "INSERT INTO dbevaluations (InstructorName, Topic, CourseID, OverallRating, RespectsParticipants, ManageGroup, ClarityExplanation, ResponsiveToQuestions, EnthusiasmForTopic, IncreasedUnderstanding, LearnedNewInfo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare and bind parameters
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssiiiiiss", $instructorName, $topic, $courseID, $overallRating, $respectsParticipants, $manageGroup, $clarityExplanation, $responsiveToQuestions, $enthusiasmForTopic, $increasedUnderstanding, $learnedNewInfo);

        // Execute SQL statement
        if (mysqli_stmt_execute($stmt)) {
            echo "New record inserted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // Close statement and connection
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
        // Redirect back to the form if accessed directly
        header("Location: evaluation_form.php");
        exit();
    }
?>

