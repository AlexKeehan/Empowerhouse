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
    include_once('database/dbinfo.php');
    include_once('database/dbPersons.php');
    include_once('domain/Person.php');
    include_once('database/dbEvaluations.php');
    $conn = connect();

    // Get date?
    if (isset($_SESSION['_id'])) {
        $person = retrieve_person($_SESSION['_id']);
    }
    //$notRoot = $person->get_id() != 'vmsroot';
    //$first_name = $person->get_first_name();
    //$last_name = $person->get_last_name();
    //$name = $last_name . " ". $first_name;
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
            include_once('database/dbinfo.php');
            include_once('database/dbPersons.php');
            include_once('domain/Person.php');
            // $conn = connect();
            // $query = "SELECT e.InstructorName, e.Topic, e.OverallRating,
            // e.RespectsParticipants, e.ManageGroup, e.ClarityExplanation,
            // e.ResponsiveToQuestions, e.EnthusiasmForTopic, e.IncreasedUnderstanding,
            // e.LearnedNewInfo, e.Improvements, e.HelpfullInformation,
            // CONCAT(p.first_name, ' ', p.last_name) AS TrainerName
            // FROM dbevaluations e 
            // INNER JOIN dbpersons p ON e.InstructorName = CONCAT(p.first_name, ' ', p.last_name)
            // WHERE p.type = 'trainer' AND p.status = 'Active'";
            // $result = mysqli_query($conn, $query);

            // if (mysqli_num_rows($result) > 0) {
            //     echo '<table>';
            //     echo '<tr><th>Instructor Name</th><th>Topic</th><th>Overall Rating</th></tr>';
            //     while ($row = mysqli_fetch_assoc($result)) {
            //         echo '<tr class="evaluation">';
            //         echo '<td class="instructor-name">' . $row['TrainerName'] . '</td>';
            //         echo '<td>' . $row['Topic'] . '</td>';
            //         echo '<td>' . $row['OverallRating'] . '</td>';
            //         echo '</tr>';
            //         // Additional hidden row for detailed evaluation
            //         echo '<tr class="evaluation-details">';
            //         foreach ($row as $key => $value) {
            //             echo '<td>' . $value . '</td>';
            //         }
            //         echo '</tr>';
            //     }
            //     echo '</table>';
            // } else {
            //     echo 'No evaluations found for active trainers.';
            // }
            $f_name = $person->get_first_name();
            $l_name = $person->get_last_name();
            $name = $f_name . " " . $l_name;
            $evals = get_evalutaion_by_Instructor($name);
            // $eval = $evals[0];
            echo "<h1> Select an evaluation to view: </h1>";
            $i = 0;
            if(!$evals){
                echo "<p> " . $name . " has no evaluations to view </p>";
            }
            else{
                echo "<form method = \"post\">";
                echo "<select name = \"eval\">";
                foreach ($evals as &$eval){
                    echo "<option value=". $i +1 . "> ". ($i + 1) . ") " .$eval["InstructorName"] . "| Topic: ".$eval["Topic"] . "</option>";
                    $i++;
                }
                echo "</select>";
                echo "<input type=\"submit\" name=\"submit\"/>";
                echo "</form>";
                if(!empty($_POST["eval"])){
                $eval_num = $_POST["eval"];
                $eval_num--;
                $curr_eval = $evals[$eval_num];
                echo "<p> eval number ". $eval_num + 1 . "</p>";
                echo "<p> topic: ".$curr_eval["Topic"] . "</p>";
                echo "<p> rating: ".$curr_eval["OverallRating"] . "</p>";
                echo "<p> respected participnts: ".$curr_eval["RespectsParticipants"] . "</p>";
                echo "<p> managed group well : ".$curr_eval["ManageGroup"] . "</p>";
                echo "<p> provided clear explainations: ".$curr_eval["ClarityExplanation"] . "</p>";
                echo "<p> was responsive to questions: ".$curr_eval["ResponsiveToQuestions"] . "</p>";
                echo "<p> showed enthusiasm: ".$curr_eval["EnthusiasmForTopic"] . "</p>";
                echo "<p> increased understanding: ".$curr_eval["IncreasedUnderstanding"] . "</p>";
                echo "<p> learned new info: ".$curr_eval["LearnedNewInfo"] . "</p>";
                echo "<p> What could be improved: ".$curr_eval["Improvements"] . "</p>";
                echo "<p> Helpful Info: ".$curr_eval["HelpfullInformation"] . "</p>";
                }
            }
            ?>
            <!-- <script>
                document.querySelectorAll('.evaluation').forEach(item => {
                    item.addEventListener('click', event => {
                        const detailsRow = item.nextElementSibling;
                        detailsRow.classList.toggle('expanded');
                    });
                });
        </script> -->
        </body>
    </html>