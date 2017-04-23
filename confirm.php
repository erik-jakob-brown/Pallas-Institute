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
// Create varriables and set their values to the corresponding data in the $_GET array
$email=$_GET['email'];

// bring in variables from dbinfo.php
include("dbinfo.php"); 

// this will connect to the mysql daemon 
// and prepare to use the brown database 
$DBConnect=mysqli_connect($dbhostname,$dbuser,$dbpass,$dbname[0]); 

// Query to change the status to 1
$SQLString="UPDATE `accounts` SET `status`=1 WHERE `email`='$email';";

// Run query
$QueryResult = mysqli_query($DBConnect,$SQLString);

if (mysqli_connect_errno())
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{
	// If the number of rows affected by the update query equals 0
	if (mysqli_affected_rows($DBConnect)==0)
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
				echo '<h2>Error</h2>';
				echo '<p>There is no account registered with the email address '.$email.'. Please create a <a href="newaccount.html">new account</a>.</p>';
			}
			// If the account exists but has already been activated
			else
			{
				echo '<h2>Error</h2>';
				echo '<p>Your account has already been activated, please <a href="schedule.php">login</a>.</p>';
			}
		}
	}
	else
	{
		// If the account is successfully activated
		echo '<h2>Success</h2>';
		echo '<p>Congratulations, your account is active, please <a href="schedule.php">login</a>.</p>';	
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