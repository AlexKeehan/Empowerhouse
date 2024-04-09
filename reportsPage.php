<?php 
/**

* @version April 6, 2023
* @authors Alip Yalikun, Alex Keehan & Diana Guzman
*/


session_cache_expire(30);
session_start();
ini_set("display_errors",1);
error_reporting(E_ALL);
$loggedIn = false;
$accessLevel = 0;
$userID = null;
if (isset($_SESSION['_id'])) 
{
    $loggedIn = true;
    // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}

require_once('include/input-validation.php');
require_once('database/dbPersons.php');
require_once('database/dbEvents.php');
require_once('include/output.php');
  
$get = sanitize($_GET);
$indivID = @$get['indivID'];
$role = @$get['role'];
$indivStatus = @$get['status'];
$type = $get['report_type'];
$dateFrom = $get['date_from'];
$dateTo = $get['date_to'];
$lastFrom = strtoupper($get['lname_start']);
$lastTo = strtoupper($get['lname_end']);
@$stats = $get['statusFilter'];
$today =  date('Y-m-d');
  
if($dateFrom != NULL && $dateTo == NULL) 
{
    $dateTo = $today;
}
if($dateFrom == NULL && $dateTo != NULL) 
{
    $dateFrom = date('Y-m-d', strtotime(' - 1 year'));
}
if($lastFrom != NULL && $lastTo == NULL)
{
    $lastTo = 'Z';
}
if($lastFrom == NULL && $lastTo != NULL)
{
    $lastFrom = 'A';
} 

// Is user authorized to view this page?
if ($accessLevel < 2) 
{
    header('Location: index.php');
    die();
}

//Describe the goal of this function
//What is 86400? Define this as a variable
function getBetweenDates($startDate, $endDate)
{
    $rangArray = [];
          
    $startDate = strtotime($startDate);
    $endDate = strtotime($endDate);
           
    for ($currentDate = $startDate; $currentDate <= $endDate; $currentDate += (86400)) 
    {
      $date = date('Y-m-d', $currentDate);
      $rangArray[] = $date;
    }
    return $rangArray;
}


?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Empowerhouse VMS | Report Result</title>
        <style>
            table 
            {
                margin-top: 1rem;
                margin-left: auto;
                margin-right: auto;
                border-collapse: collapse;
                width: 80%;
            }
            td 
            {
                border: 1px solid #333333;
                text-align: left;
                padding: 8px;
            }
            th 
            {
                background-color: var(--main-color);
                color: var(--button-font-color);
                border: 1px solid #333333;
                text-align: left;
                padding: 8px;
		        font-weight: 500;
            }
          
            tr:nth-child(even) 
            {
                background-color: #f0f0f0;
                /* color:var(--button-font-color); */
		
            }

            @media print {
                tr:nth-child(even) 
                {
                    background-color: white;
                }

                button, header 
                {
                    display: none;
                }

                :root 
                {
                    font-size: 10pt;
                }

                label 
                {
                    color: black;
                }

                table 
                {
                    width: 100%;
                }

                a 
                {
                    color: black;
                }
            }

            .theB
            {
                width: auto;
                font-size: 15px;
            }
	        .center_a 
            {
                margin-top: 0;
		        margin-bottom: 3rem;
                margin-left:auto;
                margin-right:auto;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: .8rem;
            }
            .center_b 
            {
                margin-top: 3rem;
                display: flex;
                align-items: center;
                justify-content: center;
		        gap: .8rem;
            }
            #back-to-top-btn 
            {
                bottom: 20px;
            }
            .back-to-top:visited 
            {
                color: white; /* sets the color of the link when visited */  
            }
            .back-to-top 
            {
                color: white; /* sets the color of the link when visited */  
            }
	    .intro 
        {
                display: flex;
                flex-direction: column;
                gap: .5rem;
                padding: 0 0 0 0;
            }
	    @media only screen and (min-width: 1024px) 
        {
                .intro
                {
                    width: 80%;
                }
                main.report 
                {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
            }
        footer 
        {
            margin-bottom: 2rem;
        }
    </style>

    </head>
    <body>
  	<?php require_once('header.php') ?>
        <h1>Report Result</h1>
        <main class="report">
	    <div class="intro">
        <div>
            <label>Reports Type:</label>
            <span>
                <?php echo '&nbsp&nbsp&nbsp'; 
                if($type == "top_perform")
                {
                    echo "Top Performers"; 
                }elseif($type == "general_volunteer_report")
                {
                    echo "General Volunteer Report";
                }elseif($type == "total_vol_hours")
                {
                    echo "Total Volunteer Hours";
                }elseif($type == "indiv_vol_hours")
                {
                    echo "Individual Volunteer Hours";
                }elseif($type == "complete_training")
                {
                    echo "Volunteers Who Completed Training";
                }elseif($type == "volunteer_emails"){
                    echo "Volunteer Emails";
                }
                ?> 
            </span>
        </div>
        <div>

		<?php if ($type == "indiv_vol_hours" ): ?>
			<label>Name:</label>
		<?php echo '&nbsp&nbsp&nbsp';
			$con=connect();
             		$query = "SELECT dbPersons.first_name, dbPersons.last_name FROM dbPersons WHERE id= '$indivID' ";
             		$result = mysqli_query($con,$query);
			$theName = mysqli_fetch_assoc($result);	
			echo $theName['first_name'], " " , $theName['last_name'] ?>
		<?php else: ?>    
	    	<label>Last Name Range:</label>
            <span>
                <?php echo '&nbsp&nbsp&nbsp';
			      if($lastFrom == NULL && $lastTo == NULL): ?>
                        <?php echo "All last names"; ?>
                    	<?php else: ?>
                        <?php echo $lastFrom, " to " , $lastTo; ?>
			<?php endif ?>
                 <?php endif ?>
            </span>
	</div>


	<div>
        <label>Date Range:</label>
        <span>
            <?php echo '&nbsp&nbsp&nbsp';
            // if date from is provided but not date to, assume admin wants all dates from given date to current
                if(isset($dateFrom) && !isset($dateTo))
                {
                    echo $dateFrom, " to Current";
            // if date from is not provided but date to is, assume admin wants all dates prior to the date given
                }
                elseif(!isset($dateFrom) && isset($dateTo))
                {
                    echo "Every date through ", $dateTo;
            // if date from and date to is not provided assume admin wants all dates
                }
                elseif($dateFrom == NULL && $dateTo ==NULL)
                {
                    echo "All dates";
                }
                else
                {
                    echo $dateFrom ," to ", $dateTo;
                }
            ?>
        </span>
    </div>

    <div>
        <label>Volunteer Status:</label>
        <span>
        <?php echo '&nbsp&nbsp&nbsp';
	    if ($type == 'indiv_vol_hours')
		    echo $indivStatus;
	    else                        
		    echo $stats;
        ?>
        </span>
    </div>
    <div>
        <?php if($type == "indiv_vol_hours"): ?>
        <label>Role:</label>
        <span>
            <?php echo '&nbsp&nbsp&nbsp';
	    $con=connect();
        $query = "SELECT dbPersons.type FROM dbPersons WHERE id= '$indivID' ";
        $result = mysqli_query($con,$query);
	    $theName = mysqli_fetch_assoc($result);
	    if ($role == 'volunteer')
		    $role = 'Volunteer';
	    elseif ($role == 'admin')
		    $role = 'Admin';
	    elseif ($role == 'superadmin')
		    $role = 'SuperAdmin';	
	    echo $role ?>
<?php endif?>
    </div>
    <div>   
        <?php
        if($type != "top_perform" && $type != "complete_training")
        {
            echo "<label>Total Volunteer Hours: </label>"; 
            echo '&nbsp&nbsp&nbsp';
		if ($type != 'indiv_vol_hours')
            echo get_tot_vol_hours($type,$stats,$dateFrom,$dateTo,$lastFrom,$lastTo);
		elseif ($type == 'indiv_vol_hours' && $dateTo == NULL && $dateFrom == NULL)
			echo get_hours_volunteered_by($indivID);
		elseif ($type == 'indiv_vol_hours' && $dateTo != NULL && $dateFrom != NULL)
            echo get_hours_volunteered_by_and_date($indivID,$dateFrom,$dateTo);
        }
        ?>
        <!--- <h3 style="font-weight: bold">Result: <h3> -->
	</div>
    </main>
	
	<div class="center_a">
                
                <a href="report.php">
               <!---   <button class = "theB">New Report</button> -->
                </a>
                <a href="index.php">
                <!---  <button class = "theB">Home Page</button> -->
                </a>
	</div>
        <div class="table-wrapper">
        <?php 
        
        // view General volunteer report with all date range and all name range
        if($type == "general_volunteer_report" && $dateFrom == NULL && $dateTo ==NULL && $lastFrom == NULL && $lastTo == NULL)
        {
            echo"
            <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email Address</th>
		        <th>Skills</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            if($stats!="All")
            {
                $query = "SELECT * FROM dbPersons WHERE type='$type1' AND status='$stats'
			ORDER BY dbPersons.last_name, dbPersons.first_name";
            }
            else
            {
                $query = "SELECT * FROM dbPersons WHERE type='$type1'
			ORDER BY dbPersons.last_name, dbPersons.first_name";
            }
            $result = mysqli_query($con,$query);
            $totHours = array();
            while($row = mysqli_fetch_assoc($result))
            {
                $phone = $row['phone1'];
                $mail = $row['email'];
                echo"<tr>
                <td>" . $row['first_name'] . "</td>
                <td>" . $row['last_name'] . "</td>
                <td><a href='tel:$phone'>" . formatPhoneNumber($row['phone1']) . "</a></td>
                <td><a href='mailto:$mail'>" . $row['email'] . "</a></td>
		        <td>" . $row['specialties'] . "</td>
                <td>" . get_hours_volunteered_by($row['id']) . "</td>
                </tr>"; 
                $hours = get_hours_volunteered_by($row['id']);   
                $totHours[] = $hours;
            }
            $sum = 0;
            foreach($totHours as $hrs)
            {
                $sum += $hrs;
            }
            echo"
                <tr>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td bgcolor='white'><label>Total Hours:</label></td>
                <td bgcolor='white'><label>". $sum ."</label></td>
                </tr>";
        }

        // date range and name range for general volunteer report 
        if($type == "general_volunteer_report" && !$dateFrom == NULL && !$dateTo ==NULL && !$lastFrom == NULL  && !$lastTo == NULL)
        {
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email Address</th>
		        <th>Skills</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
            if($stats != "All")
            {
                $query = "SELECT *, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
		            WHERE eventDate >= '$dateFrom' AND eventDate<='$dateTo' AND dbPersons.status='$stats' AND dbPersons.type='$type1'
		            GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY dbPersons.last_name, dbPersons.first_name";
            }
            else
            {
                $query = "SELECT *, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
		            WHERE eventDate >= '$dateFrom' AND eventDate <='$dateTo' AND type='$type1'
		            GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY dbPersons.last_name, dbPersons.first_name";
            }
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $nameRange = range($lastFrom,$lastTo);
                $bothRange = array_merge($dd,$nameRange);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                $totHours = array();
                while($row = mysqli_fetch_assoc($result))
                {
                    foreach ($bothRange as $both)
                    {
                        if(in_array($both,$dateRange) && in_array($row['last_name'][0],$nameRange))
                        {
                            $phone = $row['phone1'];
                            $mail = $row['email'];
                            echo"<tr>
                            <td>" . $row['first_name'] . "</td>
                            <td>" . $row['last_name'] . "</td>
                            <td><a href='tel:$phone'>" . formatPhoneNumber($row['phone1']) . "</a></td>
                            <td><a href='mailto:$mail'>" . $row['email'] . "</a></td>
			                <td>" . $row['specialties'] . "</td>
                            <td>" . $row['Dur'] . "</td>
                            </tr>";
                            $hours = $row['Dur'];   
                            $totHours[] = $hours;
                        }
                    }
                }
                $sum = 0;
                foreach($totHours as $hrs)
                {
                    $sum += $hrs;
                }
                echo"
                    <tr>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td bgcolor='white'><label>Total Hours:</label></td>
                    <td bgcolor='white'><label>". $sum ."</label></td>
                    </tr>";
            } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
        }

        //only name range for general volunteer report 
        if($type == "general_volunteer_report" && $dateFrom == NULL && $dateTo ==NULL && !$lastFrom == NULL  && !$lastTo == NULL)
        {
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email Address</th>
		        <th>Skills</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
            if($stats != "All")
            {
                $query = "SELECT * FROM dbPersons WHERE type='$type1' AND status='$stats'
			ORDER BY last_name, first_name";
            }
            else
            {
                $query = "SELECT * FROM dbPersons WHERE type='$type1' ORDER BY last_name, first_name";
            }
            $result = mysqli_query($con,$query);
            $nameRange = range($lastFrom,$lastTo);
            $totHours = array();
            while($row = mysqli_fetch_assoc($result))
            {
                foreach ($nameRange as $a)
                {
                    if($row['last_name'][0] == $a)
                    {
                        $phone = $row['phone1'];
                        $mail = $row['email'];
                        echo"<tr>
                        <td>" . $row['first_name'] . "</td>
                        <td>" . $row['last_name'] . "</td>
                        <td><a href='tel:$phone'>" . formatPhoneNumber($row['phone1']) . "</a></td>
                        <td><a href='mailto:$mail'>" . $row['email'] . "</a></td>
			            <td>" . $row['specialties'] . "</td>
                        <td>" . get_hours_volunteered_by($row['id']) . "</td>
                        </tr>";
                        $hours = get_hours_volunteered_by($row['id']);   
                        $totHours[] = $hours;
                    }
                } 
            }
            $sum = 0;
            foreach($totHours as $hrs)
            {
                $sum += $hrs;
            }
            echo"
                <tr>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td bgcolor='white'><label>Total Hours:</label></td>
                <td bgcolor='white'><label>". $sum ."</label></td>
                </tr>";
        }

        //only date range for general volunteer report 
        if($type == "general_volunteer_report" && !$dateFrom == NULL && !$dateTo ==NULL && $lastFrom == NULL  && $lastTo == NULL)
        {
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email Address</th>
		        <th>Skills</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
            if($stats != "All")
            {
                $query = "SELECT *, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id 
                WHERE eventDate >= '$dateFrom' AND eventDate<='$dateTo' AND dbPersons.status='$stats' AND dbPersons.type='$type1'
		            GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY dbPersons.last_name, dbPersons.first_name";
            }
            else
            {
                $query = "SELECT *, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate >= '$dateFrom' AND eventDate<='$dateTo' AND type='$type1'
                GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY dbPersons.last_name, dbPersons.first_name";
            }
            $result = mysqli_query($con,$query); 
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                $totHours = array();
                while($row = mysqli_fetch_assoc($result))
                {
                    foreach ($dd as $date)
                    {
                        if(in_array($date,$dateRange))
                        {
                            $phone = $row['phone1'];
                            $mail = $row['email'];
                            echo"<tr>
                            <td>" . $row['first_name'] . "</td>
                            <td>" . $row['last_name'] . "</td>
                            <td><a href='tel:$phone'>" . formatPhoneNumber($row['phone1']) . "</a></td>
                            <td><a href='mailto:$mail'>" . $row['email'] . "</a></td>
			                <td>" . $row['specialties'] . "</td>
                            <td>" . $row['Dur'] . "</td>
                            </tr>";
                            $hours = $row['Dur'];   
                            $totHours[] = $hours;
                        }
                    }
                }
                $sum = 0;
                foreach($totHours as $hrs)
                {
                    $sum += $hrs;
                }
                echo"
                    <tr>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td bgcolor='white'><label>Total Hours:</label></td>
                    <td bgcolor='white'><label>". $sum ."</label></td>
                    </tr>";
            } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
        }

        // User chose reports on volunteers who completed training
        if ($type == "complete_training") 
        {
            $con=connect();
            // View volunteers who have completed training with no date range & no name range
            if ($dateFrom == NULL && $dateTo ==NULL && $lastFrom == NULL && $lastTo == NULL)
            { 
                // Print out column "headers"
                echo"
                    <table>
                    <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Completed Training</th>
                    <th>Date Completed Training</th>
                    </tr>
                    <tbody>"; 
                $type1 = "volunteer";
                $today =  date('Y-m-d');
                // If status is NOT All
                if($stats != "All")
                {
                    $query = "SELECT dbpersons.id, dbpersons.first_name, dbpersons.last_name, dbpersons.email, 
                    dbpersons.completedTraining, dbpersons.dateCompletedTraining
                    FROM dbpersons
                    WHERE dbpersons.status='$stats' 
                    AND dbpersons.type='$type1' 
                    AND dbpersons.completedTraining='True'
                    AND dbpersons.dateCompletedTraining <= '$today'
		            GROUP BY dbpersons.first_name, dbpersons.last_name";
                }
                else
                {
                    $query = "SELECT dbpersons.id, dbpersons.first_name, dbpersons.last_name, dbpersons.email, 
                    dbpersons.completedTraining, dbpersons.dateCompletedTraining
                    FROM dbpersons 
                    WHERE dbpersons.type='$type1' 
                    AND dbpersons.completedTraining='True'
                    AND dbpersons.dateCompletedTraining <= '$today'
                    GROUP BY dbpersons.last_name";
                }
            }
            // View volunteers who have completed training with only date range
            elseif (!$dateFrom == NULL && !$dateTo == NULL && $lastFrom == NULL && $lastTo == NULL) 
            {
                // Print out column "headers"
                echo"
                    <table>
                    <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Completed Training</th>
                    <th>Date Completed Training</th>
                    </tr>
                    <tbody>"; 
                $type1 = "volunteer";
                $today =  date('Y-m-d');
                // If status is NOT All
                if($stats != "All") 
                {
                    $query = "SELECT dbpersons.first_name, dbpersons.last_name, dbpersons.email, 
                    dbpersons.completedTraining, dbpersons.dateCompletedTraining 
                    FROM dbpersons 
                    WHERE dbpersons.completedTraining='True' 
                    AND (dbpersons.dateCompletedTraining BETWEEN '$dateFrom' AND '$dateTo') 
                    AND dbpersons.type='$type1'
                    AND dbpersons.status='$stats'
                    AND dbpersons.dateCompletedTraining <= '$today'
                    ORDER BY dbpersons.last_name";                    
                } 
                else 
                {
                    $query = "SELECT dbpersons.first_name, dbpersons.last_name, dbpersons.email, 
                    dbpersons.completedTraining, dbpersons.dateCompletedTraining 
                    FROM dbpersons 
                    WHERE dbpersons.completedTraining='True' 
                    AND (dbpersons.dateCompletedTraining BETWEEN '$dateFrom' AND '$dateTo') 
                    AND dbpersons.type='$type1'
                    AND dbpersons.dateCompletedTraining <= '$today'
                    ORDER BY dbpersons.last_name";       
                }
            }
            // View volunteers who have completed training with only name range
            elseif ($dateFrom == NULL && $dateTo == NULL && !$lastFrom == NULL && !$lastTo == NULL) 
            {
                // Print out column "headers"
                echo"
                    <table>
                    <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Completed Training</th>
                    <th>Date Completed Training</th>
                    </tr>
                    <tbody>"; 
                $type1 = "volunteer";
                $today =  date('Y-m-d');
                // If status is NOT All
                if($stats != "All") 
                {
                    $query = "SELECT dbpersons.first_name, dbpersons.last_name, dbpersons.email, 
                    dbpersons.completedTraining, dbpersons.dateCompletedTraining 
                    FROM dbpersons 
                    WHERE dbpersons.completedTraining='True' 
                    AND LOWER(LEFT(dbpersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    AND dbpersons.type='$type1'
                    AND dbpersons.status='$stats'
                    AND dbpersons.dateCompletedTraining <= '$today'
                    GROUP BY dbpersons.dateCompletedTraining, dbpersons.last_name";                    
                } 
                else 
                {
                    $query = "SELECT dbpersons.first_name, dbpersons.last_name, dbpersons.email, 
                    dbpersons.completedTraining, dbpersons.dateCompletedTraining 
                    FROM dbpersons 
                    WHERE dbpersons.completedTraining='True' 
                    AND LOWER(LEFT(dbpersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    AND dbpersons.type='$type1'
                    AND dbpersons.dateCompletedTraining <= '$today'
                    GROUP BY dbpersons.dateCompletedTraining, dbpersons.last_name";       
                }
            }
            // View volunteers who have completed training with date range & name range
            elseif (!$dateFrom == NULL && !$dateTo == NULL && !$lastFrom == NULL && !$lastTo == NULL) 
            {
                // Print out column "headers"
                echo"
                    <table>
                    <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Completed Training</th>
                    <th>Date Completed Training</th>
                    </tr>
                    <tbody>"; 
                $type1 = "volunteer";
                // If status is NOT All
                if($stats != "All") 
                {
                    $query = "SELECT dbpersons.first_name, dbpersons.last_name, dbpersons.email, 
                    dbpersons.completedTraining, dbpersons.dateCompletedTraining 
                    FROM dbpersons 
                    WHERE dbpersons.completedTraining='True' 
                    AND LOWER(LEFT(dbpersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    AND (dbpersons.dateCompletedTraining BETWEEN '$dateFrom' AND '$dateTo') 
                    AND dbpersons.type='$type1'
                    AND dbpersons.status='$stats'
                    AND dbpersons.dateCompletedTraining <= '$today'
                    GROUP BY dbpersons.dateCompletedTraining, dbpersons.last_name";                    
                } 
                else 
                {
                    $query = "SELECT dbpersons.first_name, dbpersons.last_name, dbpersons.email, 
                    dbpersons.completedTraining, dbpersons.dateCompletedTraining 
                    FROM dbpersons 
                    WHERE dbpersons.completedTraining='True' 
                    AND LOWER(LEFT(dbpersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    AND (dbpersons.dateCompletedTraining BETWEEN '$dateFrom' AND '$dateTo') 
                    AND dbpersons.type='$type1'
                    AND dbpersons.dateCompletedTraining <= '$today'
                    GROUP BY dbpersons.dateCompletedTraining, dbpersons.last_name";       
                }
            }
            $result = mysqli_query($con,$query);
            while($row = mysqli_fetch_assoc($result))
            {
                echo"<tr>
                <td>" . $row['first_name'] . "</td>
                <td>" . $row['last_name'] . "</td>
                <td>" . $row['email'] . "</td>
                <td>" . $row['completedTraining'] . "</td>
                <td>" . $row['dateCompletedTraining'] . "</td>
                </tr>";
            }
        }

        // view Top performers report with all date range and all name range
        if($type == "top_perform" && $dateFrom == NULL && $dateTo ==NULL && $lastFrom == NULL && $lastTo == NULL)
        {
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Volunteer Hours</th>
                /tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $today = date("Y-m-d");
            if($stats != "All")
            {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate<='$today' AND dbPersons.status='$stats' AND dbPersons.type='$type1'
                GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY Dur DESC LIMIT 5";
            }
            else
            {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate<='$today' AND dbPersons.type='$type1'
                GROUP BY dbEventVolunteers.userID
                ORDER BY Dur DESC LIMIT 5";
            }
            $result = mysqli_query($con,$query);
            $totHours = array();
            while($row = mysqli_fetch_assoc($result))
            {
                echo"<tr>
                <td>" . $row['first_name'] . "</td>
                <td>" . $row['last_name'] . "</td>
                <td>" . $row['Dur'] . "</td>
                </tr>";
                $hours = get_hours_volunteered_by($row['id']);
                $totHours[] = $hours;
            }
            $sum = 0;
                foreach($totHours as $hrs)
                {
                    $sum += $hrs;
                }
            echo"
                <tr>
                <td style='border: none;' bgcolor='white'></td>
                <td bgcolor='white'><label>Total Hours:</label></td>
                <td bgcolor='white'><label>". $sum ."</label></td>
                </tr>";
        }

        // date range and name range for top performer report
        if($type == "top_perform" && !$dateFrom == NULL && !$dateTo ==NULL && !$lastFrom == NULL  && !$lastTo == NULL)
        {
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
            if($stats != "All")
            {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate<='$dateTo' AND dbPersons.status='$stats' AND dbPersons.type='$type1'
                GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY Dur DESC LIMIT 5";
            }
            else
            {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate<='$dateTo' AND dbPersons.type='$type1'
                GROUP BY dbEventVolunteers.userID
                ORDER BY Dur DESC LIMIT 5";
            }
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $nameRange = range($lastFrom,$lastTo);
                $bothRange = array_merge($dd,$nameRange);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                $totHours = array();
                while($row = mysqli_fetch_assoc($result))
                {
                    foreach ($bothRange as $both)
                    {
                        if(in_array($both,$dateRange) && in_array($row['last_name'][0],$nameRange))
                        {
                            echo"<tr>
                            <td>" . $row['first_name'] . "</td>
                            <td>" . $row['last_name'] . "</td>
                            <td>" . $row['Dur'] . "</td>
                            </tr>";
                            $hours = get_hours_volunteered_by($row['id']);
                            $totHours[] = $hours;
                        }
                    }
                }
                $sum = 0;
                foreach($totHours as $hrs)
                {
                    $sum += $hrs;
                }
                echo"
                    <tr>
                    <td style='border: none;' bgcolor='white'></td>
                    <td bgcolor='white'><label>Total Hours:</label></td>
                    <td bgcolor='white'><label>". $sum ."</label></td>
                    </tr>";
            } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
        }

        //only name range for top performer report 
        if($type == "top_perform" && $dateFrom == NULL && $dateTo ==NULL && !$lastFrom == NULL  && !$lastTo == NULL)
        {
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $today = date("Y-m-d");
            if($stats != "All")
            {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate<='$today' AND dbPersons.status='$stats' AND dbPersons.type='$type1'
                GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY Dur DESC LIMIT 5";
            }
            else
            {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate<='$today' AND dbPersons.type='$type1'
                GROUP BY dbEventVolunteers.userID
                ORDER BY Dur DESC LIMIT 5";
            }
            $result = mysqli_query($con,$query);
            $nameRange = range($lastFrom,$lastTo);
            $totHours = array();
            while($row = mysqli_fetch_assoc($result))
            {
                foreach ($nameRange as $a)
                {
                    if($row['last_name'][0] == $a)
                    {
                        echo"<tr>
                        <td>" . $row['first_name'] . "</td>
                        <td>" . $row['last_name'] . "</td>
                        <td>" . $row['Dur'] . "</td>
                        </tr>";
                        $hours = get_hours_volunteered_by($row['id']);
                        $totHours[] = $hours;
                    }
                } 
            }
            $sum = 0;
            foreach($totHours as $hrs)
            {
                $sum += $hrs;
            }
            echo"
                <tr>
                <td style='border: none;' bgcolor='white'></td>
                <td bgcolor='white'><label>Total Hours:</label></td>
                <td bgcolor='white'><label>". $sum ."</label></td>
                </tr>";
        }

        //only date range for top performer report
        if($type == "top_perform" && !$dateFrom == NULL && !$dateTo ==NULL && $lastFrom == NULL  && $lastTo == NULL)
        {
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
            if($stats != "All")
            {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate<='$dateTo' AND dbPersons.status='$stats' AND dbPersons.type='$type1'
                GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY Dur DESC LIMIT 5";
            }
            else
            {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate<='$dateTo' AND dbPersons.type='$type1'
                GROUP BY dbEventVolunteers.userID
                ORDER BY Dur DESC LIMIT 5";
            }
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                $totHours = array();
                while($row = mysqli_fetch_assoc($result))
                {
                    foreach ($dd as $date)
                    {
                        if(in_array($date,$dateRange))
                        {
                            echo"<tr>
                            <td>" . $row['first_name'] . "</td>
                            <td>" . $row['last_name'] . "</td>
                            <td>" . $row['Dur'] . "</td>
                            </tr>";
                            $hours = $row['Dur'];
                            $totHours[] = $hours;
                        }
                    }
                }
                $sum = 0;
                foreach($totHours as $hrs)
                {
                    $sum += $hrs;
                }
                echo"
                    <tr>
                    <td style='border: none;' bgcolor='white'></td>
                    <td bgcolor='white'><label>Total Hours:</label></td>
                    <td bgcolor='white'><label>". $sum ."</label></td>
                    </tr>";
            } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
        }

        // view indiv_vol_hours report with all date range and all name range
        if($type == "indiv_vol_hours" && $dateFrom == NULL && $dateTo ==NULL)
        {
            
	        echo"
                <table>
                <tr>
                <th>Event</th>
                <th>Event Location</th>
                <th>Event Date</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            if($stats != "All"){
                $query = "SELECT dbPersons.id, dbEvents.name, dbEvents.location, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE dbPersons.id ='$indivID' AND dbPersons.status='$stats' AND dbEvents.eventDate<= '$today'
                GROUP BY dbEvents.name
		            ORDER BY dbEvents.eventDate desc";
            }
          else
          {
                $query = "SELECT dbPersons.id,dbEvents.name, dbEvents.location,dbEvents.eventDate,dbEvents.startTime,dbEvents.endTime
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE dbPersons.id ='$indivID' AND dbEvents.eventDate <= '$today'
		            ORDER BY dbEvents.eventDate desc";
            }
            $theEventHrs = get_events_attended_by_desc($indivID);
	        //$result = mysqli_query($con,$query);
            //while($row = mysqli_fetch_assoc($result)) {
                //foreach ($theEventHrs as $event) {
		        //echo"<tr>
                //<td>" . $event['name'] . "</td>
                //<td>" . $event['location'] . "</td>
                //<td>" . $event['date'] . "</td>
                //<td>" . $event['duration'] . "</td>
                //</tr>";
		        //}
               //$hours = get_hours_volunteered_by($row['id']);
            //}
		    echo"
		    <tr>
            <td style='border: none;' bgcolor='white'></td>
            <td style='border: none;' bgcolor='white'></td>
		    <td bgcolor='white'><label>Total Hours:</label></td>
		    <td bgcolor='white'><label>". get_hours_volunteered_by($indivID) ."</label></td>
		    </tr>";
        }

        // date range for indiv_vol_hours report
        if($type == "indiv_vol_hours" && !$dateFrom == NULL && !$dateTo ==NULL)
        {
            echo"
                <table>
                <tr>
                <th>Event</th>
                <th>Event Location</th>
                <th>Event Date</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
           if($stats != "All")
           {
                $query = "SELECT dbPersons.id,dbEvents.name, dbEvents.location,dbEvents.eventDate,dbEvents.startTime,dbEvents.endTime,
                (dbEvents.endTime - dbEvents.startTime) AS DURATION
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE dbPersons.id ='$indivID' AND dbPersons.status='$stats' AND eventDate > '$dateFrom' AND eventDate < '$dateTo'
                ORDER BY dbEvents.eventDate desc";
            }
          else
          {
                $query = "SELECT dbPersons.id,dbEvents.name, dbEvents.location,dbEvents.eventDate,dbEvents.startTime,dbEvents.endTime,
                (dbEvents.endTime - dbEvents.startTime) AS DURATION
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE dbPersons.id ='$indivID' AND eventDate > '$dateFrom' AND eventDate < '$dateTo'
                ORDER BY dbEvents.eventDate desc";
            }    
            //$result = mysqli_query($con,$query);
            
            // Code that might throw an exception or error goes here
            //$dd = getBetweenDates($dateFrom, $dateTo);
            //$dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
            //while($row = mysqli_fetch_assoc($result)){
            $theEventHrs = get_events_attended_by_and_date($indivID,$dateFrom,$dateTo);
		    foreach ($theEventHrs as $event) {
                //if(in_array($date, $dateRange)){
                    echo"<tr>
                    <td>" . $event['name'] . "</td>
                    <td>" . $event['location'] . "</td>
                    <td>" . $event['date'] . "</td>
                    <td>" . $event['duration'] . "</td>
                    </tr>";
                //}
            }
                   
		    echo"
                <tr>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td bgcolor='white'><label>Total Hours:</label></td>
                <td bgcolor='white'><label>". get_hours_volunteered_by_and_date($indivID,$dateFrom,$dateTo) ."</label></td>
                </tr>";
        }

        //No filters on total_vol_hours report
        if($type == "total_vol_hours" && $dateFrom == NULL && $dateTo ==NULL && $lastFrom == NULL && $lastTo == NULL)
        {
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Event</th>
                <th>Event Location</th>
                <th>Event Date</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
           if($stats != "All")
           {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name,dbEvents.name, dbEvents.location,dbEvents.eventDate,dbEvents.startTime,dbEvents.endTime,
                (dbEvents.endTime - dbEvents.startTime) AS DURATION
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE dbPersons.status='$stats' AND dbPersons.type='$type1'
                GROUP BY dbPersons.first_name, dbPersons.last_name
		            ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
            }
          else
          {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name,dbEvents.name, dbEvents.location,dbEvents.eventDate,dbEvents.startTime,dbEvents.endTime,
                (dbEvents.endTime - dbEvents.startTime) AS DURATION
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE dbPersons.type='$type1'
		            GROUP BY dbPersons.first_name, dbPersons.last_name
                ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
            }    
            $result = mysqli_query($con,$query);
            
	        while($row = mysqli_fetch_assoc($result)){
            	echo"<tr>
            	<td>" . $row['first_name'] . "</td>
            	<td>" . $row['last_name'] . "</td>
            	<td>" . $row['name'] . "</td>
            	<td>" . $row['location'] . "</td>
            	<td>" . $row['date'] . "</td>
            	<td>" . get_hours_volunteered_by($row['id']) . "</td>
		        </tr>";
            }
            echo"
                <tr>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td bgcolor='white'><label>Total Hours:</label></td>
                <td bgcolor='white'><label>". get_tot_vol_hours($type,$stats,$dateFrom,$dateTo,$lastFrom,$lastTo) ."</label></td>
                </tr>";
        }

        //Date range filter on total_vol_hours report
        if($type == "total_vol_hours" && !$dateFrom == NULL && !$dateTo ==NULL && $lastFrom == NULL && $lastTo == NULL)
        {
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Event</th>
                <th>Event Location</th>
                <th>Event Date</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
            if($stats != "All")
            {
                $query = "SELECT *, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
		            WHERE eventDate >= '$dateFrom' AND eventDate<='$dateTo' AND dbPersons.status='$stats' AND dbPersons.type='$type1'
		            GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
            }
          else
          {
                $query = "SELECT *, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate >= '$dateFrom' AND eventDate<='$dateTo' AND dbPersons.type='$type1'
                GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
            }
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                while($row = mysqli_fetch_assoc($result))
                {
		            foreach ($dd as $date) 
                    {
                        if(in_array($date, $dateRange))
                        {
                            echo"<tr>
            			        <td>" . $row['first_name'] . "</td>
            			        <td>" . $row['last_name'] . "</td>
            			        <td>" . $row['name'] . "</td>
            			        <td>" . $row['location'] . "</td>
            			        <td>" . $row['date'] . "</td>
            			        <td>" . $row['Dur'] . "</td>
				                </tr>";
	    		        }  
		            }
             	}
            echo"
                <tr>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td bgcolor='white'><label>Total Hours:</label></td>
                <td bgcolor='white'><label>". get_tot_vol_hours($type,$stats,$dateFrom,$dateTo,$lastFrom,$lastTo) ."</label></td>
                </tr>";
            } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
	    }

        //All filters on total_vol_hours report
        if($type == "total_vol_hours" && !$dateFrom == NULL && !$dateTo ==NULL && !$lastFrom == NULL && !$lastTo == NULL)
        {
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Event</th>
                <th>Event Location</th>
                <th>Event Date</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
            if($stats != "All")
            {
                $query = "SELECT *, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate >= '$dateFrom' AND eventDate<='$dateTo' AND dbPersons.status='$stats' AND dbPersons.type='$type1'
                GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPerson.first_name";
            }
          else
          {
                $query = "SELECT *, SUM(HOUR(TIMEDIFF(dbEvents.endTime, dbEvents.startTime))) as Dur
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE eventDate >= '$dateFrom' AND eventDate<='$dateTo' AND dbPersons.type='$type1'
                GROUP BY dbPersons.first_name,dbPersons.last_name
                ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
            }
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $nameRange = range($lastFrom,$lastTo);
                $bothRange = array_merge($dd,$nameRange);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                while($row = mysqli_fetch_assoc($result)) 
                {
                    foreach ($bothRange as $both)
                    {
                        if(in_array($both,$dateRange) && in_array($row['last_name'][0],$nameRange))
                        {
                            echo"<tr>
            			        <td>" . $row['first_name'] . "</td>
            			        <td>" . $row['last_name'] . "</td>
            			        <td>" . $row['name'] . "</td>
            			        <td>" . $row['location'] . "</td>
            			        <td>" . $row['date'] . "</td>
            			        <td>" . $row['Dur'] . "</td>
				                </tr>";
	    		        }  
		            }
                }
            	echo"
                <tr>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td bgcolor='white'><label>Total Hours:</label></td>
                <td bgcolor='white'><label>". get_tot_vol_hours($type,$stats,$dateFrom,$dateTo,$lastFrom,$lastTo) ."</label></td>
                </tr>";
            } catch (TypeError $e) {
            // Code to handle the exception or error goes here
            echo "No Results found!"; 
            }
        }


        // Name range filter on total_vol_hours report
        if($type == "total_vol_hours" && $dateFrom == NULL && $dateTo == NULL && !$lastFrom == NULL && !$lastTo == NULL){
            echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Event</th>
                <th>Event Location</th>
                <th>Event Date</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
            $con=connect();
            $type1 = "volunteer";
           if($stats != "All")
           {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name,dbEvents.name, dbEvents.location,dbEvents.eventDate,dbEvents.startTime,dbEvents.endTime,
                (dbEvents.endTime - dbEvents.startTime) AS DURATION
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                WHERE dbPersons.status='$stats' AND dbPersons.type='$type1'
                GROUP BY dbPersons.first_name, dbPersons.last_name
		        ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
            }
          else
          {
                $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name,dbEvents.name, dbEvents.location,dbEvents.eventDate,dbEvents.startTime,dbEvents.endTime,
                (dbEvents.endTime - dbEvents.startTime) AS DURATION
                FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
		            WHERE dbPersons.type='$type1'
                GROUP BY dbPersons.first_name, dbPersons.last_name
                ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
            }    
            $result = mysqli_query($con,$query);
            $nameRange = range($lastFrom,$lastTo);
            while($row = mysqli_fetch_assoc($result)) 
            {
                foreach ($nameRange as $a)
                {
                    if($row['last_name'][0] == $a)
                    {
                        echo"<tr>
            		<td>" . $row['first_name'] . "</td>
            		<td>" . $row['last_name'] . "</td>
            		<td>" . $row['name'] . "</td>
            		<td>" . $row['location'] . "</td>
            		<td>" . $row['date'] . "</td>
            		<td>" . get_hours_volunteered_by($row['id']) . "</td>
			        </tr>";
	    	        }  
		        }
	        }
            echo"
                <tr>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td style='border: none;' bgcolor='white'></td>
                <td bgcolor='white'><label>Total Hours:</label></td>
                <td bgcolor='white'><label>". get_tot_vol_hours($type,$stats,$dateFrom,$dateTo,$lastFrom,$lastTo) ."</label></td>
                </tr>";
	    }
        //Display email list only - PSEUDOCODE
         if($type == "volunteer_emails"){
            $con=connect();
            echo"
            <table>
            <tr>
                <th>Volunteer Emails</th>
            </tr>
            <tbody>";

            $type1 = "Status";
            $type1 = "volunteer";
            if($stats!="All"){
                $query = "SELECT * FROM dbPersons WHERE type='$type1' AND status='$stats'
			ORDER BY dbPersons.last_name, dbPersons.first_name";
            }else{
                $query = "SELECT * FROM dbPersons WHERE type='$type1'
			ORDER BY dbPersons.last_name, dbPersons.first_name";
            }
            $result = mysqli_query($con,$query);
            $totHours = array();
            while($row = mysqli_fetch_assoc($result)){
                $phone = $row['phone1'];
                $mail = $row['email'];
                echo"<tr>
                <td><a href='mailto:$mail'>" . $row['email'] . "</a></td>
                </tr>"; 
                $hours = get_hours_volunteered_by($row['id']);   
                $totHours[] = $hours;
            }
            $sum = 0;
            foreach($totHours as $hrs){
                $sum += $hrs;
            }
            echo"
                <tr>
                <td style='border: none;' bgcolor='white'></td>
                </tr>";



         }
        /*
            //NOTE: var save the value of the Active/Inactive form in a variable (Control + F for "radio" in "report.php" to find it)

            //NOTE: if the value is All, we're getting all the emails / the entire email column
            query = select email from dbPersons

            //NOTE: else, filter by Active/Inactive value
            query = select email from dbPersons where status = '.var.'

            //NOTE: This code to create a table row for each email.
            $result = mysqli_query($con,$query);
            while($row = mysqli_fetch_assoc($result)) {
                echo
                    "<tr>
            		<td>" . $row['email'] . "</td>
			        </tr>";

	        }
        */






      ?> 


        </tbody>
        </table>
        </div>
        <div class="center_b">
	    <a href="report.php">
            <button class = "theB">New Report</button>
            </a>
	    <a href="index.php">
            <button class = "theB">Home Page</button>
        </a>
        </div>
        </main>
        <footer>
        <div class="center_b">
            <button class = "theB" id="back-to-top-btn"><a href="#" class="back-to-top">Back to top</a></button>
        </div>
        </footer>
    </body>
</html>