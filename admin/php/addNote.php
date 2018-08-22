<?php 
header('Content-Type: application/json');
require_once("config_fmc.php");
require_once("db_connect.php");
require_once("fbAdminCheck.php");
//$db variable should be available now

//make sure to protect against injection
$note = json_decode($_POST['note']);


$stmt = $db->prepare('INSERT INTO table_notes (upload_unique_id,note,note_timestamp) VALUES (?,?,?)');

$stmt->bind_param("iss",$note->upload_unique_id,$note->note,$note->note_timestamp);

$stmt->execute();



if ($stmt->error) {
	http_response_code(503);//throw a 503 Service Unavailable on error
	echo json_encode("Could not execute query. ".$stmt->error.print_r($note,true));
	error_log("Could not execute query. ".$stmt->error);
	exit();
}

$stmt->close();

//no data to retreive

echo '{"success": true}';
?>