<?php 
header('Content-Type: application/json');
require_once("config_fmc.php");
require_once("db_connect.php");
require_once("fbAdminCheck.php");
//$db variable should be available now

//make sure to protect against injection
$mem = json_decode($_POST['member']);
foreach ($r as &$val) {
	if ($val == null) {
		$val = "";
	}
}
$stmt = $db->prepare('UPDATE fmc_members SET is_deleted=1 WHERE member_id=?');

$stmt->bind_param("s",$mem->member_id);

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