<?php
/*
 * Export report(s) to CSV for Empowerhouse project
 * @author Alex Keehan
 * @version April 23, 2024
 */

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
//External file used to export as .xlsx
//Can be found here: https://github.com/shuchkin/simplexlsxgen
require_once('SimpleXLSXGen.php');

if ($accessLevel < 2) 
{
	header('Location: index.php');
	die();
}

//Grab the type of report for the filename
$type = $_SESSION['type'];
//Grab the data from export_array
$export_array = $_SESSION['export_array'];
//Unset it for safety
unset($_SESSION['export_array']);

//Create filename with name of report
$fileName = $type .  ".xlsx";

//Exports Excel files using external library from Github
//Can be found here: https://github.com/shuchkin/simplexlsxgen
$xlsx = Shuchkin\SimpleXLSXGen::fromArray($export_array);
$xlsx->downloadAs($fileName);
?>
