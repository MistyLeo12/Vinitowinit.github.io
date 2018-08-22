<?php 
header('Content-Type: application/json');
require_once("config_fmc.php");
require_once("db_connect.php");
require_once("fbAdminCheck.php");
//$db variable should be available now

//no input parameters

$result = $db->query("SELECT * FROM fmc_departments WHERE is_deleted=0");

if (!$result) {
	http_response_code(503);//throw a 503 Service Unavailable on error
	echo json_encode("Could not execute query");
	exit();
}

while($row = $result->fetch_assoc()) {
	$final[] = $row;
}

$result->free();

echo json_encode($final);

?>