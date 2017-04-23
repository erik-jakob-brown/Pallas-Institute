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
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
<link href="css/master.css" rel="stylesheet" type="text/css" />
<title>The Pallas Institute</title>
<script type="text/javascript">
function goLastMonth(month,year)
{
	if (month==1)
	{
		// If the current month is 1, decrease the year and set the month to 12
		--year;
		month=12;
	}
	else
	{
		month--;
	}
	
	document.location.href="<?php $_SERVER['PHP_SELF'];?>?month="+month+"&year="+year;
}
function goNextMonth(month,year)
{
	if(month==12)
	{
		// If the current month is 12, increase the year and set month to 12
		++year;
		month=1;
	}
	else
	{
		month++;
	}
	
	document.location.href="<?php $_SERVER['PHP_SELF'];?>?month="+month+"&year="+year;
}
</script>
</head>
<body>

<div id="header"></div>

<article>

<?php
session_start();

// If the patientID index of the $_SESSION autoglobal is not blank (indicating the user is not logged in)
// Then display the calendar
if (!$_SESSION['patientID']=="")
{	
	if (isset($_GET['day']))
	{
		$day=$_GET['day'];
	}
	else
	{
		$day=date("j");
	}

	if (isset($_GET['month']))
	{
		$month=$_GET['month'];
	}
	else
	{
		$month=date("n");;
	}

	if (isset($_GET['year']))
	{
		$year=$_GET['year'];
	}
	else
	{
		$year=date("Y");
	}

	// Create a timestamp by passing the year, month and day to a strtotime function to be used in date() calls
	$currentTimeStamp = strtotime("$year-$month-$day");

	// Find the month name by calling the date() function and passing the timestamp
	$monthName = date("F",$currentTimeStamp);
	// Find the number of days in the month by calling the date() function and passing the timestamp
	$numDays=date("t",$currentTimeStamp);

	// Initialize a counter used to build calendar layout
	$counter=0;

	// Function used to generate an appointment tile, accepts a year (4 digits), a month (1-2 digits) and a day (1-2 digits) i.e. getAppts(year,month,day);
	function getAppts($apptYear,$apptMonth,$apptDay)
	{
		// bring in variables from dbinfo.php
		include("dbinfo.php"); 
		
		// this will connect to the mysql daemon 
		// and prepare to use the brown database 
		$DBConnect=mysqli_connect($dbhostname,$dbuser,$dbpass,$dbname[0]); 
		
		// Prepare $date variable for SQL query function, padding the month and day with leading 0 if necessary
		$apptDate=$apptYear.'-'.str_pad($apptMonth,2,'0', STR_PAD_LEFT).'-'.str_pad($apptDay,2,'0', STR_PAD_LEFT);

		// Query to check database to see how many appointments we have on the given date
		$SQLString="SELECT * FROM `appointments` WHERE `date`='$apptDate' ORDER BY `time` ASC;";

		// Run query
		$QueryResult = mysqli_query($DBConnect,$SQLString);

		// If there is a connection error
		if (mysqli_connect_errno())
		{
			// Print the error to the page
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		else
		{
			// If there are no rows returned
			if (mysqli_num_rows($QueryResult)===0)
			{
				// If the day is not today or any day previous and the day is not a Saturday or Sunday
				if 
				(
					!(strtotime($apptDate)<strtotime((date("Y")."-".date("n")."-".date("j"))))
					&&
					(
						!(strcasecmp((date("D", strtotime($apptDate))),"Sun")===0)
						&&
						!(strcasecmp((date("D", strtotime($apptDate))),"Sat")===0)
					)
				)
				{
					// Print a grid of selectable appointment slots
					echo '<a href="javascript:void(0)" class="button" time="9" date="'.$apptDate.'"><p class="appointment">Schedule for 9:00am</p></a>';
					echo '<a href="javascript:void(0)" class="button" time="10" date="'.$apptDate.'"><p class="appointment">Schedule for 10:00am</p></a>';
					echo '<a href="javascript:void(0)" class="button" time="11" date="'.$apptDate.'"><p class="appointment">Schedule for 11:00am</p></a>';
					echo '<a href="javascript:void(0)" class="button" time="12" date="'.$apptDate.'"><p class="appointment">Schedule for 12:00pm</p></a>';
					echo '<a href="javascript:void(0)" class="button" time="13" date="'.$apptDate.'"><p class="appointment">Schedule for 1:00pm</p></a>';
					echo '<a href="javascript:void(0)" class="button" time="14" date="'.$apptDate.'"><p class="appointment">Schedule for 2:00pm</p></a>';
					echo '<a href="javascript:void(0)" class="button" time="15" date="'.$apptDate.'"><p class="appointment">Schedule for 3:00pm</p></a>';
					echo '<a href="javascript:void(0)" class="button" time="16" date="'.$apptDate.'"><p class="appointment">Schedule for 4:00pm</p></a>';
					echo '<a href="javascript:void(0)" class="button" time="17" date="'.$apptDate.'"><p class="appointment">Schedule for 5:00pm</p></a>';
				}
			}	
			else
			{
				// If the day is not today or any day previous and the day is not a Saturday or Sunday
				if 
				(
					!(strtotime($apptDate)<strtotime((date("Y")."-".date("n")."-".(date("j")))))
					&&
					(
						!(strcasecmp((date("D", strtotime($apptDate))),"Sun")===0)
						&&
						!(strcasecmp((date("D", strtotime($apptDate))),"Sat")===0)
					)
				)
				{
					/* Welcome to the loops of doom */
					
					// Outer loop prints out empty appointment slots before and after claimed appointment blocks
					// For every hour 9 and greater but less than 18
					for ($hour=9;$hour<18;$hour++)
					{
						// Middle loop iterates through the claimed appointments blocks and prints them out
						// While there are still appointments in the current day 
						while (($Row = mysqli_fetch_row($QueryResult)) !== NULL)
						{
							// Explode the time variable around the ':' delimiter in order to see what the hours are
							$timeExplode=explode(':',$Row[2]);
							
							// Trim any leading zeros from the hours
							$apptHour=ltrim($timeExplode[0], '0');
							
							// Inner loop prints out empty appointment slots between claimed appointments
							// While the hour value in the time column of the row being evaluated in the while loop does not match the hour we are currently evaluating in the for loop
							while ($hour!=$apptHour&&$hour<17)
							{
								// If the current time being evaluated in the for loop is after noon
								if ($hour>12)
								{
									// Print it out in PM format
									echo '<a href="javascript:void(0)" class="button" time="'.$hour.'" date="'.$apptDate.'"><p class="appointment">Schedule for '.($hour-12).':00pm</p></a>';
								}
								else
								{
									if ($hour==12)
									{
										// Print it out in PM format
										echo '<a href="javascript:void(0)" class="button" time="'.$hour.'" date="'.$apptDate.'"><p class="appointment">Schedule for '.$hour.':00pm</p></a>';
									}								
									else
									{
										// Otherwise print it out in AM format
										echo '<a href="javascript:void(0)" class="button" time="'.$hour.'" date="'.$apptDate.'"><p class="appointment">Schedule for '.$hour.':00am</p></a>';
									}
								}
								
								// Remember to increment the hour so that the same time slot is not evaluated again
								$hour++;
							}
							
							// Now that we have a match print out that the appointment is taken
							// If the current time being evaluated in the for loop is after noon
							if ($apptHour>12)
							{
								// Print it out in PM format
								echo '<p class="appointment">'.($apptHour-12).':00pm is taken</p>';
							}
							else
							{
								if ($apptHour==12)
								{
									echo '<p class="appointment">'.$apptHour.':00pm is taken</p>';
								}
								else
								{
									// Otherwise print it out in AM format
									echo '<p class="appointment">'.$apptHour.':00am is taken</p>';
								}
							}
							// Remember to increment the hour so that the same time slot is not evaluated again
							$hour++;
						}
						// If the current time being evaluated in the for loop is after noon and isn't 18:00 (6:00pm)
						if ($hour>12&&$hour!=18)
						{
							// Print it out in PM format
							echo '<a href="javascript:void(0)" class="button" time="'.$hour.'" date="'.$apptDate.'"><p class="appointment">Schedule for '.($hour-12).':00pm</p></a>';
						}
						else
						{
							if ($apptHour==12)
							{
								echo '<a href="javascript:void(0)" class="button" time="'.$hour.'" date="'.$apptDate.'"><p class="appointment">Schedule for '.$hour.':00pm</p></a>';
							}
							else
							{
								{
									// Otherwise print it out in AM format
									echo '<a href="javascript:void(0)" class="button" time="'.$hour.'" date="'.$apptDate.'"><p class="appointment">Schedule for '.$hour.':00am</p></a>';
								}
							}
						}
					}
				}
			}
		}	
	}
	
	// Print a hidden form in order to pass variables for the chosen appointment date via a POST method instead of a GET method using jQuery
	// (in order to hide the variables to make tampering difficult)
	echo'<form action="makeappt.php" method="post" name="redirect" class="redirect">';
	echo '<input type="hidden" class="time" name="time" value="">';
	echo '<input type="hidden" class="date" name="date" value="">';
	echo '<input type="submit" style="display: none;">';
	echo '</form>';

	// Print the beginning of the div used to hold the calendar grid
	echo '<div class="calendar-frame calendar">';
	echo '<h3>';
	
	// If this is the current month print the back button disabled, otherwise print the back button normal
	if (strtotime(date("Y")."-".date("n")."-".date("j"))>=$currentTimeStamp)
	{
		echo '<input style="width:50px" type="button" value="<" name="previousbutton" disabled>';
	}	
	else
	{
		echo '<input style="width:50px" type="button" value="<" name="previousbutton"	onclick="goLastMonth('.$month.','.$year.')">';
	}
	echo '&nbsp;'.$monthName.' '.$year.'&nbsp;';
	
	echo '<input style="width:50px" type="button" value=">" name="nextbutton"	onclick="goNextMonth('.$month.','.$year.')">';
	echo '</h3>';
	
	// Print out the weekday label bar
	echo '<ul class="weekdays">';
	echo '<li>Sun&nbsp;&nbsp;</li>';
	echo '<li>Mon&nbsp;&nbsp;</li>';
	echo '<li>Tue&nbsp;&nbsp;</li>';
	echo '<li>Wed&nbsp;&nbsp;</li>';
	echo '<li>Thu&nbsp;&nbsp;</li>';
	echo '<li>Fri&nbsp;&nbsp;</li>';
	echo '<li>Sat&nbsp;&nbsp;</li>';
	echo '</ul>';
		
	// Begin row
	echo '<ul class="days">';
	for ($i=1;$i<$numDays+1;$i++,$counter++)
	{
		// Initialize timestamp
		$timeStamp=strtotime($year."-".$month."-".$i);
		
		/**************************** First we need to build the cells before the start of the month in focus ****************************/
		
		// If this is our first time through the loop
		if ($i==1)
		{
			// Insert blank cells until the first day of the month
			$firstDay=date("w",$timeStamp);
				
			// Figure out what the date of Sunday in the last week of the previous month is and assign it to the $days_ago variable
			$days_ago = date('Y-m-d', strtotime('-'.$firstDay.' days', strtotime($year.'-'.$month.'-01')));
			
			// Explode $days_ago into an array called $explode_days_ago splitting by the '-' character
			$explode_days_ago=explode('-',$days_ago);
				
			// Loop through all the days from last month (out-of-range) and print them out
			for($j=0;$j<$firstDay;$j++,$counter++)
			{
				// If the current day being evaluated is the same as or subsequent to today's date minus the number of days since Sunday
				if ($timeStamp>=strtotime(date("Y")."-".date("n")."-".(date("j")-(date("w",strtotime(date("Y")."-".date("n")."-".date("j")))))))
				{
					// Print out a calendar cell
					echo '<li class="calendar out-of-range"><div class="date">'.$explode_days_ago[2].'&nbsp;&nbsp;</div>';
					
					// Call getAppts to print the appointment in the cell
					getAppts($explode_days_ago[0],$explode_days_ago[1],$explode_days_ago[2]);
					
					// Close calendar cell
					echo '</li>';
						
					// Increment the number used to track the day number
					$explode_days_ago[2]++;
				}
			}
		}
		// Start a new row when we are at the end of the week
		if($counter%7==0)
		{
			echo '</ul><ul class="days">';
		}	
		
		/**************************** Next we need to build the cells for the month in focus ****************************/
		
		// If the current day being evaluated is the same as or subsequent to today's date minus the number of days since Sunday
		if ($timeStamp>=strtotime(date("Y")."-".date("n")."-".(date("j")-(date("w",strtotime(date("Y")."-".date("n")."-".date("j")))))))
		{
			// Create cell with the number of day in it, if it is today mark it as today, otherwise just print the cell
			if (!strcmp(($year."-".$month."-".$i),(date("Y")."-".date("n")."-".date("j"))))
			{
				echo '<li class="calendar calendar-today"><div class="date"><span class="today">&nbsp;&nbsp;Today</span><span class="day">Day&nbsp;</span><span class="month">Month&nbsp;</span>'.$i.'&nbsp;&nbsp;</div>';
			}
			else
			{
				echo '<li class="calendar"><div class="date"><span class="day">'.date("D", $timeStamp).'&nbsp;</span><span class="month">'.date("M", $timeStamp).'&nbsp;</span>'.$i.'&nbsp;&nbsp;</div>';
			}
				
			getAppts($year,$month,$i);
					
			echo '</li>';
		}
	}
		
	/**************************** Last we need to build the cells for the month after the month in focus ****************************/
			
	//Initialize variable to store the current day in the next month
	$nextMonthDay=1;
			
	if($month==12)
	{
		// If the current month is 12, increase the year and set month to 12
		++$year;
		$month=1;
	}
	else
	{
		$month++;
	}

	while ($counter%7!=0)
	{	
		// Print cell for next month day
		echo '<li class="calendar out-of-range"><div class="date">'.$nextMonthDay.'&nbsp;&nbsp;</div>';
			
		getAppts($year,$month,$nextMonthDay);
			
		echo '</li>';
			
		// Increment counter
		$counter++;
		
		// Increment $nextMonthDays;
		$nextMonthDay++;
	}
	
	// End row
	echo '</ul>';
}
// If the user is not logged in prompt the user to login or create an account
else
{
	echo '<h3>Log In</h3>';
	
	echo '<form name="Login" method="post" action="login.php">';

	echo '<p>';
	echo 'Email:';
	echo '<br />';
	echo '<input type="email" name="email" required />';
	echo '</p>';

	echo '<p>';
	echo 'Password:';
	echo '<br />';
	echo '<input type="password" name="password" size="15" />';
	echo '</p>';

	echo '<br />';

	echo '<div style="text-align: center;">';
	echo '<input type="submit" name="submit" value="Login" />&nbsp;';
	echo '<input type="reset" name="reset" value="Clear" />';
	echo '</div>';

	echo '</form>';
	echo '<p>Or <a href="newaccount.html">create a new account</a></p>';
}
?>

</div>

</article>

<div id="footer"></div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">

// This jQuery script passes the time and date  of the chosen appointment slot via a GET command
$(".button").click(function() {
    var time = $(this).attr('time');
    $('.time').attr("value",time);
	
	var date = $(this).attr('date');
	$('.date').attr("value",date);
	
	//it is pm if hours from 12 onwards
    var suffix = (time >= 12)? 'pm' : 'am';

    //only -12 from hours if it is greater than 12 (if not back at mid night)
    var hours = (time > 12)? time-12 : time;

    //if 00 then it is 12 am
    var hours = (time == '00')? 12 : hours;
	
	// Convert date format for alert
	var d=date.substr(0, 10).split("-")
	d = d[1] + "-" + d[2] + "-" + d[0];
	
	// This confirmation box asks if the user is sure they want to schedule the appointment
	var result=confirm("Are you sure you want to schedule an appointment for "+hours+":00"+suffix+" on "+d+"?");
	
	if (result==true)
	{
		$('.redirect').submit();
	}
});

// Load the header and footer
$(function(){
  $("#header").load("header.html"); 
  $("#footer").load("footer.html"); 
});
</script>

</body>
</html>
