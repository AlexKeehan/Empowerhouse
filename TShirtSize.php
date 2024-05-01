<?php
/** 
 * Page is for the volunteer to have the abiility to change there t-shirt size, and for admin to be able to see the t-shirts sizes.
 * @ Author Diana Guzman
 * @ Lastest Version April 28 2024
 **/

include_once('database/dbPersons.php');
include_once('domain/Person.php');

// Check if user is logged in and has appropriate access level
session_cache_expire(30);
session_start();
date_default_timezone_set("America/New_York");


$loggedIn = false;
$access_level = 0;//1;
$userID = null;

if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];

    //$accessLevel = 1;

    //Update t-shirt size if necessary
    if($_SERVER["REQUEST_METHOD"] == "POST") {

        update_shirt_size($userID, $_POST['select_shirt']);
    }

}
?>
<!DOCTYPE html>
<html>

<head>
    <?php require_once('universal.inc') ?>
    <title>Empowerhouse VMS | Reports</title>
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
    <h1>Volunteer Shirt Size</h1>

    <main>
    <?php
        //This is for the volunteers 
        if($accessLevel == 1) {
    ?>
            <h2><center>This is your current information!<center></h2>
    <?php
            $con = connect();
            $type1 = "volunteer";

            //Select first_name, last_name, shirt_size WHERE id='user_id"
            $query = "SELECT * FROM dbPersons WHERE id='$userID' ORDER BY dbPersons.first_name, dbPersons.last_name";
            $result = mysqli_query($con,$query);

            echo'
                <table><center>
                    <tr>
                        <th>Volunteer Name</th>
                        <th>Shirt Size</th>
                    </tr>
                <tbody>    
            ';

            //Make row for each person
            while($row = mysqli_fetch_assoc($result)) {
                $name = $row['first_name'] . $row['last_name'];
                $size = $row['shirt_size'];
                //Echo a table row with a data cell for name (first and last) and a data cell for shirt size
                echo
                    "<tr>
                        <td>" . $name . "</td>
                        <td>" . $size . "</td>
                    </tr>
                    "; 
                
            }
            echo "</tbody></center></table>";

        ?>
            <br>
            <!--Change header name-->
            <h2><center>Would you like to change your shirt size?</center></h2>
            <br>
            <form id="select_shirt" action="./TShirtSize.php" method="POST">
                <div>
                    <label for="select_shirt">Select Size</label>
                    <select name="select_shirt"><!-- id="select_shirt">-->
                        <option value = "S">Small</option>
                        <option value = "M">Medium</option>
                        <option value = "L">Large</option>
                        <option value = "XL">Extra Large</option>
                        <option value = "XXL">2X Large</option>
                    </select>
                </div>


                <!--button to submit tshirt size change-->
                <input type="submit" value="Change Size"/>

            <!--closing form tag-->
            </form>
        <?php
        }else if($accessLevel != 1){
            $con = connect();

            //Select first_name, last_name, shirt_size WHERE id all active 
            $query = "SELECT * FROM dbPersons WHERE status='Active' ORDER BY dbPersons.first_name, dbPersons.last_name";
            $result = mysqli_query($con,$query);

            echo'
                <table><center>
                    <tr>
                        <th>Volunteer Name</th>
                        <th>Shirt Size</th>
                    </tr>
                <tbody>    
            ';

            //Create a CSV file for the tshirt sizes
            $columns = array("Name", "Shirt Size");
            $delimiter = ",";
            $filename = "Documents/tShirtSize.csv";
            $f = fopen($filename,"w");

            fputcsv($f, $columns, $delimiter);
            

            //Make row for each person
            while($row = mysqli_fetch_assoc($result)) {
                $name = $row['first_name'] . " " . $row['last_name'];
                $size = $row['shirt_size'];

                //Add table row to CSV file
                $csv_row = array($name, $size);
                fputcsv($f, $csv_row, $delimiter);

                //Echo a table row with a data cell for name (first and last) and a data cell for shirt size
                echo
                    "<tr>
                        <td>" . $name . "</td>
                        <td>" . $size . "</td>
                    </tr>
                    ";   
            }
            echo "</tbody></center></table>"; 
            
            //Close CSV file
            fclose($f);
            ?> 
            <br>
            <br>
            <h3><center> Would you like to download a CSV file of shirt sizes?</center></h3>
            <br>
            <form id="shirt_csv" action="./TShirtSize.php" method="POST">
                <a href="Documents/tShirtSize.csv" target="_self" class="button" download>
                    Download as CSV
                <target="_self">
                </a>
                <!--<input type="submit" name="shirt_csv" value="Download CSV of all Sizes"/>-->

            </form>
        <?php
        }
        ?>    
    </main>
</body>
</html>