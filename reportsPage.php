<?php 
/**
* @version April 30, 2024
* @authors Alip Yalikun, Alex Keehan, Diana Guzman, & Tubba Noor

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
$export_array = array();
$totHours = array();
  
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
                }
                elseif($type == "general_volunteer_report")
                {
                    echo "General Volunteer Report";
                }
                elseif($type == "total_vol_hours")
                {
                    echo "Total Volunteer Hours";
                }
                elseif($type == "indiv_vol_hours")
                {
                    echo "Individual Volunteer Hours";
                }
                elseif($type == "completed_training")
                {
                    echo "Volunteers Who Completed Training";
                }
                elseif($type == "email_volunteer_list")
                {
                    echo "Volunteer Emails";
                }
                elseif($type == "missing_paperwork")
                {
                    echo "Volunteers Missing Paperwork";
                }
                ?> 
            </span>
        </div>

        <div>
		<?php if ($type == "indiv_vol_hours" ): ?>
			<label>Name:</label>
		<?php echo '&nbsp&nbsp&nbsp';
			$con=connect();
            $query = "SELECT dbPersons.first_name, dbPersons.last_name FROM dbPersons WHERE dbPersons.id='$indivID' ";
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
	    echo $role ?>
        <?php endif?>
    </div>
    <div>   
        <?php
        if($type == "general_volunteer_report" || $type == "total_vol_hours" || $type == "indiv_vol_hours")
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

        // Report for general volunteer information
        if ($type == "general_volunteer_report")
        {
            $sum = 0;
            $totHours = array();
            $con=connect();

            // view General volunteer report with all date range and all name range
            if ($dateFrom == NULL && $dateTo == NULL && $lastFrom == NULL && $lastTo == NULL)
            {
                if($stats!="All")
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, dbPersons.phone1, dbPersons.email
                    FROM dbPersons 
                    WHERE type='volunteer' AND status='$stats'
			        ORDER BY dbPersons.last_name, dbPersons.first_name";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, dbPersons.phone1, dbPersons.email
                    FROM dbPersons 
                    WHERE type='volunteer'
			        ORDER BY dbPersons.last_name, dbPersons.first_name";
                }
            }
            // both date and name range for general volunteer report 
            elseif (!$dateFrom == NULL && !$dateTo == NULL && !$lastFrom == NULL  && !$lastTo == NULL)
            {
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, dbPersons.phone1, dbPersons.email
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
		            WHERE eventDate >= '$dateFrom' AND eventDate <= '$dateTo' 
                    AND dbPersons.status='$stats' AND dbPersons.type='volunteer'
		            GROUP BY dbPersons.first_name, dbPersons.last_name
                    ORDER BY dbPersons.last_name, dbPersons.first_name";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, dbPersons.phone1, dbPersons.email
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
		            WHERE eventDate >= '$dateFrom' AND eventDate <='$dateTo' AND type='volunteer'
		            GROUP BY dbPersons.first_name,dbPersons.last_name
                    ORDER BY dbPersons.last_name, dbPersons.first_name";
                }
            }
            // only name range for general volunteer report 
            elseif ($dateFrom == NULL && $dateTo == NULL && !$lastFrom == NULL  && !$lastTo == NULL)
            {
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, dbPersons.phone1, dbPersons.email
                    FROM dbPersons 
                    WHERE type='volunteer' AND status='$stats'
			        ORDER BY last_name, first_name";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, dbPersons.phone1, dbPersons.email
                    FROM dbPersons WHERE type='volunteer' 
                    ORDER BY last_name, first_name";
                }
            }
            // only date range for general volunteer report 
            elseif (!$dateFrom == NULL && !$dateTo == NULL && $lastFrom == NULL  && $lastTo == NULL)
            {
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, dbPersons.phone1, dbPersons.email
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id 
                    WHERE eventDate >= '$dateFrom' AND eventDate<='$dateTo' 
                    AND dbPersons.status='$stats' AND dbPersons.type='volunteer'
		            GROUP BY dbPersons.first_name,dbPersons.last_name
                    ORDER BY dbPersons.last_name, dbPersons.first_name";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, dbPersons.phone1, dbPersons.email
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate >= '$dateFrom' AND eventDate<='$dateTo' AND type='volunteer'
                    GROUP BY dbPersons.first_name,dbPersons.last_name
                    ORDER BY dbPersons.last_name, dbPersons.first_name";
                }
            }

            $result = mysqli_query($con,$query); 

            //Check if the query results is empty
            if (mysqli_num_rows($result) == 0)
            {
                echo '<div class="error-toast">No Results Found</div>';
            } 
            //Otherwise, print out our headers and rows
            else
            {
                echo"
                    <table>
                    <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Phone Number</th>
                    <th>Email Address</th>
                    <th>Volunteer Hours</th>
                    </tr>
                    <tbody>";
                //Output query results
                while($row = mysqli_fetch_assoc($result))
                {
                    $hours = get_hours_volunteered_by($row['id']);  
                    $phone = $row['phone1'];
                    $mail = $row['email'];
                    echo"<tr>
                    <td>" . $row['first_name'] . "</td>
                    <td>" . $row['last_name'] . "</td>
                    <td><a href='tel:$phone'>" . formatPhoneNumber($row['phone1']) . "</a></td>
                    <td><a href='mailto:$mail'>" . $row['email'] . "</a></td>
                    <td>" . $hours . "</td>
                    </tr>";  
                    $totHours[] = $hours;
                    //Stores the data to be exported
                    $export_array[] = [$row['first_name'], $row['last_name'], $row['phone1'], $row['email'], $hours];
                }
                //Total up all the hours and output it
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
        }

        // Report on volunteers who completed training
        if ($type == "completed_training") 
        {
            $con=connect();

            // View volunteers who have completed training with no date range & no name range
            if ($dateFrom == NULL && $dateTo ==NULL && $lastFrom == NULL && $lastTo == NULL)
            { 
                // If status is NOT All
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, dbPersons.email, 
                    dbPersons.completedTraining, dbPersons.dateCompletedTraining
                    FROM dbPersons
                    WHERE dbPersons.status='$stats' 
                    AND dbPersons.type='volunteer' 
                    AND dbPersons.completedTraining='True'
		            GROUP BY dbPersons.first_name, dbPersons.last_name";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, dbPersons.email, 
                    dbPersons.completedTraining, dbPersons.dateCompletedTraining
                    FROM dbPersons 
                    WHERE dbPersons.type='volunteer' 
                    AND dbPersons.completedTraining='True'
                    GROUP BY dbPersons.last_name";
                }
            }
            // View volunteers who have completed training with only date range
            elseif (!$dateFrom == NULL && !$dateTo == NULL && $lastFrom == NULL && $lastTo == NULL) 
            {
                // If status is NOT All
                if($stats != "All") 
                {
                    $query = "SELECT dbPersons.first_name, dbPersons.last_name, dbPersons.email, 
                    dbPersons.completedTraining, dbPersons.dateCompletedTraining 
                    FROM dbPersons 
                    WHERE dbPersons.completedTraining='True' 
                    AND (dbPersons.dateCompletedTraining BETWEEN '$dateFrom' AND '$dateTo') 
                    AND dbPersons.type='volunteer'
                    AND dbPersons.status='$stats'
                    ORDER BY dbPersons.last_name";                    
                } 
                else 
                {
                    $query = "SELECT dbPersons.first_name, dbPersons.last_name, dbPersons.email, 
                    dbPersons.completedTraining, dbPersons.dateCompletedTraining 
                    FROM dbPersons 
                    WHERE dbPersons.completedTraining='True' 
                    AND (dbPersons.dateCompletedTraining BETWEEN '$dateFrom' AND '$dateTo') 
                    AND dbPersons.type='volunteer'
                    ORDER BY dbPersons.last_name";       
                }
            }
            // View volunteers who have completed training with only name range
            elseif ($dateFrom == NULL && $dateTo == NULL && !$lastFrom == NULL && !$lastTo == NULL) 
            {
                // If status is NOT All
                if($stats != "All") 
                {
                    $query = "SELECT dbPersons.first_name, dbPersons.last_name, dbPersons.email, 
                    dbPersons.completedTraining, dbPersons.dateCompletedTraining 
                    FROM dbPersons 
                    WHERE dbPersons.completedTraining='True' 
                    AND LOWER(LEFT(dbPersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    AND dbPersons.type='volunteer'
                    AND dbPersons.status='$stats'
                    GROUP BY dbPersons.dateCompletedTraining, dbPersons.last_name";                    
                } 
                else 
                {
                    $query = "SELECT dbPersons.first_name, dbPersons.last_name, dbPersons.email, 
                    dbPersons.completedTraining, dbPersons.dateCompletedTraining 
                    FROM dbPersons 
                    WHERE dbPersons.completedTraining='True' 
                    AND LOWER(LEFT(dbPersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    AND dbPersons.type='volunteer'
                    GROUP BY dbPersons.dateCompletedTraining, dbPersons.last_name";       
                }
            }
            // View volunteers who have completed training with date range & name range
            elseif (!$dateFrom == NULL && !$dateTo == NULL && !$lastFrom == NULL && !$lastTo == NULL) 
            {
                // If status is NOT All
                if($stats != "All") 
                {
                    $query = "SELECT dbPersons.first_name, dbPersons.last_name, dbPersons.email, 
                    dbPersons.completedTraining, dbPersons.dateCompletedTraining 
                    FROM dbPersons 
                    WHERE dbPersons.completedTraining='True' 
                    AND LOWER(LEFT(dbPersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    AND (dbPersons.dateCompletedTraining BETWEEN '$dateFrom' AND '$dateTo') 
                    AND dbPersons.type='volunteer'
                    AND dbPersons.status='$stats'
                    GROUP BY dbPersons.dateCompletedTraining, dbPersons.last_name";                    
                } 
                else 
                {
                    $query = "SELECT dbPersons.first_name, dbPersons.last_name, dbPersons.email, 
                    dbPersons.completedTraining, dbPersons.dateCompletedTraining 
                    FROM dbPersons 
                    WHERE dbPersons.completedTraining='True' 
                    AND LOWER(LEFT(dbPersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    AND (dbPersons.dateCompletedTraining BETWEEN '$dateFrom' AND '$dateTo') 
                    AND dbPersons.type='volunteer'
                    GROUP BY dbPersons.dateCompletedTraining, dbPersons.last_name";       
                }
            }

            $result = mysqli_query($con,$query);
            //Check if query results are empty
            if (mysqli_num_rows($result) == 0)
            {
                echo '<div class="error-toast">No Results Found</div>';
            }
            else
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
                //Output query results
                while($row = mysqli_fetch_assoc($result))
                {
                    echo"<tr>
                    <td>" . $row['first_name'] . "</td>
                    <td>" . $row['last_name'] . "</td>
                    <td>" . $row['email'] . "</td>
                    <td>" . $row['completedTraining'] . "</td>
                    <td>" . $row['dateCompletedTraining'] . "</td>
                    </tr>";
                    //Stores the data to be exported
                    $export_array[] = [$row['first_name'], $row['last_name'], $row['email'], $row['completedTraining'], $row['dateCompletedTraining']];
                }
            }
        }

        // Report on top performers
        if ($type == "top_perform")
        {
            $con=connect();
            $sum = 0;

            // view Top performers report with all date range and all name range
            if ($dateFrom == NULL && $dateTo == NULL && $lastFrom == NULL && $lastTo == NULL)
            {
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate <= '$today' AND dbPersons.status='$stats' AND dbPersons.type='volunteer'
                    GROUP BY dbPersons.first_name,dbPersons.last_name";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate <= '$today' AND dbPersons.type='volunteer'
                    GROUP BY dbEventVolunteers.userID";
                }
            }
            // date range and name range for top performer report
            elseif (!$dateFrom == NULL && !$dateTo == NULL && !$lastFrom == NULL  && !$lastTo == NULL)
            {
                if($stats != "All")
                {
                    //Add dur to organize results based on event duration
                    $query = "SELECT dbPersons.id, dbPersons.first_name,  dbPersons.last_name,
                    DATEDIFF(minute, dbEvents.startTime, dbEvents.endtime) as dur
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate <= '$dateTo' AND dbPersons.status='$stats' AND dbPersons.type='volunteer'
                    GROUP BY dur";
                }
                else
                {
                    //Add dur to organize results based on event duration
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name,
                    DATEDIFF(minute, dbEvents.startTime, dbEvents.endtime) as dur
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate<='$dateTo' AND dbPersons.type='volunteer'
                    GROUP BY dur";
                }
            }
            //only name range for top performer report 
            elseif ($dateFrom == NULL && $dateTo == NULL && !$lastFrom == NULL  && !$lastTo == NULL)
            {
                if($stats != "All")
                {
                    //Add dur to organize results based on event duration
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name,
                    DATEDIFF(minute, dbEvents.startTime, dbEvents.endtime) as dur
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate <= '$today' AND dbPersons.status='$stats' AND dbPersons.type='volunteer'
                    GROUP BY dur";
                }
                else
                {
                    //Add dur to organize results based on event duration
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name,
                    DATEDIFF(minute, dbEvents.startTime, dbEvents.endtime) as dur
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate <= '$today' AND dbPersons.type='volunteer'
                    GROUP BY dur";
                }
            }
            //only date range for top performer report
            elseif (!$dateFrom == NULL && !$dateTo == NULL && $lastFrom == NULL  && $lastTo == NULL)
            {
                if($stats != "All")
                {
                    //Add dur to organize results based on event duration
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, 
                    DATEDIFF(minute, dbEvents.startTime, dbEvents.endtime) as dur
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate <= '$dateTo' AND dbPersons.status='$stats' AND dbPersons.type='volunteer'
                    GROUP BY dur";
                }
                else
                {
                    //Add dur to organize results based on event duration
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_namez
                    DATEDIFF(minute, dbEvents.startTime, dbEvents.endtime) as dur
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate <= '$dateTo' AND dbPersons.type='volunteer'
                    GROUP BY dur";
                }
            }
            $result = mysqli_query($con,$query); 
            //Check if query results are empty
            if (mysqli_num_rows($result) == 0)
            {
                echo '<div class="error-toast">No Results Found</div>';
            } 
            else
            {
                //Print headers and rows
                echo"
                    <table>
                    <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Volunteer Hours</th>
                    </tr>
                    <tbody>";
                // Output query results
                while($row = mysqli_fetch_assoc($result))
                {
                    $hours = get_hours_volunteered_by($row['id']);
                    echo"<tr>
                    <td>" . $row['first_name'] . "</td>
                    <td>" . $row['last_name'] . "</td>
                    <td>" . $hours . "</td>
                    </tr>";
                    $totHours[] = $hours;
                }
                //Sum up & print total hours
                foreach($totHours as $hrs)
                {
                    $sum += $hrs;
                }

                echo"
                    <tr>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td bgcolor='white'><label>Total Hours:</label></td>
                    <td bgcolor='white'><label>". $sum ."</label></td>
                    </tr>";
            }
        }

        // Report on individual volunteer hours
        if ($type == "indiv_vol_hours")
        {
            $con=connect();

            // view indiv_vol_hours report with all date range and all name range
            if ($dateFrom == NULL && $dateTo == NULL && $lastFrom == NULL && $lastTo == NULL)
            {
                $theEventHrs = get_events_attended_by_desc($indivID);
                $totalHrs = get_hours_volunteered_by($indivID);
                if ($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbEventVolunteers.userID='$indivID'
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.id='$indivID' AND dbPersons.status='$stats'
		            ORDER BY dbEvents.eventDate desc";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbEventVolunteers.userID='$indivID'
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.id='$indivID'
		            ORDER BY dbEvents.eventDate desc";
                }
            }
            // date range and name range for indiv_vol_hours report
            elseif (!$dateFrom == NULL && !$dateTo == NULL && !$lastFrom == NULL  && !$lastTo == NULL)
            {
                $theEventHrs = get_events_attended_by_and_date($indivID, $dateFrom, $dateTo);
                $totalHrs = get_hours_volunteered_by_and_date($indivID, $dateFrom, $dateTo);
                if ($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.id ='$indivID' AND dbPersons.status='$stats' 
                    AND LOWER(LEFT(dbPersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    AND (dbEvents.eventDate BETWEEN '$dateFrom' AND '$dateTo')
                    GROUP BY dbEvents.eventName
		            ORDER BY dbEvents.eventDate desc";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.id ='$indivID'
                    AND LOWER(LEFT(dbPersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    AND (dbEvents.eventDate BETWEEN '$dateFrom' AND '$dateTo')
                    GROUP BY dbEvents.eventName
		            ORDER BY dbEvents.eventDate desc";
                }
            }
            // only date range for indiv_vol_hours report
            elseif (!$dateFrom == NULL && !$dateTo == NULL && $lastFrom == NULL && $lastTo == NULL)
            {
                $theEventHrs = get_events_attended_by_and_date($indivID, $dateFrom, $dateTo);
                $totalHrs = get_hours_volunteered_by_and_date($indivID, $dateFrom, $dateTo);
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.id ='$indivID' AND dbPersons.status='$stats' AND eventDate BETWEEN '$dateFrom' AND '$dateTo'
                    ORDER BY dbEvents.eventDate desc";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.id ='$indivID' AND eventDate BETWEEN '$dateFrom' AND '$dateTo'
                    ORDER BY dbEvents.eventDate desc";
                }    
            }
            // only name range for indiv_vol_hours report
            elseif ($dateFrom == NULL && $dateTo == NULL && !$lastFrom == NULL && !$lastTo == NULL)
            {
                $theEventHrs = get_events_attended_by_desc($indivID);
                $totalHrs = get_hours_volunteered_by($indivID);
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.id ='$indivID' AND dbPersons.status='$stats' 
                    AND LOWER(LEFT(dbPersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    ORDER BY dbEvents.eventDate desc";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.id ='$indivID'
                    AND LOWER(LEFT(dbPersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    ORDER BY dbEvents.eventDate desc";
                }    
            }
            // Output query results
            $result = mysqli_query($con,$query);
            
            //Check if query results are empty
            if (mysqli_num_rows($result) == 0)
            {
                echo '<div class="error-toast">No Results Found</div>';
            }
            else
            {
                //Print headers and rows
                echo"
                <table>
                <tr>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";

                foreach ($theEventHrs as $event) 
                {
                    $hours = calculateHourDuration($event['startTime'], $event['endTime']);
		            echo"<tr>
                        <td>" . $event['eventName'] . "</td>
                        <td>" . $event['eventDate'] . "</td>
                        <td>" . $hours . "</td>
                        </tr>";
		        }

                echo"
                    <tr>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td bgcolor='white'><label>Total Hours:</label></td>
                    <td bgcolor='white'><label>". $totalHrs ."</label></td>
                    </tr>";
            }
        }

        // report for total volunteer hours
        if ($type == "total_vol_hours")
        {
            $sum = 0;
            $con=connect();
            
            // All fields for total_vol_hours report
            if ($dateFrom == NULL && $dateTo == NULL && $lastFrom == NULL && $lastTo == NULL)
            {
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, 
                    dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.status='$stats' AND dbPersons.type='volunteer'
                    GROUP BY dbPersons.first_name, dbPersons.last_name
		            ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, 
                    dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.type='volunteer'
                    GROUP BY dbPersons.first_name, dbPersons.last_name
		            ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
                }    
            }
            //Both name & date fields for total_vol_hours report
            elseif (!$dateFrom == NULL && !$dateTo == NULL && !$lastFrom == NULL && !$lastTo == NULL)
            {
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, 
                    dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime 
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate >= '$dateFrom' AND eventDate <= '$dateTo' AND dbPersons.status='$stats' AND dbPersons.type='volunteer'
                    GROUP BY dbPersons.first_name,dbPersons.last_name
                    ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPerson.first_name";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, 
                    dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate >= '$dateFrom' AND eventDate <= '$dateTo' AND dbPersons.type='volunteer'
                    GROUP BY dbPersons.first_name,dbPersons.last_name
                    ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
                }
            }
            // Name range field for total_vol_hours report
            elseif ($dateFrom == NULL && $dateTo == NULL && !$lastFrom == NULL && !$lastTo == NULL)
            {
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, 
                    dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE dbPersons.status='$stats' AND dbPersons.type='volunteer'
                    GROUP BY dbPersons.first_name, dbPersons.last_name
	                ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, 
                    dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
	                WHERE dbPersons.type='volunteer'
                    GROUP BY dbPersons.first_name, dbPersons.last_name
                    ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
                }
	        }
            // Only date range field on total_vol_hours report
            elseif (!$dateFrom == NULL && !$dateTo == NULL && $lastFrom == NULL && $lastTo == NULL)
            {
                if($stats != "All")
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, 
                    dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
		            WHERE eventDate BETWEEN '$dateFrom' AND '$dateTo' AND dbPersons.status='$stats' 
                    AND dbPersons.type='volunteer'
		            GROUP BY dbPersons.first_name, dbPersons.last_name
                    ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
                }
                else
                {
                    $query = "SELECT dbPersons.id, dbPersons.first_name, dbPersons.last_name, 
                    dbEvents.eventName, dbEvents.eventDate, dbEvents.startTime, dbEvents.endTime
                    FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
                    JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
                    WHERE eventDate BETWEEN '$dateFrom' AND '$dateTo' AND dbPersons.type='volunteer'
                    GROUP BY dbPersons.first_name, dbPersons.last_name
                    ORDER BY dbEvents.eventDate DESC, dbPersons.last_name, dbPersons.first_name";
                }
	        }
            $result = mysqli_query($con,$query);
            
            //Check if query results are empty
            if (mysqli_num_rows($result) == 0)
            {
                echo '<div class="error-toast">No Results Found</div>';
            } 
            else
            {
                //Print headers and rows
                echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Event</th>
                <th>Event Date</th>
                <th>Volunteer Hours</th>
                </tr>
                <tbody>";
                //Ouput query results
                while ($row = mysqli_fetch_assoc($result))
                {
                    $hours = get_hours_volunteered_by($row['id']);
            	    echo"<tr>
            	        <td>" . $row['first_name'] . "</td>
            	        <td>" . $row['last_name'] . "</td>
            	        <td>" . $row['eventName'] . "</td>
            	        <td>" . $row['eventDate'] . "</td>
            	        <td>" . $hours . "</td>
		                </tr>";
                    $export_array = [[$row['first_name'], $row['last_name'], $row['eventName'], $row['eventDate'], $hours]];
                }
            
                //Total volunteer hours
                echo"
                    <tr>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td style='border: none;' bgcolor='white'></td>
                    <td bgcolor='white'><label>Total Hours:</label></td>
                    <td bgcolor='white'><label>". get_tot_vol_hours($type, $stats, $dateFrom, $dateTo, $lastFrom, $lastTo) ."</label></td>
                    </tr>";   
            }
        }

        //Display email list only
        if ($type == "volunteer_emails")
        {
            $con=connect();

            if ($stats!="All")
            {
                $query = "SELECT * FROM dbPersons WHERE type='volunteer' AND status='$stats' 
                ORDER BY dbPersons.last_name, dbPersons.first_name";
            }
            else
            {
                $query = "SELECT * FROM dbPersons WHERE type='volunteer'
                ORDER BY dbPersons.last_name, dbPersons.first_name";
            }

            $result = mysqli_query($con,$query);

            //Check if query results are empty
            if (mysqli_num_rows($result) == 0)
            {
                echo '<div class="error-toast">No Results Found</div>';
            } 
            else
            {
                //Print headers and rows
                echo"
                <table>
                <tr>
                    <th>Volunteer Emails</th>
                </tr>
                <tbody>";
                while ($row = mysqli_fetch_assoc($result))
                {
                    $mail = $row['email'];
                    echo"<tr>
                    <td><a href='mailto:$mail'>" . $row['email'] . "</a></td>
                    </tr>"; 
                    $hours = get_hours_volunteered_by($row['id']);   
                    $totHours[] = $hours;
                    $export_array = [[$row['email']]];
                }
                echo"
                    <tr>
                    <td style='border: none;' bgcolor='white'></td>
                    </tr>";
            }
        }

        //Diplay volunteers who are missing paperwork
        if ($type == "missing_paperwork") {
            //Date range isn't needed for this kind of report
            //Only name range is needed
            //So only need two if statements to catch those occurences

            $con=connect();

            //No fields
            //User chose to not filter by name range
            if ($dateFrom == NULL && $dateTo == NULL && $lastFrom == NULL && $lastTo == NULL)
            {
                // Generic SQL query to retrieve volunteers with missing paperwork
                if ($stats != "All") 
                {
                    //renamed column to completedPaperwork to fit with completedTraining column to track training completion
                    $query = "SELECT * FROM dbPersons WHERE type='volunteer' 
                    AND status='$stats' AND dbPersons.completedPaperwork is NULL 
                    ORDER BY dbPersons.last_name, dbPersons.first_name";
                } 
                else 
                {
                    $query = "SELECT * FROM dbPersons WHERE type='volunteer' 
                    AND dbPersons.completedPaperwork is NULL
                    ORDER BY dbPersons.last_name, dbPersons.first_name";
                }
            }
            //only name field
            //User chose to filter by only name field
            elseif ($dateFrom == NULL && $dateTo == NULL && !$lastFrom == NULL && !$lastTo == NULL)
            {
                // Specific SQL query to retrieve volunteers with missing paperwork with name range
                if ($stats != "All") 
                {
                    $query = "SELECT * FROM dbPersons WHERE type='volunteer' 
                    AND status='$stats' AND dbPersons.completedPaperwork is NULL
                    AND LOWER(LEFT(dbPersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    ORDER BY dbPersons.last_name, dbPersons.first_name";
                } 
                else 
                {
                    $query = "SELECT * FROM dbPersons WHERE type='volunteer' 
                    AND dbPersons.completedPaperwork is NULL
                    AND LOWER(LEFT(dbPersons.last_name, 1)) between '$lastFrom' AND '$lastTo'
                    ORDER BY dbPersons.last_name, dbPersons.first_name";
                }
            }

            $result = mysqli_query($con,$query);

            //Check if the query returned empty
            if (mysqli_num_rows($result) == 0)
            {
                echo "No Results Found";
            }
            //Otherwise print our headers and rows
            else
            {
                //Print headers
                echo"
                <table>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                </tr>
                <tbody>";
                //Ouput query results
                while ($row = mysqli_fetch_assoc($result))
                {
            	    echo"<tr>
            	        <td>" . $row['first_name'] . "</td>
            	        <td>" . $row['last_name'] . "</td>
            	        <td>" . $row['email'] . "</td>
		                </tr>";
                    //export_array is filled with the data to be exported
                    $export_array[] = [$row['first_name'], $row['last_name'], $row['email']];
                }
            }
            echo "</tbody></table>";
        }

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
    <?php
    //Only export for certain report types
    if ($type == "general_volunteer_report" || 
    $type == "email_volunteer_list" ||
    $type == "completed_training" || 
    $type == "total_vol_hours" ||
    $type == "missing_paperwork")
    {
        //Put the type variable into SESSION to pass to reportsExport.php
        $_SESSION['type'] = $type;
        //Put the export array into SESSION to pass to reportsExport.php
        $_SESSION['export_array'] = $export_array;
        //Show the Export Report button
        //Redirect to reportsExport.php
        echo"<a href='reportsExport.php'>
        <button class = 'theB'>Export Report</button>
        </a>";
    }
    ?>
    </div>
    </main>
    <footer>
    <div class="center_b">
        <button class = "theB" id="back-to-top-btn"><a href="#" class="back-to-top">Back to top</a></button>
    </div>
    </footer>
    </body>
</html>