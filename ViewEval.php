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
            $f_name = $person->get_first_name();
            $l_name = $person->get_last_name();
            $name = $f_name . " " . $l_name;
            $evals = get_evalutaion_by_Instructor($name);
            
            echo "<h1> Select an evaluation to view: </h1>";
            $i = 0;
            if(!$evals){
                echo "<p> " . $name . " has no evaluations to view </p>";
            }
            else{
                echo "<form method = \"post\">";
                echo "<select name = \"eval\">";
                foreach ($evals as &$eval){
                    echo "<option value=". $i +1 . "> ". ($i + 1) . ") " .$eval["InstructorName"] . "| Topic: ".$eval["Topic"] . "| Overall Rating: ". $eval["OverallRating"]. "</option>";
                    $i++;
                }
                echo "</select>";
                echo "<input type=\"submit\" name=\"submit\"/>";
                echo "</form>";
                if(!empty($_POST["eval"])){
                $eval_num = $_POST["eval"];
                $eval_num--;
                $curr_eval = $evals[$eval_num];
                echo "<h2> eval # ". $eval_num + 1 . "</h2>";
                echo "<h3> Instructor Name:</h3>";
                echo "<p> ". $curr_eval["InstructorName"] . "</p>";
                echo "<h3> Topic:</h3>";
                echo "<p> ".$curr_eval["Topic"] . "</p>";
                echo "<h3> Overall Rating:</h3>";
                echo "<p>".$curr_eval["OverallRating"] . "</p>";
                echo "<h3> In your opinion the instructor...:</h3>";
                echo "<p> respected participnts: ".$curr_eval["RespectsParticipants"] . "</p>";
                echo "<p> managed group well : ".$curr_eval["ManageGroup"] . "</p>";
                echo "<p> provided clear explainations: ".$curr_eval["ClarityExplanation"] . "</p>";
                echo "<p> was responsive to questions: ".$curr_eval["ResponsiveToQuestions"] . "</p>";
                echo "<p> showed enthusiasm: ".$curr_eval["EnthusiasmForTopic"] . "</p>";
                echo "<p> increased understanding: ".$curr_eval["IncreasedUnderstanding"] . "</p>";
                echo "<p> learned new info: ".$curr_eval["LearnedNewInfo"] . "</p>";
                echo "<h3> How could this training be improved?</h3>";
                echo "<p style='word-wrap: break-word; max-width: 95%;'>".$curr_eval["Improvements"] . "</p>";
                echo "<h3> What information did you find most helpful?</h3>";
                echo "<p style='word-wrap: break-word; max-width: 95%;'>".$curr_eval["HelpfullInformation"] . "</p>";
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