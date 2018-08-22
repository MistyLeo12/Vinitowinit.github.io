<?php
	
	//errors to consider
	// (1) Currently, we will continue with table construction even if the 
	//       the facebook update portion doesn't work. 
	// (2) Does PART 1 work if no new items are returned?
	// (3) What happens if we can't connect to the database?...or in general, if the 
	//         the set of table items can't be returned?

include('tools.php');	
include('tools_fmc.php');
include('tools_facebook_v2.php');



include('db_connect.php');
$objFb = new classFacebookFmc('1920852034722181');
$path_root = fmc_get_uploads_path();

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

// Get most recent recorded database entry
$query_str = "SELECT MAX(upload_timestamp) AS upload_timestamp FROM table_main";
$query = mysqli_query($connect,$query_str);
if (!$query) {
	echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
}
$results_array = get_array_from_mysqli_result($query);
$date_of_most_recent_db_item = $results_array[0]['upload_timestamp'];
// Add buffer time
$date_of_most_recent_db_item = $date_of_most_recent_db_item-$BUFFER_TIME;
// Get any new facebook posts & instantiate facebook object
$newPostsArr = $objFb->getNewFmcWallPosts($date_of_most_recent_db_item);
echo var_dump($newPostsArr);
//file_put_contents("Tmpfile.zip", fopen("http://someurl/file.zip", 'r'));

// NOTE: do not forget to update the other scripts that check 
//          for new facebook uploads!! Also, you still need to code up 
//			the function 'fmc_add_post_to_db_with_image'

// Upload name of each file successfully uploaded
$newPostsArr = array_reverse($newPostsArr);
foreach ($newPostsArr as $key => $newPostItem)
{
	// Pop the 'upload_image_url' from each item
	$upload_image_url = $newPostItem['upload_image_url'];
	unset($newPostItem['upload_image_url']);
	// check if 'upload_image_url' is initialized
	if (isset($upload_image_url)){ 
		fmc_add_post_to_db_with_image($connect, $newPostItem, $upload_image_url); 		// if it is set
	}else{
		// if isset($upload_image_url)
		fmc_add_post_to_db($connect,$newPostItem);  //from 'tools_fmc.php'
	}
}

	
	
	// PART 2: NOW GET ALL THE REQUESTS CURRENTLY IN THE SERVER & RETURN THEM TO THE BROWSER	
	/* *********************************************************** */
	
	if ( isset($_REQUEST['upload_unique_id']))  //If this variable is set, then return just one list item
	{
		$upload_unique_id = $_REQUEST['upload_unique_id'];
		$query = "SELECT * FROM table_main WHERE upload_unique_id=$upload_unique_id";
	}else{
		$query = "SELECT * FROM table_main";
	}
	//Now get the full database table and return it to the browser
	$result = mysqli_query($connect,$query);
	//$results_array = mysqli_fetch_all($result,MYSQLI_ASSOC);
	// Check if there are any new results
	if (mysqli_num_rows($result)>0){
		// format and print out the alerts in XML format
		$result_xml_tips = result_to_xml($result, 't_alerts', 'alert');  // 2nd input is root tag, 3rd input is the tag for each structure element
		echo $result_xml_tips;
	}else{
		echo '<t_alerts></t_alerts>';
	}

	include('db_close.php');

	
?>