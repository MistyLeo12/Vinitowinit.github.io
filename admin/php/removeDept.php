<?php 
header('Content-Type: application/json');
require_once("config_fmc.php");
require_once("db_connect.php");
require_once("fbAdminCheck.php");
//$db variable should be available now

//make sure to protect against injection
$dept = json_decode($_POST['dept']);


$stmt = $db->prepare("UPDATE fmc_departments SET is_deleted=1 WHERE unique_id=?");
$stmt->bind_param("s",$dept->unique_id);

$stmt->execute();



if ($stmt->error) {
	http_response_code(503);//throw a 503 Service Unavailable on error
	echo json_encode("Could not execute query");
	exit();
}

//no data to retreive

echo '{"success": true}';
?>