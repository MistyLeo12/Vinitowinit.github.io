<?php
	echo nl2br("\n"); printf("you made it to the server"); echo nl2br("\n");
	include('config_fmc.php');
	$config_object = $array_config;
	include('classFacebook.php');
	include('tools.php'); // additional small tools
	include('tools_fmc.php');
	echo nl2br("\n"); printf("you made it to the server"); echo nl2br("\n");

	//include('db_connect.php');
	$objFb = new classFacebookFmc($config_object);
	$connect = db_connect($config_object); // this returns the $connect object
	$path_root = $config_object['path_root'];
	$path_image_uploads = $config_object['path_image_uploads'];
	//$path_root = fmc_get_uploads_path();

	// PART 1: VALIDATE THE FACEBOOK ACCESS TOKEN
	//------------------------------------------------------
	$user_access_token_facebook = $_REQUEST['user_access_token_facebook']; 
	echo $user_access_token_facebook;
	$outFlag = $objFb->checkAdminAccessToken($user_access_token_facebook);
	echo "  the flag is";
	echo $outFlag;
	if (!$outFlag){
		return; 
	}
	echo " you made it past token verification";
	
	$newDepoName = $_POST['newDepoName'];
	
	/* Echoed and checked to this point. */
	
	// Insert into the database the new member.
	$table_name = $_POST['tableName'];
	
	$queryStr = "INSERT INTO `".$table_name."`(`department`) VALUES ('".$newDepoName."')";
	
	
	// Check if there are any new results
	if ($query = mysqli_query($connect,$queryStr)){
		// format and print out the alerts in XML format
		echo '<t_alerts><alert><finish_flag>1</finish_flag></alert></t_alerts>';
	}else{
		echo '<t_alerts><alert><finish_flag>0</finish_flag></alert></t_alerts>';
	}

    printf("Result set has %d rows.\n", $num_rows);
	if (!$query) {
		echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
	}
	
	include('db_close.php');
	
?>