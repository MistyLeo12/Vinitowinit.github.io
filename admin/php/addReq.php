<?php 
header('Content-Type: application/json');
require_once("config_fmc.php");
require_once("db_connect.php");
require_once("fbAdminCheck.php");
//$db variable should be available now

//make sure to protect against injection
$req = json_decode($_POST['req']);
$upload_timestamp = DateTime::createFromFormat("Y-n-d H:i:sP",str_replace("T"," ",str_replace(" ","+",$req->created_time)));
//error_log(str_replace("T"," ",str_replace(" ","+",$req->created_time)));
//if (!$upload_timestamp) {
//	error_log($_POST['req']);
//}
$upload_timestamp = $upload_timestamp->getTimestamp();
$user_name_facebook = $req->from->name;
$upload_unique_facebook_id = $req->id;
$upload_text = $req->message;
if (property_exists($req, 'picture')) {
	$upload_image_address = $req->link;
} else {$upload_image_address = "";}
if (property_exists($req, 'source')) {
	$upload_video_address = $req->source;
} else {$upload_video_address = "";}
$user_unique_facebook_id = $req->from->id;

$stmt = $db->prepare('INSERT INTO table_main (
	upload_timestamp, user_name_facebook, user_location_x, user_location_y, upload_origin, upload_unique_facebook_id,
	upload_text, upload_image_address, upload_audio_address, upload_video_address, fmc_member_assigned,
	fmc_project_status, fmc_department, user_access_token_facebook, isDeleted, isAnonymous, user_unique_facebook_id) 
	VALUES (?,?,0,0,3,?,?,?,"",?,"",0,"","",0,0,?)');
$stmt->bind_param("sssssss",$upload_timestamp,$user_name_facebook,$upload_unique_facebook_id,$upload_text,$upload_image_address,$upload_video_address,$user_unique_facebook_id);

$stmt->execute();



if ($stmt->error) {
	http_response_code(503);//throw a 503 Service Unavailable on error
	echo json_encode("Could not execute query");
	error_log('mysql: '.$stmt->error);
	exit();
}

$stmt->close();

//no data to retreive

echo '{"success": true}';
?>