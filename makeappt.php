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
session_start();

// Define the patient ID using the stored session data
$patientID=$_SESSION['patientID'];

// Create variables and set their values to the corresponding data in the $_POST array
$date=$_POST['date'];
$time=$_POST['time'];

// bring in variables from dbinfo.php
include("dbinfo.php"); 

// this will connect to the mysql daemon 
// and prepare to use the brown database 
$DBConnect=mysqli_connect($dbhostname,$dbuser,$dbpass,$dbname[0]); 

// Define a query to insert new appointment
$SQLString="INSERT INTO `appointments` (`date`, `time`, `patient_primary`) VALUES ('".$date."','0".$time.":00:00','".$patientID."');";

// If the patient ID (indicating user is logged in), date and time (indicating schedule.php submitted data) is set
if (isset($_SESSION['patientID'])&&isset($_POST['date'])&&isset($_POST['time']))
{
	// Run query and store the result in $QueryResult
	$QueryResult = mysqli_query($DBConnect,$SQLString);

	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	else
	{
		// If the appointment was inserted without error
		if ($QueryResult===TRUE)
		{
			// Define a query get name using patientID
			$SQLString="SELECT `name` FROM `accounts` where `PRIMARY`=".$patientID.";";
			
			// Run query and store the result in $QueryResult
			$QueryResult = mysqli_query($DBConnect,$SQLString);

			if (mysqli_connect_errno())
			{
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			else
			{
				$name=mysqli_fetch_row($QueryResult);
			}
			
			echo '<h2>Success</h2>';
			echo '<p>Appointment made!</p>';
			echo '<p>Name: '.$name[0].'</p>';
			$message='Success<br>Appointment made!<br>Name: '.$name[0].'<br>';
			
			// Explode $date so that we can change the format before printing it
			$dateExploded=explode('-',$date);
			
			echo '<p>Date: '.$dateExploded[1].'-'.$dateExploded[2].'-'.$dateExploded[0].'</p>';
			$message.='Date: '.$dateExploded[1].'-'.$dateExploded[2].'-'.$dateExploded[0].'<br>';
			
			// If the time is over 12 print out the time as pm minus 12 hours
			if ($time>12)
			{
				echo '<p>Time: '.($time-12).':00pm</p>';
				$message .='Time: '.($time-12).':00pm<br>';
			}
			else
			{
				// If the time is 12 print out the time as pm
				if ($time==12)
				{
					echo '<p>Time: 12:00pm</p>';
					$message.='Time: 12:00pm<br>';
				}
				// Otherwise print out the time as am
				else
				{
					echo '<p>Time: '.$time.':00am</p>';
					$message.='Time: '.$time.':00am<br>';
				}
			}
			
			// Create variables for confirmation email
			$message.='If you need to cancel your appointment please <a href="https://cts.gruv.org/brown/final/cancel.php?patientID='.$patientID.'&date='.$date.'&time='.$time.'">click here.</a>';
			
			// Check Connection
			if (!$DBConnect) {
				printf("Failed to connect to MySQL: %s\n", mysqli_connect_error());
				exit();
			}
			else
			{
				// Prepare statement
				$stmt = mysqli_prepare($DBConnect,"SELECT `email` FROM `accounts` where `PRIMARY`=?;");

				// Bind parameters to prevent injection attacks
				mysqli_stmt_bind_param($stmt,'s',$patientID);

				// Execute prepared statement
				mysqli_stmt_execute($stmt);
				
				/* bind variables to prepared statement */
				mysqli_stmt_bind_result($stmt,$email);
				
				/* fetch values */
				mysqli_stmt_fetch($stmt);
				
				// Assign result of query to variable
				$to=sprintf('%s',$email);
			}
			
			$subject='Appointment Confirmation';
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
			$headers .= 'From: <noreply@cts.gruv.org>'."\r\n";
			$headers .= 'BCC: <erik.brownj@gmail.com>'."\r\n";
			$headers .= 'Reply-To: <noreply@cts.gruv.org>';
			$status=mail($to,$subject,$message,$headers);
			
			if ($status)
			{
				echo '<p>A confirmation email was sent!</p>';
			}
			else
			{
				echo '<h2>Failure</h2>';
				echo '<p>There was a problem sending your confirmation email.</p>';
			}			
		}
		else
		{
			echo '<h2>Failure</h2>';
			echo '<p>Error! Your appointment could not be made.  Please call 555-1234 or email the webmaster at <a href="mailto:erik.brownj@gmail.com">erik.brownj@gmail.com</a></p>';
		}
	}
}
else
{
	echo '<h2>Failure</h2>';
	echo 'No data is set!';
}

// Close database connection
mysqli_close($DBConnect);
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