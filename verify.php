<?php
// Connects to the database server as well as sets up info for header.php
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
    
    include_once('database/dbPersons.php');
    include_once('domain/Person.php');
    // Get date?
    if (isset($_SESSION['_id'])) {
        $person = retrieve_person($_SESSION['_id']);
    }
    $notRoot = $person->get_id() != 'vmsroot';
// Connect to database server
$servername = "localhost";
$username = "homebasedb";
$password = "homebasedb";
$dbname = "homebasedb";

if ($_SERVER['SERVER_NAME'] == 'jenniferp119.sg-host.com') {
    $username = 'uwpcgsjb3tzec';
    $dbname = 'dbyrqpvdjpzamq';
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn ->connect_error);
}

// Display all accounts that need to be verified
$sql = "SELECT * FROM dbPersons WHERE type = 'verify'";
$result = mysqli_query($conn, $sql);

//while($row = mysqli_fetch_assoc($result)) {
//    echo "ID: " . $row['id'] . " First_Name: " . $row['first_name'] . " Last_Name: " . $row['last_name'] . " Type: " . $row['type'] . " Address: " . $row['address'] . " City: " . $row['city'] . " State: " . $row['state'] . " Zip: " . $row['zip'] . " Phone1: " . $row['phone1'] . " Phone 1 Type: " . $row['phone1type'] . " Birthday: " . $row['birthday'] . " Email: " . $row['email'] . " Gender: " . $row['gender'];
//}
//$sql = "SELECT * FROM dbpersons WHERE type = 'verifyAdmin'";
//$result = mysqli_query($conn, $sql);

//while($row = mysqli_fetch_assoc($result)) {
//    echo "ID: " . $row['id'] . " First_Name: " . $row['first_name'] . " Last_Name: " . $row['last_name'] . " Type: " . $row['type'] . " Address: " . $row['address'] . " City: " . $row['city'] . " State: " . $row['state'] . " Zip: " . $row['zip'] . " Phone1: " . $row['phone1'] . " Phone 1 Type: " . $row['phone1type'] . " Birthday: " . $row['birthday'] . " Email: " . $row['email'] . " Gender: " . $row['gender'];
//}
//$sql = "SELECT * FROM dbpersons WHERE type = 'verifyTrainer'";
//$result = mysqli_query($conn, $sql);

//while($row = mysqli_fetch_assoc($result)) {
//    echo "ID: " . $row['id'] . " First_Name: " . $row['first_name'] . " Last_Name: " . $row['last_name'] . " Type: " . $row['type'] . " Address: " . $row['address'] . " City: " . $row['city'] . " State: " . $row['state'] . " Zip: " . $row['zip'] . " Phone1: " . $row['phone1'] . " Phone 1 Type: " . $row['phone1type'] . " Birthday: " . $row['birthday'] . " Email: " . $row['email'] . " Gender: " . $row['gender'];
//}
$checkbox1 = array();
?>
<!DOCTYPE html>
<html>
    <head>
	<?php require_once('universal.inc') ?>
        <title>Empower House VMS | Verify</title>
    </head>
    <body>
    	<form method="post">
    	    <?php require_once('header.php') ?>
    	    <label style="margin-left: 450px; font-size: 50px; font-weight: 600"> User Verification Form</label> 
    	    <br></br>
	        <label for="volunteer" style="margin-left: 20px"> Volunteer Users</label>
    	    <table id="volunteer" class="volunteer" style="border: 1px solid; width: 97%; margin-left: 20px; margin-bottom: 20px">
	        	<tr class="verifyVolunteer" style="border: 1px solid; background-color: #002A5E;">
    	    		<td style="border: 1px solid; color: greenyellow;"> Verify </td>
    	    		<td style="border: 1px solid; color: red;"> Delete </td>
        			<td style="border: 1px solid; color: white"> User ID / Email </td>
        			<td style="border: 1px solid; color: white"> First Name </td>
        			<td style="border: 1px solid; color: white"> Last Name </td>
    	    		<td style="border: 1px solid; color: white"> Type </td>
        			<td style="border: 1px solid; color: white"> Address </td>
        			<td style="border: 1px solid; color: white"> City </td>
        			<td style="border: 1px solid; color: white"> State </td>
	        		<td style="border: 1px solid; color: white"> Zip </td>
    	    		<td style="border: 1px solid; color: white"> Phone1 </td>
        			<td style="border: 1px solid; color: white"> Phone1 Type </td>
        			<td style="border: 1px solid; color: white"> Birthday </td>
    	    		<td style="border: 1px solid; color: white"> Gender </td>
        		</tr>
	        	<tr style="border: 1px solid">
    	    		<?php
        				while($row = mysqli_fetch_assoc($result)) {
        					$id = $row['id'];
        			?>
	        		<td style="background-color: gray;border: 1px solid; color: white; font-weight: 500;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="chk1[ ]" name="chk1[ ]" value="<?php echo $id; ?>" onclick="validate()"></td>
	        		<td style="background-color: gray;border: 1px solid; color: white; font-weight: 500;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="chk2[ ]" name="chk2[ ]" value="<?php echo $id; ?>" onclick="validate2()"></td>
	        		<script>
	        			function validate() {
	        				if (document.getElementById("chk1[ ]").checked) {
	        					document.getElementById("chk2[ ]").checked=false;
	        				}
	        				else {
	        					document.getElementById("chk2[ ]").disabled=false;
	        				}
	        			}
	        			function validate2() {
	        				if (document.getElementById("chk2[ ]").checked) {
	        					document.getElementById("chk1[ ]").checked=false;
	        				}
	        				else {
	        					document.getElementById("chk1[ ]").disabled=false;
	        				}
	        			}
	        		</script>
    	    		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['id'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['first_name'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['last_name'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['type'] ?></td>
	        		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['address'] ?></td>
    	    		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['city'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['state'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['zip'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['phone1'] ?></td>
	        		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['phone1type'] ?></td>
    	    		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['birthday'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['gender'] ?></td>
	        	</tr>
    	    	<?php
        			}
        			$sql = "SELECT * FROM dbPersons WHERE type = 'verifyAdmin'";
					$result = mysqli_query($conn, $sql);

        		?>
	        </table>
    	    <label for="admin" style="margin-left: 20px"> Admin Users</label>
        	<table id="admin" class="admin" style="border: 1px solid; width: 97%; margin-left: 20px; margin-bottom: 20px;">
    	    	<tr class="verifyAdmin" style="border: 1px solid; background-color: black; background-color: #002A5E;">
        			<td style="border: 1px solid; color: greenyellow;"> Verify </td>
        			<td style="border: 1px solid; color: red;"> Delete </td>
        			<td style="border: 1px solid; color: white"> User ID / Email</td>
        			<td style="border: 1px solid; color: white"> First Name </td>
	        		<td style="border: 1px solid; color: white"> Last Name </td>
    	    		<td style="border: 1px solid; color: white"> Type </td>
        			<td style="border: 1px solid; color: white"> Address </td>
        			<td style="border: 1px solid; color: white"> City </td>
        			<td style="border: 1px solid; color: white"> State </td>
    	    		<td style="border: 1px solid; color: white"> Zip </td>
        			<td style="border: 1px solid; color: white"> Phone1 </td>
        			<td style="border: 1px solid; color: white"> Phone1 Type </td>
        			<td style="border: 1px solid; color: white"> Birthday </td>
    	    		<td style="border: 1px solid; color: white"> Gender </td>
        		</tr>
        		<tr style="border: 1px solid">
        			<?php
        				while($row = mysqli_fetch_assoc($result)) {
        					$id = $row['id']
        			?>
	        		<td style="background-color: gray;border: 1px solid; color: white; font-weight: 500;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="chk3[ ]" name="chk1[ ]" value="<?php echo $id; ?>" onclick="validate3()"></td>
	        		<td style="background-color: gray;border: 1px solid; color: white; font-weight: 500;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="chk4[ ]" name="chk2[ ]" value="<?php echo $id; ?>" onclick="validate4()"></td>
	        		<script>
	        			function validate3() {
	        				if (document.getElementById("chk3[ ]").checked) {
	        					document.getElementById("chk4[ ]").checked=false;
	        				}
	        				else {
	        					document.getElementById("chk4[ ]").disabled=false;
	        				}
	        			}
	        			function validate4() {
	        				if (document.getElementById("chk4[ ]").checked) {
	        					document.getElementById("chk3[ ]").checked=false;
	        				}
	        				else {
	        					document.getElementById("chk3[ ]").disabled=false;
	        				}
	        			}
	        		</script>
	        		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['id'] ?></td>
    	    		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['first_name'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['last_name'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['type'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['address'] ?></td>
	        		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['city'] ?></td>
    	    		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['state'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['zip'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['phone1'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['phone1type'] ?></td>
    	    		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['birthday'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['gender'] ?></td>
	        	</tr>
    	    	<?php
        			}
        			$sql = "SELECT * FROM dbPersons WHERE type = 'verifyTrainer'";
					$result = mysqli_query($conn, $sql);

        		?>
	        </table>
    	    <label for="trainer" style="margin-left: 20px"> Trainer Users</label>
        	<table id="trainer" class="trainer" style="border: 1px solid; width: 97%; margin-left: 20px; margin-bottom: 20px;">
        		<tr class="verifyTrainer" style="border: 1px solid; background-color: black; background-color: #002A5E;">
	        		<td style="border: 1px solid; color: greenyellow;"> Verify </td>
	        		<td style="border: 1px solid; color: red;"> Delete </td>
    	    		<td style="border: 1px solid; color: white"> User ID / Email</td>
        			<td style="border: 1px solid; color: white"> First Name </td>
        			<td style="border: 1px solid; color: white"> Last Name </td>
	        		<td style="border: 1px solid; color: white"> Type </td>
    	    		<td style="border: 1px solid; color: white"> Address </td>
        			<td style="border: 1px solid; color: white"> City </td>
        			<td style="border: 1px solid; color: white"> State </td>
        			<td style="border: 1px solid; color: white"> Zip </td>
	        		<td style="border: 1px solid; color: white"> Phone1 </td>
    	    		<td style="border: 1px solid; color: white"> Phone1 Type </td>
        			<td style="border: 1px solid; color: white"> Birthday </td>
        			<td style="border: 1px solid; color: white"> Gender </td>
	        	</tr>
    	    	<tr style="border: 1px solid">
        			<?php
        				while($row = mysqli_fetch_assoc($result)) {
        					$id = $row['id'];
        			?>
	        		<td style="background-color: gray;border: 1px solid; color: white; font-weight: 500;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="chk5[ ]" name="chk1[ ]" value="<?php echo $id; ?>" onclick="validate5()"></td>
	        		<td style="background-color: gray;border: 1px solid; color: white; font-weight: 500;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="chk6[ ]" name="chk2[ ]" value="<?php echo $id; ?>" onclick="validate6()"></td>
	        		<script>
	        			function validate5() {
	        				if (document.getElementById("chk5[ ]").checked) {
	        					document.getElementById("chk6[ ]").checked=false;
	        				}
	        				else {
	        					document.getElementById("chk6[ ]").disabled=false;
	        				}
	        			}
	        			function validate6() {
	        				if (document.getElementById("chk6[ ]").checked) {
	        					document.getElementById("chk5[ ]").checked=false;
	        				}
	        				else {
	        					document.getElementById("chk5[ ]").disabled=false;
	        				}
	        			}
	        		</script>
    	    		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['id'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['first_name'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['last_name'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['type'] ?></td>
	        		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['address'] ?></td>
    	    		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['city'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['state'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['zip'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['phone1'] ?></td>
	        		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['phone1type'] ?></td>
    	    		<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['birthday'] ?></td>
        			<td style="border: 1px solid; color: black; font-weight: 500;"><?php echo $row['gender'] ?></td>
        		</tr>
        		<?php
        			}
        		?>
        	</table>
	    	<input type="submit" name="login" value="Verify / Delete Users">
		</form>
		<?php
	    // Update the account to a volunteer once the higher ranking account verifies them
	    if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$checkbox1 = (array)$_POST['chk1'];
			$checkbox2 = (array)$_POST['chk2'];
			for($i=0; $i<sizeof ($checkbox1); $i++) {
				$sql = "SELECT type from dbPersons where id = '$checkbox1[$i]'";
				$result = mysqli_query($conn, $sql);
				while($row = mysqli_fetch_assoc($result)){
					if ($row['type'] == "verify"){
						$sql  = "UPDATE dbPersons SET type = 'volunteer' WHERE id = '$checkbox1[$i]'";
						if($conn->query($sql) === TRUE) {
		    				echo "Record updated successfully";
						}
					}
					elseif ($row['type'] == "verifyAdmin"){
						$sql = "UPDATE dbPersons SET type = 'admin' WHERE id = '$id'";
						if($conn->query($sql) === TRUE) {
		    				echo "Record updated successfully";
						}
					}
					elseif ($row['type'] == "verifyTrainer"){
						$sql = "UPDATE dbPersons SET type = 'trainer' WHERE id = '$id'";
						if($conn->query($sql) === TRUE) {
			    			echo "Record updated successfully";
						}
					}
				}
			}
			for($i=0; $i<sizeof($checkbox2); $i++){
				$sql = "DELETE FROM dbPersons WHERE id = '$checkbox2[$i]'";
				if($conn->query($sql) == TRUE) {
					echo "Deleted successfully";
				}
			}
	    $conn->close();
	    }
	?>
    </body>
</html>