<?php 
header('Content-Type: application/json');
require_once("config_fmc.php");
require_once("db_connect.php");
require_once("fbAdminCheck.php");
//$db variable should be available now


//Note finite state for inputs, removing necessity of binding variables while protecting against injection
$limit = intval($_REQUEST['limit']) > 0 ? intval($_REQUEST['limit']) : 99999999;
$sort = $_REQUEST['sort'];
switch ($sort) {
 	case 'recent':
 	default:
 		$order_by = 'upload_timestamp';
 		$order_direction = 'DESC';
}

$result = $db->query("SELECT * FROM table_main WHERE isDeleted=0 ORDER BY $order_by $order_direction LIMIT $limit");

if (!$result) {
	http_response_code(503);//throw a 503 Service Unavailable on error
	echo json_encode("Could not execute query. ".$db->error);
	exit();
}

while($row = $result->fetch_assoc()) {
	$final[] = $row;
}

$result->free();

echo json_encode($final);

?>