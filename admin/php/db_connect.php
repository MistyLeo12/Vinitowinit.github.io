<?php 

$mysql_host = "localhost";
$mysql_database = "fmc";
$mysql_user = "root";
$mysql_password = "password";

$db = new mysqli($mysql_host,$mysql_user,$mysql_password,$mysql_database);

if($db->connect_error) {
	http_response_code(503);
	echo json_encode("DB Connect error: ".$db->connect_error);
}
?>