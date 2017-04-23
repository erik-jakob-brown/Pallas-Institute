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

echo 'Test';

 ?>

<?php

if (isset($_POST['email'])&&isset($_POST['message']))
{
	/* Use the PHP mail() function to send the form data to your email *and* a copy to the customer, set 
	the "reply-to address" to be the customer's email, and set the "from" email to be noreply@cts.gruv.org. */

	// Create variables and set their values to the corresponding data in the $_POST array
	$email=$_POST['email'];
	$message=$_POST['message'];
	$to='erik.brownj@gmail.com';
	$subject='Website Comment from '.$email;

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
	$headers .= 'From: <noreply@cts.gruv.org>'."\r\n";
	$headers .= 'Reply-To: <'.$email.'>';

	$status=mail($to,$subject,$message,$headers);

	if ($status==TRUE)
	{
		echo '<h2>Success</h2>';
		echo '<p>Your message has been sent.</p>';

	}
	else
	{
		echo '<h2>Failure</h2>';
		echo '<p>Your message could not be sent at this time, please email the webmaster at <a href="mailto:erik.brownj@gmail.com">erik.brownj@gmail.com</a>.</p>';
		
	}

	echo '<p>Email: '.$email.'</p>';
	echo '<p>Message: '.$message.'</p>';
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