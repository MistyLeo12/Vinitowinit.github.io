<?php

/**
 * @author Jordan Malof, jmmalo03@gmail.com
 * @version 3.0 
 */

// allow cross-domain access
header('Access-Control-Allow-Origin: *');

// This is an array that contains the following important information:
// the database/tables being used, the URL being used, the facebook app being used,
// and the group page being pointed to 
include('config_fmc.php');
$config_object = $array_config; 
include('classFacebook.php');
include('tools.php'); // additional small tools
include('tools_fmc.php');

// Instantiate an object of the class with the $config_object
$objFb = new classFacebookFmc($config_object);
$connect = db_connect($config_object); // this returns the $connect object
$path_root = $config_object['path_image_uploads'];
$path_image_uploads = $config_object['path_image_uploads'];
//$path_root = fmc_get_uploads_path(); // get the root path to save things in
$user_access_token_facebook = $_REQUEST['user_access_token_facebook'];

// NO LONGER DO THIS PART DUE TO FACEBOOK RESTRICTIONS ON CHECKING GROUP WALLS
//---------------------------------------------------------------
// PART 1: CHECK FOR ANY NEW WEB-BASED FACEBOOK UPLOADS
//fmc_check_for_recent_facebook_web_posts($connect,$objFb,$path_image_uploads,$user_access_token_facebook);
//---------------------------------------------------------------

// PART 2:  ADD THE MOBILE UPLOAD TO THE SERVER
//------------------------------------------------------
//This is the set of key-values we're looking for from the phone
$upload_text = array_key_exists('upload_text',$_REQUEST) ? $_REQUEST['upload_text'] : ' ';
$user_location_x = array_key_exists('user_location_x',$_REQUEST) ? $_REQUEST['user_location_x'] : ' ';
$user_location_y = array_key_exists('user_location_y',$_REQUEST) ? $_REQUEST['user_location_y'] : ' ';
$user_name_facebook = array_key_exists('user_name_facebook',$_REQUEST) ? $_REQUEST['user_name_facebook'] : ' ';
$user_access_token_facebook = array_key_exists('user_access_token_facebook',$_REQUEST) ? $_REQUEST['user_access_token_facebook'] : ' ';
$upload_origin  = array_key_exists('origin',$_REQUEST) ? $_REQUEST['origin'] : 2;
$upload_timestamp = array_key_exists('time',$_REQUEST) ? $_REQUEST['time'] : time();
$isAnonymous = array_key_exists('isAnonymous',$_REQUEST) ? $_REQUEST['isAnonymous'] : ' ';
$stmt = mysqli_prepare($connect,"INSERT INTO table_main (user_access_token_facebook,upload_origin,upload_timestamp,upload_text,user_location_x,user_location_y,user_name_facebook,isAnonymous) VALUES (?,?,?,?,?,?,?,?)");
mysqli_stmt_bind_param($stmt,"ssssssss",$user_access_token_facebook,$upload_origin,$upload_timestamp,$upload_text,$user_location_x,$user_location_y,$user_name_facebook,$isAnonymous);
mysqli_stmt_execute($stmt);
if (mysqli_stmt_error($stmt)) {
	echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_stmt_errno($stmt)); echo mysqli_stmt_error($stmt); echo nl2br("\n");
}
mysqli_stmt_close($stmt);
//UPLOAD EACH FILE ATTACHMENT
// check for image and audio, no video
$attachment_types = array("image","audio","video");  // Attachment types
$upload_address_str = array(' ',' ',' '); // Assume upload unssuccessful until proven otherwise
foreach ($attachment_types as $key => $value){
	//Get necessary names
	$prop_name = 'attachment_'.$value;
	$ext = pathinfo($_FILES[$prop_name]['name'], PATHINFO_EXTENSION);
	$save_path1 = '//uploads//' . mysqli_insert_id($connect).'_'.$prop_name.'.'.$ext;  // Should read '22342_attachement_audio'
	$save_path2 = $path_root . mysqli_insert_id($connect).'_'.$prop_name.'.'.$ext;  // Should read '22342_attachement_audio'
	//$save_path = '//uploads//test_file.jpg';  // Should read '22342_attachement_audio'
	if (array_key_exists("prop_name",$_FILES)) { //Check to see if file exists
		if($_FILES[$prop_name]['error'] == 0){
			if(move_uploaded_file($_FILES[$prop_name]['tmp_name'], $save_path2)) {
				echo nl2br("\n"); echo "The ".$prop_name." file has been uploaded."; echo nl2br("\n");
				echo nl2br("\n"); echo$_FILES[$prop_name]['tmp_name']; echo nl2br("\n");
				echo nl2br("\n"); echo $save_path2; echo nl2br("\n");
				$upload_address_str[$key]=$save_path1;
			} else{
				echo "The file ".$prop_name." was received by the server, but not properly copied";
				echo nl2br("\n"); echo$_FILES[$prop_name]['tmp_name']; echo nl2br("\n");
				echo nl2br("\n"); echo $save_path2; echo nl2br("\n");
			}
		}else{
			echo "No " .$prop_name . " uploaded ";
		}
	}
}
// Upload name of each file successfully uploaded
$last_id = mysqli_insert_id($connect);
$query_str = "UPDATE table_main SET upload_image_address='$upload_address_str[0]',
upload_audio_address='$upload_address_str[1]',upload_video_address='$upload_address_str[2]'
WHERE upload_unique_id=$last_id";
$query = mysqli_query($connect,$query_str);
if (!$query) {
	echo nl2br("\n"); printf("Attachment upload error"); echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
}

echo nl2br("\n"); printf("SUCCESS 4: UPLOAD MADE IT TO SERVER DB"); echo nl2br("\n"); 

// PART 3:  PUSH THE MOBILE UPLOAD ONTO FACEBOOK
//------------------------------------------------------
$upload_text = array_key_exists('upload_text',$_REQUEST) ? $_REQUEST['upload_text'] : ' ';
$isAnonymous = $_REQUEST['isAnonymous'];

echo nl2br("\n");
printf("isAnonymous=");
echo var_dump($isAnonymous);
// Don't post to facebook 
if ($isAnonymous==1){
	include('db_close.php');
	echo nl2br("\n"); printf("SUCCESS 5: EXITED WITHOUT POSTING TO FB BECUASE isAnonymous=1"); echo nl2br("\n");
	return;
}

$upload_text = $upload_text . " 
		 [posted via FMC mobile app]";
//  ----------------------
if ($_FILES['attachment_image']['size']!=0){
	// Doesn't 
	$ext = pathinfo($_FILES['attachment_image']['name'], PATHINFO_EXTENSION);
	$save_path2 = $config_object['uploads_directory']."$last_id".'_attachment_image.' .$ext;
	$post_item = $objFb->postMessageAndImageToGroupWall($user_access_token_facebook,$upload_text,$save_path2);
}elseif($_FILES['attachment_video']['size']!=0){
	// Doesn't
	$ext = pathinfo($_FILES['attachment_video']['name'], PATHINFO_EXTENSION);
	$save_path2 = $config_object['uploads_directory']."$last_id".'_attachment_video.' .$ext;
	$post_item = $objFb->postMessageAndImageToGroupWall($user_access_token_facebook,$upload_text,$save_path2);
}else{
	$post_item = $objFb->postMessageToGroupWall($user_access_token_facebook,$upload_text);
}

echo nl2br("\n"); printf("SUCCESS 5: POSTED TO FACEBOOK SUCCESSFULLY"); echo nl2br("\n");

//$post_item = $objFb->postToFmcWall($user_access_token_facebook,$upload_text);
//pull out details of new facebook post
$post_id = $post_item['upload_unique_facebook_id'];
$user_unique_facebook_id = $post_item['user_unique_facebook_id'];
$user_name_facebook = $post_item['user_name_facebook']; 
$upload_timestamp = $post_item['upload_timestamp'];
$upload_text = $post_item['upload_text'];

$query_str = "UPDATE table_main SET upload_unique_facebook_id='$post_id',
user_unique_facebook_id='$user_unique_facebook_id',user_name_facebook='$user_name_facebook',
upload_timestamp='$upload_timestamp' WHERE upload_unique_id=$last_id";
$query = mysqli_query($connect,$query_str);
if (!$query) {
	echo nl2br("\n"); printf("facebook details of post were not uploaded to the database"); echo nl2br("\n"); printf("Errorcode: %d\n", mysqli_errno($connect)); echo nl2br("\n");
}

echo nl2br("\n"); printf("SUCCESS 6: FACEBOOK POST DETAILS ADDED TO SERVER"); echo nl2br("\n");
// Update database 
include('db_close.php');

	
?>