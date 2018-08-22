<?php 
header('Content-Type: application/json');
require_once("config_fmc.php");
require_once("db_connect.php");
require_once("fbAdminCheck.php");
//$db variable should be available now

//make sure to protect against injection
$r = json_decode($_POST['req']);
foreach ($r as &$val) {
	if ($val == null) {
		$val = "";
	}
}
$stmt = $db->prepare('UPDATE table_main SET
	upload_timestamp=?, user_name_facebook=?, user_location_x=?, user_location_y=?, upload_origin=?, upload_unique_facebook_id=?,
	upload_text=?, upload_image_address=?, upload_audio_address=?, upload_video_address=?, fmc_member_assigned=?,
	fmc_project_status=?, fmc_department=?, user_access_token_facebook=?, isDeleted=?, isAnonymous=?, user_unique_facebook_id=?
	WHERE upload_unique_id=?');
error_log($db->error);
$stmt->bind_param("ssssssssssssssssss",$r->upload_timestamp,$r->user_name_facebook,$r->user_location_x,$r->user_location_y,$r->upload_origin,$r->upload_unique_facebook_id,
	$r->upload_text,$r->pload_image_address,$r->upload_audio_address,$r->upload_video_address,$r->fmc_member_assigned,
	$r->fmc_project_status,$r->fmc_department,$r->user_access_token_facebook,$r->isDeleted,$r->isAnonymous,$r->user_unique_facebook_id,
	$r->upload_unique_id);

$stmt->execute();



if ($stmt->error) {
	http_response_code(503);//throw a 503 Service Unavailable on error
	echo json_encode("Could not execute query");
	exit();
}

$stmt->close();

//no data to retreive

echo '{"success": true}';
?>