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

// Create variables and set their values to the corresponding data in the $_POST array
$email=$_POST['email'];
$password=$_POST['password'];

// Create a variable to hold the acount status
$status=NULL;

// bring in variables from dbinfo.php
include("dbinfo.php"); 

// this will connect to the mysql daemon 
// and prepare to use the brown database 

$DBConnect=mysqli_connect($dbhostname,$dbuser,$dbpass,$dbname[0]); 

// Define a query to retrieve the status associated with the given email address
$SQLString="SELECT `status` FROM `accounts` WHERE `email`='$email';";

// Run query and store the result in $QueryResult
$QueryResult = mysqli_query($DBConnect,$SQLString);

if (mysqli_connect_errno())
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{
	// Convert the retrieved row from an object array to an int
	$status=implode(mysqli_fetch_assoc($QueryResult));
}

// If the status is active
if ($status==1)
{
	// Define a query to retrieve the salt associated with the given email address
	$SQLString="SELECT `salt` FROM `accounts` WHERE `email`='$email';";

	// Run query and store the result in $QueryResult
	$QueryResult = mysqli_query($DBConnect,$SQLString);

	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	else
	{
		// Convert the retrieved row from an object array to a string
		$salt=implode(mysqli_fetch_assoc($QueryResult));
		
		// Concatenate password onto the end of the salt and assign the value to the $saltedPassword variable
		$saltedPassword=$salt.$password;

		// Hash the password
		$hashedPassword=hash('sha256',($saltedPassword));
		
		// Define a query to grab our hashed password to test against
		$SQLString="SELECT `password` FROM `accounts` WHERE `email`='$email';";
		
		// Run query and store the result in $QueryResult
		$QueryResult = mysqli_query($DBConnect,$SQLString);
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		else
		{
			// Convert the retrieved row from an object array to a string
			$storedPassword=implode(mysqli_fetch_assoc($QueryResult));
			
			// Check to see if the password matches
			if (!strcmp($storedPassword,$hashedPassword))
			{
				
				
				// Define a query to grab the user's PRIMARY key to use as the PatientID
				$SQLString="SELECT `PRIMARY` FROM `accounts` WHERE `email`='$email';";
				
				// Run query and store the result in $QueryResult
				$QueryResult = mysqli_query($DBConnect,$SQLString);
				
				if (mysqli_connect_errno())
				{
					echo "Failed to connect to MySQL: " . mysqli_connect_error();
				}
				else
				{
					session_start();
					
					// Convert the retrieved row from an object array to a string
					$_SESSION['patientID']=implode(mysqli_fetch_assoc($QueryResult));
				}
				echo '<h2>Success</h2>';
				echo '<p>You are signed in! You can now <a href="schedule.php">schedule</a> your appointment.</p>';
			}
			else
			{
				echo '<h2>Error</h2>';
				echo '<p>Incorrect password, please <a href="schedule.php">try again</a>.</p>';
			}
		}
	}
}
else
{
	// Define a query to return all rows from the accounts table where the email matches
	$SQLString="SELECT * FROM `accounts` WHERE `email`='$email';";

	// Run query and store the result in $QueryResult
	$QueryResult = mysqli_query($DBConnect,$SQLString);

	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	else
	{
		// Query will return NULL if there are no rows matching the query, indicating this email is not yet associated with an account
		if (mysqli_fetch_assoc($QueryResult)==NULL)
		{
			echo '<p>Account does not exist, would you like to <a href="createaccount.html">create an account</a>?</p>';
		}
		else
		{
			echo '<p>Account has not been activated, please check your email and follow the enclosed link in order to activate your account.</p>';
		}
	}
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