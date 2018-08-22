<?php 
header('Content-Type: application/json');
require_once("config_fmc.php");
require_once("db_connect.php");
require_once("fbAdminCheck.php");
//$db variable should be available now

$id = intval($_REQUEST['id']);
//Note finite state for inputs, removing necessity of binding variables while protecting against injection


$result = $db->query("SELECT * FROM table_notes WHERE upload_unique_id=".$id);


if (!$result) {
	http_response_code(503);//throw a 503 Service Unavailable on error
	echo json_encode("Could not execute query. ".$db->error);
	exit();
}

while($row = $result->fetch_assoc()) {
	$final[] = $row;
}

$result->free();


if (!$final) {echo '[]';exit();}
echo json_encode($final);

?>