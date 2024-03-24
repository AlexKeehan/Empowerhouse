<?php
/**
 * @version March 24, 2024
 * @author Alex Keehan
 */
include_once('dbinfo.php');
#include_once(dirname(__FILE__).'/../domain/Person.php');

/*
    Insert a new training period into the dbtrainingperiods table
    Returns null if insertion failed. Otherwise, returns id
*/
function insert_training_period($period) {
    $connection = connect();
    $semester = $period[0];
    $year = $period[1];
    $startDate = $period[2];
    $endDate = $period[3];

    $query = "
        insert into dbTrainingperiods (semester, year, startDate, endDate)
        values ('$semester', '$year', '$startDate', '$endDate')
    ";
    try{
        $result= mysqli_query($connection, $query);
    } catch (Exception $e) {
        echo "Training Period Already Present In Database";
        return null;
    }
    $id = mysqli_insert_id($connection);
    mysqli_commit($connection);
    mysqli_close($connection);
    return $id;
}

/* Query dbtrainingperiods table for entries with matching years
    Returns either false or an array with the query results
*/

function get_training_periods_by_year($year) {
    $connection = connect();
    $query = "select * from dbtrainingperiods
              where year = '$year' order by startTime asc";
    try{
        $results= mysqli_query($connection, $query);
    } catch (Exception $e) {
        echo "No Training Periods With That Year Found";
        return null;
    }
    require_once('include/output.php');
    $periods = [];
    foreach ($results as $row) {
        $periods []= hsc($row);
    }
    mysqli_close($connection);
    return $periods;
}

/* Removes a trainin period with the cooresponding id
    Returns boolean indicating if training period was deleted
*/
function remove_training_period($id) {
    query = "delete from dbtrainingperiods where id='$id'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $result = boolval($result);
    mysqli_close($connection);
    return $result;
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