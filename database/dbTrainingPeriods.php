<?php
/**
 * making this for standardization to match other database phps.
 * all phps that interact with a particular db use one of these to centralize all operations
 * currently dbtrainingperiods is only accessed by selectTrainingPeriod.php so this isn't needed yet
 * but if any new php files interact with dbtrainingperiods then it could lead to duplicate code across multiple files
 */

/**
 * @version March 25, 2024
 * @author Chris Cronin
 */
include_once('dbinfo.php');
//include_once(dirname(__FILE__).'/../domain/TrainingPeriod.php'); //should I have this line?


//add training period to dbTrainingPeriods table

function add_trainingperiod($trainingperiod) {
    if (!$trainingperiod instanceof TrainingPeriod)
        die("Error: add_trainingperiod type mismatch");
    $con=connect();

    $query = "SELECT * FROM dbtrainingperiods WHERE id = '" . $trainingperiod->get_id() . "'"; //id might be wrong choice here
    $result = mysqli_query($con,$query);
    //if there's no entry for this id, add it
    //might be better to check for name, since that will also be unique and id is auto-incremented by the table
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_query($con,'INSERT INTO dbtrainingperiods VALUES("' .
                $trainingperiod->get_id() . '","' .
                $trainingperiod->get_name() . '","' . 
                $trainingperiod->get_startdate() . '","' .
                $trainingperiod->get_enddate() .            
                '");');							
        mysqli_close($con);
        return true;
    }
    mysqli_close($con);
    return false;
}