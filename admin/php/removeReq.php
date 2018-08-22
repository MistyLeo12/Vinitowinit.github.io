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
$stmt = $db->prepare('UPDATE table_main SET isDeleted=1 WHERE upload_unique_id=?');
error_log($db->error);
$stmt->bind_param("s",$r->upload_unique_id);

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