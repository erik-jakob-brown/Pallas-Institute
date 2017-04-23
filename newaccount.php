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
$name=$_POST['name'];
$email=$_POST['email'];
$password=$_POST['password'];
$confirmPassword=$_POST['confirmPassword'];

// Begin password tests, set flags

/* Test to see if the passwords match */
if (strcmp($password,$confirmPassword)==0)
{
	$match=true;
}
else
{
	$match=false;
}

/* Test for a number in the string, store the result in the $number variable */
$number=preg_match("/[0-9]/",$password);
	
/* Test for a lower case character in the string, store the result in the $lowerCase variable */
$lowerCase=preg_match("/[a-z]/",$password);

/* Test for an upper case character in the string, store the result in the $upperCase variable */
$upperCase=preg_match("/[A-Z]/",$password);

/* Test for whitespace in the string, store the result in the $spaces variable (true means there is whitespace) */
$spaces=preg_match("/\s/",$password);

/* Test for a non alpha numeric character in the string, store the result in the $notAlphaNumeric variable */
$notAlphaNumeric=preg_match("/[^0-9A-Za-z]/",$password);

/* Test for the length of the password, store the result in the #PasswordLength variable */
$PasswordLength=strlen($password);
	
/* Test to see if the password meets the criteria of one number, one lowercase letter, one uppercase letter, 
no spaces, and at least one character that is not a letter or a number. The string should also be between 8 
and 16 characters long. */
if ($match&&$number&&$lowerCase&&$upperCase&&(!$spaces)&&$notAlphaNumeric&&$PasswordLength<17&&$PasswordLength>7)
{
	$strongPassword=true;
}
else
{
	/* The count variable is used to tell if we need a comma before the next failure condition listed in the paragraph */
	$count=0;
	
	/* Open the table column for the failure condition(s) */
	echo '<p>';
	
	/* See if the passwords match, if not */
	if (!$match)
	{
		/* Output number condition failure */
		echo 'Passwords do not match</p>';
		echo '<p>Return to the <a href="newaccount.html">new account</a> page and try again.</p>';
		
		/* Increment counter used to track commas in output */
		$count++;	
	}
	else
	{
		/* Output the failure result */
		echo '<p>Insecure password!</p>';
		
		/* Test the number condition, if not true */
		if (!$number)
		{
			/* Output number condition failure */
			echo '<p>No numbers';
			
			/* Increment counter used to track commas in output */
			$count++;	
		}
		
		/* Test the lower case condition, if not true */
		if (!$lowerCase)
		{
			/* Test the count variable, if nonzero */
			if ($count>0)
				/* Output failure with leading comma */
				echo ', No lower case characters';
			else
				/* Otherwise output failure without leading comma */
				echo '<p>No lower case characters';
			
			/* Increment counter used to track commas in output */
			$count++;
		}
		
		/* Test the upper case condition, if not true */
		if (!$upperCase)
		{
			/* Test the count variable, if nonzero */
			if ($count>0)
				/* Output failure with leading comma */
				echo ', No upper case characters';
			else
				/* Otherwise output failure without leading comma */
				echo '<p>No lower case characters';
			
			/* Increment counter used to track commas in output */
			$count++;
		}
		
		/* Test the spaces condition, if true */
		if ($spaces)
		{
			/* Test the count variable, if nonzero */
			if ($count>0)
				/* Output failure with leading comma */
				echo ', Illegal whitespace ';
			else
				/* Otherwise output failure without leading comma */
				echo '<p>Illegal whitespace';
			
			/* Increment counter used to track commas in output */
			$count++;
		}
		
		/* Test the non alpha numeric condition, if not true */
		if (!$notAlphaNumeric)
		{
			/* Test the count variable, if nonzero */
			if ($count>0)
				/* Output failure with leading comma */
				echo ', No special characters';
			else
				/* Otherwise output failure without leading comma */
				echo '<p>No special characters';
			
			/* Increment counter used to track commas in output */
			$count++;
		}
		
		/* Test the length condition, if not between 8 and 16 characters long */
		if (!($PasswordLength<17&&$PasswordLength>7))
		{
			/* Test the count variable, if nonzero */
			if ($count>0)
				/* Output failure with leading comma */
				echo ', Length must be between 8 and 16 characters';
			else
				/* Otherwise output failure without leading comma */
				echo '<p>Length must be between 8 and 16 characters';
			
			/* Increment counter used to track commas in output */
			$count++;
		}
		/* Close the paragraph */
		echo '</p>';
		echo '<p>Return to the <a href="newaccount.html">new account</a> page and try again.</p>';
	}
}
	
if ($strongPassword&&$match)
{
	// Define some variables for the confirmation email
	$subject='Confirm your Email to Activate your Account';

	// Define the massage with the activation link
	$message= 'Thank you for creating an account!  Please go <a href="https://cts.gruv.org/brown/final/confirm.php?email='.$email.'">here</a> in order to confirm your account.';

	// Define headers for confirmation email
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
	$headers .= 'From: <noreply@cts.gruv.org>'."\r\n";

	// Create salt, which is a randomly generated 16 digit number 
	$salt=strval(mt_rand(1000000000000000,9999999999999999));

	// Concatenate password onto the end of the salt and assign the value to the $saltedPassword variable
	$saltedPassword=$salt.$password;

	// Hash the password
	$hashedPassword=hash('sha256',($saltedPassword));

	 // bring in variables from dbinfo.php
	include("dbinfo.php"); 

	// this will connect to the mysql daemon 
	// and prepare to use the brown database 

	$DBConnect=mysqli_connect($dbhostname,$dbuser,$dbpass,$dbname[0]); 
	
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
			// Insert new row and pass the values collected and/or generated from the collected form data
			$SQLString="INSERT INTO `accounts` VALUES (NULL,'$email','$name','$salt','$hashedPassword',0);";
			
			// Run query and store the result in $QueryResult
			$QueryResult = mysqli_query($DBConnect,$SQLString);
			
			// Test for connection failure
			if (mysqli_connect_errno())
			{
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			else
			{
				// Send confirmation email and return success/failure to $status variable
				$status=mail($email,$subject,$message,$headers);
				
				// If the mail send successfully prompt user to return to the main page
				if ($status==TRUE)
				{
					echo '<h2>Success</h2>';
					echo '<p>A confirmation email has been sent to your account, please <a href="index.html">return to the index</a>.</p>';
				}
				else
				{
					// If the mail does not send prompt the user to contact the webmaster
					echo '<h2>Error</h2>';
					echo '<p>Your confirmation email could not be sent at this time, please contact the webmaster at the email address below</p>';
				}
			}
		}
		else
		{
			// If there is already an account with that email prompt the user to log in
			echo '<h2>Duplicate Account</h2>';
			echo '<p>Account already exists, please <a href="schedule.php">login</a>.</p>';
		}
	}
	
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