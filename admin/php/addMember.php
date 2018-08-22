<?php 
header('Content-Type: application/json');
require_once("config_fmc.php");
require_once("db_connect.php");
require_once("fbAdminCheck.php");
//$db variable should be available now

//make sure to protect against injection
$mem = json_decode($_POST['member']);


$stmt = $db->prepare('INSERT INTO fmc_members (member_id,member,is_deleted) VALUES (?,?,0)');
$stmt->bind_param("ss",$mem->member_id,$mem->member);

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