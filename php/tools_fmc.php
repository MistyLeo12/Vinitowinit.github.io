<?php

//include('tools.php');
function fmc_check_for_new_fmc_admin($connect,$objFb,$user_access_token_facebook)
{
	// Check for any members from facebook that are not currently in the
	// members list on our server database.  This is probably not the most
	// efficient method.
	$isAdminOnly = true; // get only administrators from group
	// Get list of members on facebook
	$members_array = $objFb->getMembersOfFacebookGroup($user_access_token_facebook,$isAdminOnly);
	echo $members_array;
	
	// add each member to the array if they don't already exist
	$property_name_for_uniqueness = 'member_id';
	$table_name = 'fmc_members';
	
	// CREATE TABLE IF IT DOESN'T EXIST
	$query_str = "CREATE TABLE IF NOT EXISTS `fmc_members` ( `member_id` text NOT NULL,`member` text NOT NULL,`is_deleted` int(11) NOT NULL);";
	$query_out = mysqli_query($connect,$query_str);
	if (!$query_out) {
		echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
	}
	foreach ($members_array as $value){
		// UPDATE TABLE WITH ANY NEW MEMBERS
		$query_str = array_to_sql_insert_str_if_unique($value,$table_name,$property_name_for_uniqueness);
		// Check first that table exists
		$query_out = mysqli_query($connect,$query_str);
		if (!$query_out) {
			echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
		}
	}

}



function fmc_check_for_recent_facebook_web_posts($connect,$objFb,$uploads_directory,$user_access_token_facebook)
{
	// PART 1:  CHECK FOR ANY NEW WEB-BASED FACEBOOK UPLOADS
	//------------------------------------------------------
	//Get date of most recent post
	//Connect to DB
	// Insert the relevant alert variables into the database
	
	// MARGIN OF ERROR BETWEEN FACEBOOK TIMES AND DATABASE TIMES
	// this should only really be included when there is a check for
	// duplicate entries...otherwise this would cause repeated entries in database
	$n_minutes_buffer_time = 1;
	$BUFFER_TIME = $n_minutes_buffer_time*60;
	
	
	if(mysqli_num_rows(mysqli_query($connect,"SHOW TABLES LIKE 'table_main'"))==1)
	{
		echo "     Table exists    ";
	}else{
		$objFb->facebookQueryResultLengthLimit = '500';
		// CREATE TABLE IF IT DOESN'T EXIST
		$query_str = "
						CREATE TABLE IF NOT EXISTS `table_main` (
			  `upload_unique_id` int(11) NOT NULL AUTO_INCREMENT,
			  `upload_timestamp` double NOT NULL,
			  `user_name_facebook` text NOT NULL,
			  `user_location_x` double NOT NULL,
			  `user_location_y` double NOT NULL,
			  `upload_origin` int(11) NOT NULL,
			  `upload_unique_facebook_id` text NOT NULL,
			  `upload_text` text NOT NULL,
			  `upload_image_address` text NOT NULL,
			  `upload_audio_address` text NOT NULL,
			  `upload_video_address` text NOT NULL,
			  `fmc_member_assigned` text NOT NULL,
			  `fmc_project_status` int(11) NOT NULL,
			  `fmc_department` text NOT NULL,
			  `user_access_token_facebook` text NOT NULL,
			  `isDeleted` int(11) NOT NULL,
			  `isAnonymous` int(11) NOT NULL,
			  `user_unique_facebook_id` text NOT NULL,
			  PRIMARY KEY (`upload_unique_id`)
			);";
		$query_out = mysqli_query($connect,$query_str);
		if (!$query_out) {
			echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
		}
	
	}	
	
	// CONTINUE WITH THE SCRIPT
	// Get most recent recorded database entry
	$query_str = "SELECT MAX(upload_timestamp) AS upload_timestamp FROM table_main WHERE upload_origin=3";
	$query = mysqli_query($connect,$query_str);
	if (!$query) {
		echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
	}
	$results_array = get_array_from_mysqli_result($query);
	
	
	if (!results_array){
		$date_of_most_recent_db_item = 0;
	}else{
		$date_of_most_recent_db_item = $results_array[0]['upload_timestamp'];
	}
	
	
	
	// Add buffer time
	$date_of_most_recent_db_item = $date_of_most_recent_db_item-$BUFFER_TIME;
	// Get any new facebook posts & instantiate facebook object
	$newPostsArr = $objFb->getNewGroupWallPosts($date_of_most_recent_db_item,$user_access_token_facebook);
	
	//echo var_dump($newPostsArr);
	//file_put_contents("Tmpfile.zip", fopen("http://someurl/file.zip", 'r'));
	

	// Upload name of each file successfully uploaded
	$newPostsArr = array_reverse($newPostsArr);
	foreach ($newPostsArr as $key => $newPostItem)
	{
		// Pop the 'upload_image_url' from each item
		$upload_image_url = $newPostItem['upload_image_url'];
		unset($newPostItem['upload_image_url']);
		// check if 'upload_image_url' is initialized
		if (isset($upload_image_url)){
			fmc_add_post_to_db_with_image($connect, $newPostItem, $upload_image_url,$uploads_directory); 		// if it is set
		}else{
			// if isset($upload_image_url)
			fmc_add_post_to_db($connect,$newPostItem);  //from 'tools_fmc.php'
		}
	}
	
	
}


function fmc_get_uploads_path()
//	Detects if we're on server or on the web
//  As written now (2014.08.19) the code checks if the server is 
//  on a windows machine or a linux machine.  If it is windows then 
//  it assumes we're on localhost
{
	$isWindows = PHP_OS=='WINNT';
	if ($isWindows){
		$path_root = $_SERVER['DOCUMENT_ROOT'] . '/fmc_web/uploads/';
	}else{
		$path_root = $_SERVER['DOCUMENT_ROOT'] . '/uploads/'; //on server
	}
	return $path_root;
}



function fmc_add_post_to_db($connect,$http_post_arr){
	
	// Get unique facebook id
	//$upload_unique_facebook_id = $http_post_arr['upload_unique_facebook_id'];
	///$path_root = fmc_get_uploads_path();
	
	// MAKE QUERY STRING
	$property_name_for_uniqueness = 'upload_unique_facebook_id';
	$query_str = array_to_sql_insert_str_if_unique($http_post_arr,'table_main',$property_name_for_uniqueness);
	
	// RUN QUERY
	$query = mysqli_query($connect,$query_str);
	if (!$query) {
		echo nl2br("\n"); printf('Web-based facebook upload not added to database'); echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
		echo printf('      ' . $query_str . '           ');
	}else{
		echo nl2br("\n"); printf('Successfully added note from facebook group wall'); echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
		echo printf('      ' . $query_str . '           ');
	}
	
	
}

function fmc_add_post_to_db_with_image($connect,$http_post_arr,$image_url,$path_image_uploads){
	// THIS EXCLUSIVELY FOR USE IN 'getNewFmcWallPosts' in the
	// classFacebookFmc
	
	// Get unique facebook id
	//$upload_unique_facebook_id = $http_post_arr['upload_unique_facebook_id'];
	///$path_root = fmc_get_uploads_path();

	// MAKE QUERY STRING
	$property_name_for_uniqueness = 'upload_unique_facebook_id';
	$query_str = array_to_sql_insert_str_if_unique($http_post_arr,'table_main',$property_name_for_uniqueness);

	// RUN QUERY
	$query = mysqli_query($connect,$query_str);
	if (!$query) {
		echo nl2br("\n"); printf('Web-based facebook upload not added to database'); echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
	}
	// Upload name of each file successfully uploaded
	$last_id = mysqli_insert_id($connect);	
	
	// COPY IMAGE FROM FACEBOOK ONTO DATABASE
	$prop_name = 'attachment_image';
	//$path_root = fmc_get_uploads_path();
	//$ext = pathinfo($_FILES[$prop_name]['name'], PATHINFO_EXTENSION);  
	$ext = 'jpg';	// facebook always saves as jpeg
	$save_path1 = '//uploads//' . $last_id .'_'.$prop_name.'.'.$ext;  // Should read '22342_attachement_audio'
	$save_path2 = $path_image_uploads . $last_id .'_'.$prop_name.'.'.$ext;  // Should read '22342_attachement_audio'
	//$save_path2 = $path_root . $last_id .'_'.$prop_name.'.'.$ext;  // Should read '22342_attachement_audio'
	
	try{
		copy($image_url,$save_path2);
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	//echo nl2br("\n"); printf("COPY_IMAGE_FNAME_GIVEN"); echo nl2br("\n"); echo $save_path2; echo nl2br("\n");
	
	// ADD IMAGE ADDRESS TO DATABASE
	$query_str = "UPDATE table_main SET upload_image_address='$save_path1' WHERE upload_unique_id=$last_id";
	$query = mysqli_query($connect,$query_str);
	if (!$query) {
		echo nl2br("\n"); printf("Image address not added to database"); echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
	}
	
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




?>
