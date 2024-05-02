<?php
/*
 * Copyright 2013 by Jerrick Hoang, Ivy Xing, Sam Roberts, James Cook, 
 * Johnny Coster, Judy Yang, Jackson Moniaga, Oliver Radwan, 
 * Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker. 
 * This program is part of RMH Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */

/**
 * @version March 1, 2012
 * @author Oliver Radwan and Allen Tucker
 */

/* 
 * Created for Gwyneth's Gift in 2022 using original Homebase code as a guide
 */


include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Event.php');


$courses = array(
  "History",
  "Listening&Boundaries",
  "DomesticViolence101",
  "Legal1",
  "Legal2",
  "Diversity-Latinx/LGBTQ",
  "Court/Legal",
  "Shelter",
  "RCASA/MenFS",
  "MH/SU/SupGr/YthPr",
  "Hotline,CI,SP,DA",
  "Graduation"
);
$abvrcourses = array(
  "History",
  "L&B",
  "DV101",
  "Legal1",
  "Legal2",
  "Diversity",
  "Court/Legal",
  "Shelter",
  "RCASA/MenFS",
  "MH/SU/SupGr",
  "Hotline+",
  "Graduation"
);

/*
 * add an event to dbEvents table: if already there, return false
 */


function add_course($event) {
    if (!$event instanceof Event)
        die("Error: add_event type mismatch");
    $con=connect();
    $query = "SELECT * FROM dbCourses WHERE id = '" . $event->get_id() . "'";
    $result = mysqli_query($con,$query);
    //if there's no entry for this id, add it
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_query($con,'INSERT INTO dbEvents VALUES("' .
                $event->get_id() . '","' .
                $event->get_event_date() . '","' .
                $event->get_venue() . '","' .
                $event->get_event_name() . '","' . 
                $event->get_description() . '","' .
                $event->get_event_id() .            
                '");');							
        mysqli_close($con);
        return true;
    }
    mysqli_close($con);
    return false;
}

/*
 * remove an event from dbEvents table.  If already there, return false
 */

function remove_course($id) {
    $con=connect();
    $query = 'SELECT * FROM dbCourses WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $query = 'DELETE FROM dbEvents WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    mysqli_close($con);
    return true;
}


/*
 * @return an Event from dbEvents table matching a particular id.
 * if not in table, return false
 */

function retrieve_course($id) {
    $con=connect();
    $query = "SELECT * FROM dbCourses WHERE id = '" . $id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    return $result_row;
}

// not in use, may be useful for future iterations in changing how events are edited (i.e. change the remove and create new event process)
function update_course_date($id, $new_event_date) {
	$con=connect();
	$query = 'UPDATE dbEvents SET event_date = "' . $new_event_date . '" WHERE id = "' . $id . '"';
	$result = mysqli_query($con,$query);
	mysqli_close($con);
	return $result;
}

// update event volunteer list
function update_course_volunteer_list($eventID, $volunteerID) {
	$con=connect();
	$check = 'SELECT * FROM dbEventVolunteers WHERE eventID = "'.$eventID.'" AND userID = "'.$volunteerID.'" ';
	$result = mysqli_query($con, $check);
  $result_check = mysqli_fetch_assoc($result);
	if ($result_check > 0) {
			return 0;
	}
	$query = 'INSERT INTO dbEventVolunteers (eventID, userID) VALUES ("'.$eventID.'", "'.$volunteerID.'")';
	$result = mysqli_query($con, $query);
	mysqli_close($con);
	return $result;
}

function remove_volunteer_from_course($eventID, $volunteerID){
	$con = connect();
	$query = 'DELETE FROM dbEventVolunteers WHERE eventID = "'.$eventID.'" AND userID = "'.$volunteerID.'" ';
	$result = mysqli_query($con, $query);
	mysqli_close($con);
	return $result;
}


function make_a_course($result_row) {
	/*
	 ($en, $v, $sd, $description, $ev))
	 */
    $theEvent = new Event(
                    $result_row['event_name'],
                    $result_row['venue'],                   
                    $result_row['event_date'],
                    $result_row['description'],
                    $result_row['event_id']);  
    return $theEvent;
}


// retrieve only those events that match the criteria given in the arguments
function getonlythose_dbCourse($name, $day, $venue) {
   $con=connect();
   $query = "SELECT * FROM dbEvents WHERE event_name LIKE '%" . $name . "%'" .
           " AND event_name LIKE '%" . $name . "%'" .
           " AND venue = '" . $venue . "'" . 
           " ORDER BY event_name";
   $result = mysqli_query($con,$query);
   $theEvents = array();
   while ($result_row = mysqli_fetch_assoc($result)) {
       $theEvent = make_an_event($result_row);
       $theEvents[] = $theEvent;
   }
   mysqli_close($con);
   return $theEvents;
}

function fetch_courses_in_date_range($start_date, $end_date, $filter) {
    $connection = connect();
    $start_date = mysqli_real_escape_string($connection, $start_date);
    $end_date = mysqli_real_escape_string($connection, $end_date);
    if($filter == -1){
      $query = "select * from dbCourses
                where date >= '$start_date' and date <= '$end_date'
                order by startTime asc";
    }else{
      $query = "select * from dbCourses
                where date >= '$start_date' and date <= '$end_date'
                and eventId = $filter order by startTime asc";
    }
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    require_once('include/output.php');
    $events = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $key = $result_row['date'];
        if (isset($events[$key])) {
            $events[$key] []= hsc($result_row);
        } else {
            $events[$key] = array(hsc($result_row));
        }
    }
    mysqli_close($connection);
    return $events;
}

function fetch_course_on_date($date) {
    $connection = connect();
    $date = mysqli_real_escape_string($connection, $date);
    $query = "select * from dbEvents
              where date = '$date' order by startTime asc";
    $results = mysqli_query($connection, $query);
    if (!$results) {
        mysqli_close($connection);
        return null;
    }
    require_once('include/output.php');
    $events = [];
    foreach ($results as $row) {
        $events []= hsc($row);
    }
    mysqli_close($connection);
    return $events;
}

function fetch_courses_by_eventid($id) {
    $connection = connect();
    $id = mysqli_real_escape_string($connection, $id);
    $query = "select * from dbCourses where eventId = '$id'";
    $results = mysqli_query($connection, $query);
    if (!$results) {
        mysqli_close($connection);
        return null;
    }
    require_once('include/output.php');
    $events = [];
    foreach ($results as $row) {
        $events []= hsc($row);
    }
    mysqli_close($connection);
    return $events;
}

function create_course($event) {
    $connection = connect();
    $name = $event[0];
    $abbrevName = $event[1];
    $staffId= $event[2];
    $eventId = $event[3];
    $periodId = $event[4];
    $date = $event[5];
    $startTime = $event[6];
    $endTime = $event[7];
    $description = $event[8];
    $location = $event[9];
    $capacity = $event[10];
    $query = "
        insert into dbCourses (name, abbrevName, staffId, eventId, periodId, date, startTime, endTime, description, location, capacity)
        values ('$name', '$abbrevName', '$staffId', '$eventId', '$periodId', '$date', '$startTime', '$endTime', '$description', '$location', '$capacity')";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return null;
    }
    $id = mysqli_insert_id($connection);
    mysqli_commit($connection);
    mysqli_close($connection);
    return $id;
}

//changed this function since previous did not work
function update_course($courseId, $eventDetails) {
    $connection = connect();
        $query = "UPDATE dbCourses 
              SET name=?, 
                  abbrevName=?, 
                  staffId=?, 
                  eventId=?, 
                  periodId=?, 
                  date=?, 
                  startTime=?, 
                  endTime=?, 
                  description=?, 
                  location=?, 
                  capacity=?
              WHERE id=?";
        $statement = mysqli_prepare($connection, $query);
    if (!$statement) {
        echo "Error: " . mysqli_error($connection);
        return false;
    }
        mysqli_stmt_bind_param($statement, "sssssssssssi", 
        $eventDetails[1],     // name
        $eventDetails[2],     // abbrevName
        $eventDetails[3],     // staffId
        $eventDetails[4],     // eventId
        $eventDetails[5],     // periodId
        $eventDetails[6],     // date
        $eventDetails[7],     // startTime
        $eventDetails[8],     // endTime
        $eventDetails[9],     // description
        $eventDetails[10],    // location
        $eventDetails[11],    // capacity
        $courseId             // id
    );
        $result = mysqli_stmt_execute($statement);
    
    if (!$result) {
        echo "Error: " . mysqli_error($connection);
        mysqli_stmt_close($statement);
        mysqli_close($connection);
        return false;
    }
        mysqli_stmt_close($statement);
    mysqli_close($connection);
    
    return true;
}

function find_course($nameLike) {
    $connection = connect();
    $query = "
        select * from dbEvents
        where name like '%$nameLike%'
    ";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return null;
    }
    $all = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $all;
}

function fetch_courses_in_date_range_as_array($start_date, $end_date) {
    $connection = connect();
    $start_date = mysqli_real_escape_string($connection, $start_date);
    $end_date = mysqli_real_escape_string($connection, $end_date);
    $query = "select * from dbEvents
              where date >= '$start_date' and date <= '$end_date'
              order by date, startTime asc";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    $events = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $events;
}

function delete_courses_by_eventId($id) {
    $query = "delete from dbCourses where eventId='$id'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $result = boolval($result);
    mysqli_close($connection);
    return $result;
}

function find_first_course_month($eventId) {
    $connection = connect();
    $query = "
        select * from dbCourses
        where eventId='$eventId'
        order by date ASC limit 1
    ";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return null;
    }
    $course = mysqli_fetch_array($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    $date=substr($course['date'], 0,7);
    return $date;
}

// function to fetch all courses
function fetch_all_courses() {
    $connection = connect();
    $query = "SELECT * FROM dbCourses";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $courses;
}

//remove an event from dbCourses table.  If already there, return false
function remove_course_from_courses($course_name) {
    $con = connect();
    $query = 'SELECT * FROM dbCourses WHERE name = "' . $course_name . '"';
    $result = mysqli_query($con, $query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $query = 'DELETE FROM dbCourses WHERE name = "' . $course_name . '"';
    $result = mysqli_query($con, $query);
    mysqli_close($con);
    return true;
}

?>
