<?php 

$mysql_host = "localhost";
$mysql_database = "fmc";
$mysql_user = "root";
$mysql_password = "password";

$connect = mysqli_connect($mysql_host,$mysql_user,$mysql_password,$mysql_database);

if(mysqli_connect_errno($connect)) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	echo 'could not connect to the database';
}
?>
