<?php
	/*
	Login script for security personel.
	*/
	session_start();
	$username = $_GET['username'];
	$password = $_GET['password'];
	
	// connect to the database.
	if ($username && $password)
	{
	
		// Define the db.
		$dbhost = "localhost";
		$dbroot = "user";
		$dbpassword = "Sspaciss123!";
		$dbname = "dbCC";
		
		//connect to the user
		$dbconnect = mysqli_connect($dbhost,$dbroot,$dbpassword,$dbname);
		
		if (mysqli_connect_errno($dbconnect))
		{
			echo "Fail".mysqli_connect_error();
		}
		else
		{
			//echo "hooah";
		}
		
	
		// prevent sql injection
		$username = mysqli_real_escape_string($dbconnect,$username);
		$password = mysqli_real_escape_string($dbconnect,$password);

		// build the query
		$query = "SELECT * FROM companion_db WHERE username = 
			'$username' && password = '$password'";
			
		// make the query
		$queryResult = mysqli_query($dbconnect,$query);
		
		// check to make sure that multiple values weren't returned
		$numRows = mysqli_num_rows($queryResult);
		
		if ($numRows != 0)
		{
			//echo "going through the rows.";
		
			
			// check the username and password submitted against the database.
			while ($row = mysqli_fetch_assoc($queryResult))
			{
				$dbusername = $row['username'];
				$dbpassword = $row['password'];
			}
			if ($dbusername == $username && $dbpassword == $password)
			{
				// send back 1 to allow tracking
				$query2 = "SELECT * FROM companion_db WHERE username = '$username'";
				$result2 = mysqli_query($dbconnect,$query);
				
				while ($row = mysqli_fetch_array($result2))
				{
					$sendBack = 1;
				}
			}
		}
		else
		{
			$sendBack = 0;
		}
	}
	else
	{
		//echo "Input both username and password";
		$sendBack = 0;
	}
	
	$_SESSION['sendBack'] = $sendBack;
	
	
	echo $sendBack;
?>