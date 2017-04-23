<!DOCTYPE html>
<html>
<head>

<!--
Name: Erik Brown
Current Date: 2016
Language: HTML5
Libraries: Bootstrap JQuery
Includes: master.css 
-->

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>The Pallas Institute</title>
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
<link href="css/master.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div id="header"></div>

<article>

<?php
// bring in variables from dbinfo.php
include("dbinfo.php"); 

// this will connect to the mysql daemon 
// and prepare to use the brown database 
$DBConnect=mysqli_connect($dbhostname,$dbuser,$dbpass,$dbname[0]); 

// Check Connection
if (!$DBConnect) {
    printf("Failed to connect to MySQL: %s\n", mysqli_connect_error());
    exit();
}
else
{
	// Create variables and set their values to the corresponding data in the $_GET array
	$patient=$_GET['patientID'];
	$date=$_GET['date'];
	$time=$_GET['time'];
	$time.=':00:00';

	// Prepare statement
	$stmt = mysqli_prepare($DBConnect,"DELETE FROM `appointments` WHERE `date`=? AND `time`=? AND `patient_primary`=?;");

	// Bind parameters
	mysqli_stmt_bind_param($stmt,'sss',$date,$time,$patient);

	// Execute prepared statement
	mysqli_stmt_execute($stmt);

	// If a row has been affected give a confirmation
	if (mysqli_stmt_affected_rows($stmt)==1)
	{
		echo '<h2>Success</h2>';
		echo '<p>Appointment cancelled.</p>';
	}
	// Otherwise give an error
	else
	{
		echo '<h2>Error</h2>';
		echo '<p>No appointment found.</p>';
	}

	// Close statement
	mysqli_stmt_close($stmt);

	// Close database connection
	mysqli_close($DBConnect);
}
?>

</article>

<div id="footer"></div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">

// Load the header and footer
$(function(){
  $("#header").load("header.html"); 
  $("#footer").load("footer.html"); 
});
</script>

</body>
</html>