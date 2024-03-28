<?php
/**
 * making this for standardization to match other database phps.
 * all phps that interact with a particular db use one of these to centralize all operations
 * currently dbtrainingperiods is only accessed by selectTrainingPeriod.php so this isn't needed yet
 * but if any new php files interact with dbtrainingperiods then it could lead to duplicate code across multiple files
 * @ Authors Chris Cronin & Alex Keehan
 * @ Version March 27 2024
 **/
include_once('dbinfo.php');
//include_once(dirname(__FILE__).'/../domain/TrainingPeriod.php'); //should I have this line?


/* Query dbtrainingperiods table for entries with matching years
    Returns either false or an array with the query results
*/
function get_training_periods_by_semester_and_year($semester, $year) {
    $connection = connect();
    $query = "select * from dbtrainingperiods
              where year = '$year' and semester = '$semester' order by startDate asc";
    try{
        $results= mysqli_query($connection, $query);
    } catch (Exception $e) {
        echo "No Training Periods With That Semester & Year Found";
        return null;
    }
    $periods = [];
    foreach ($results as $row) {
        $periods[] = $row;
    }
    mysqli_close($connection);
    return $periods;
}

/*
    Insert a new training period into the dbtrainingperiods table
    Returns null if insertion failed. Otherwise, returns id
 */
function add_trainingperiod($trainingperiod) {
    $connection=connect();
    $semester = $trainingperiod[0];
    $year = $trainingperiod[1];
    $startDate = $trainingperiod[2];
    $endDate = $trainingperiod[3];

    $query = "SELECT * FROM dbtrainingperiods WHERE semester = '" . $semester . "' and year = '" . $year . "'";
    $result = mysqli_query($connection,$query);
    //if there's no entry for this semester & year, then add it
    if ($result == null || mysqli_num_rows($result) == 0) {
        $new_row = mysqli_query($connection,'INSERT INTO dbtrainingperiods (semester, year, startDate, endDate) VALUES ("' .$semester . '","' .$year . '","' . $startDate . '","' .$endDate .'");');			
        if (!$new_row) {
            return null;
        }
        $id = mysqli_insert_id($connection);
        mysqli_commit($connection);
        mysqli_close($connection);
        return $id;
    }
}

/* Update the year for a training period given the id
    Returns true or false depending on success of update
*/
function update_training_period_year($id, $new_year) {
    $con=connect();
	$query = 'UPDATE dbtrainingperiods SET year = "' . $new_year . '" WHERE id = "' . $id . '"';
	try{
        $result= mysqli_query($connection, $query);
    } catch (Exception $e) {
        echo "Updating Unsuccessful";
        return null;
    }
	mysqli_close($con);
	return $result;
}

/* Retrieve training periods by their id
    Returns false if search failed. Otherwise, return query result
*/
function get_training_period_by_id($id) {
    $con=connect();
    $query = "SELECT * FROM dbtrainingperiods WHERE id = '" . $id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
    mysqli_close($con);
    return false;
}
    $result_row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    return $result_row;
}