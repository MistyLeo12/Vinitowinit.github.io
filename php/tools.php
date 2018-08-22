<?php


function db_connect($config_object) {
	
	//upack values
	$mysql_host = $config_object["mysql_host"];
	$mysql_database = $config_object["mysql_database"];
	$mysql_user = $config_object["mysql_user"];
	$mysql_password = $config_object["mysql_password"];
	
	// Make connection. Make sure you are logged in with username: root, password: None. Also there should be a database named 'mygpsdatabase'
	$connect = mysqli_connect($mysql_host,$mysql_user,$mysql_password,$mysql_database);
	
	// Check if connection is good
	if(mysqli_connect_errno($connect))
	{
		#echo "Failed to connect to MySQL: " . mysqli_connect_error();
		echo 'could not connect to the database';
	}
	else
	{
		echo "Success. You reached the FMC Server!";
	}
	
	// return the variable
	return $connect;
	
}

// this function is design to turn a 2d array into xml string array has N sub arrays which contain actual values indexed potentially by strings
// it should be able to take output of get_array_from_mysql_result as an $array input
function result_to_xml($result, $root_name, $basic_element_name) {
	
	$string = '<' . $root_name . '>';
	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		$subarray = mysqli_fetch_array($result,$resulttype = MYSQLI_ASSOC);
	//foreach ($array as $subarray) {
		//echo " sub: ";
		//print_r($subarray);
		$string = $string . '<' . $basic_element_name . '>';
		foreach ($subarray as $key =>$value) {
			$string = $string . '<' . $key . '>' . $value . '</' . $key . '>';
		}
		$string = $string . '</' . $basic_element_name . '>';
	}
	$string = $string . '</' . $root_name . '>';

	return $string;
}

// this function is design to turn a 2d array into xml string array has N sub arrays which contain actual values indexed potentially by strings
// it should be able to take output of get_array_from_mysql_result as an $array input
function array_to_xml($array, $root_name, $basic_element_name) {
    $string = '<' . $root_name . '>';
    foreach ($array as $subarray) {
        //echo " sub: ";
        //print_r($subarray);
        $string = $string . '<' . $basic_element_name . '>';
        foreach ($subarray as $key =>$value) {
            $string = $string . '<' . $key . '>' . $value . '</' . $key . '>';
        }
        $string = $string . '</' . $basic_element_name . '>';
    }
    $string = $string . '</' . $root_name . '>';

    return $string;
}

function get_array_from_mysql_result($result) {
    $array = array();
    while ($row = mysql_fetch_assoc($result)) {
        $subarray = array();
        foreach ($row as $key => $value)
            $subarray[$key] = $value;
        $array[] = $subarray;
    }    
    
    return $array;
}

function get_array_from_mysqli_result($result) {
	$array = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$subarray = array();
		foreach ($row as $key => $value)
			$subarray[$key] = $value;
		$array[] = $subarray;
	}

	return $array;
}

function array_to_sql_insert_str($array,$table_name) {
	
	$query_str_names = "INSERT INTO $table_name (";
	$query_str_values = "VALUES (";
	foreach ($array as $key => $value){
		$query_str_names = $query_str_names . $key .',';
		$query_str_values = $query_str_values . "'$value'" .',';
	}
	$query_str_names = substr($query_str_names, 0, -1);
	$query_str_values = substr($query_str_values, 0, -1);
	$query_str = $query_str_names . ') ' . $query_str_values . ')';
	return $query_str;
}

// INSERT ONLY IF IT DOESN'T EXIST
function array_to_sql_insert_str_if_unique($array,$table_name,$property_name_for_uniqueness) {

	$property_value = $array[$property_name_for_uniqueness];
	$query_str_names = "INSERT INTO $table_name (";
	$query_str_values = "SELECT ";
	foreach ($array as $key => $value){
		$query_str_names = $query_str_names . $key .',';
		$query_str_values = $query_str_values . "'$value'" .',';
	}
	$query_str_names = substr($query_str_names, 0, -1);
	$query_str_values = substr($query_str_values, 0, -1);
	$query_str = $query_str_names . ') ' . $query_str_values . "  FROM dual WHERE NOT EXISTS 
			(SELECT * FROM $table_name WHERE $property_name_for_uniqueness = '$property_value')";
	return $query_str;
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




?>
