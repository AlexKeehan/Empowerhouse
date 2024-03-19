<?php
/**
 * @version March 14, 2024
 * @author Matthew Rose and Evan Guard
 */
include_once('dbinfo.php');
#include_once(dirname(__FILE__).'/../domain/Person.php');

/**
 * returns an array of  associative arrays of course evaluations based on the instructors name
 * How to use data when called:
* index each evaluation by evaluations[x] ex: $eval = evaluations[1] will return the second evaluation
 * index each data field by eval["key"] ex: eval["Topic"] will return the Topic of the evaluation
 */
function get_evalutaion_by_Instructor($InstructorName) {
    $con=connect();
    $query = "SELECT * FROM dbEvaluations WHERE InstructorName = '" . $InstructorName . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) < 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    $evaluations = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $eval = $result_row;
        $evaluations[] = $eval;
    }
    return $evaluations;
}

/**
 * returns an array of  associative arrays of course evaluations based on the instructors name
 *  How to use data when called:
 * index each evaluation by evaluations[x] ex: $eval = evaluations[1] will return the second evaluation
 * index each data field by eval["key"] ex: eval["Topic"] will return the Topic of the evaluation
 */
function get_evalutaion_by_Topic($Topic) {
    $con=connect();
    $query = "SELECT * FROM dbEvaluations WHERE Topic = '" . $Topic . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) < 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    $topics = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $topic = $result_row;
        $topics[] = $topic;
    }
    return $topics;
}


?>