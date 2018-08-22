<?php 

//$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$server_base_url = $_SERVER['HTTP_HOST'];
$doc_root = $_SERVER['DOCUMENT_ROOT'];

/*
	if ($_SERVER['DOCUMENT_ROOT']=='C:/xampp/htdocs'){
		$mysql_host = "localhost";
		$mysql_database = "fmc";
		$mysql_user = "root";
		$mysql_password = "";
*/


switch ($server_base_url) {
	case "campuscompanion.co":
	case "www.campuscompanion.co":
		echo "campuscompanion server detected";
		$array_config = array(
				"mysql_host" => "localhost",
				"mysql_database" => "dbCC",
				"mysql_user" => "user",
				"mysql_password" => "Sspaciss123!",
				"uploads_directory" => "www.campuscompanion.co//uploads//",
				"path_root" => "www.campuscompanion.co//uploads//",
				//"path_image_uploads" => "//home//jmmalo03//public_html//uploads//"
				"path_image_uploads" => $doc_root . "//uploads//",
				//"facebook_fmc_admin_group_id" => '1920852034722181'  /* fmc_test2 page */
				"facebook_fmc_admin_group_id" => '347519535355326'   /*Fix My Campus page*/
		);	
		break;
	case "localhost:92":
	case "localhost:90":
	case "localhost":
		echo "localhost server detected";
		$array_config = array(
				"mysql_host" => "localhost",
				"mysql_database" => "fmc",
				"mysql_user" => "root",
				"mysql_password" => "password",
				"uploads_directory" => "localhost//uploads//",
				"path_root" => "C://xampp//htdocs//fmc_web/uploads//",
				"path_image_uploads" => "C://xampp//htdocs//fmc_web//localhost//uploads//",
				"facebook_fmc_admin_group_id" => '1920852034722181'  /* fmc_test2 page */
				//"facebook_fmc_admin_group_id" => '347519535355326'   /*Fix My Campus page*/
		);
		break;
	case "www.fmc.colab.duke.edu":
	case "fmc.colab.duke.edu":
	case "www.fixmycampus.colab.duke.edu":
	case "fixmycampus.colab.duke.edu":
	case "colab-sbx-209.oit.duke.edu":
	case "www.colab-sbx-209.oit.duke.edu":
		echo "co-lab server detected";
		$array_config = array(
				"mysql_host" => "localhost",
				"mysql_database" => "fmc",
				"mysql_user" => "root",
				"mysql_password" => "password",//hapLer2ind
				"uploads_directory" => "colab-sbx-209.oit.duke.edu//uploads//",
				"path_root" => "colab-sbx-209.oit.duke.edu//uploads//",
				//"uploads_directory" => "www.fmc.colab.duke.edu//uploads//",
				//"path_root" => "www.fmc.colab.duke.edu//uploads//",
				//"path_image_uploads" => "//home//bitnami//opt//bitnami//apache2//htdocs//uploads//"
				//"path_image_uploads" => "www.colab-sbx-209.oit.duke.edu//uploads//"
				//"path_image_uploads" => "//opt//bitnami//apache2//htdocs//uploads//"
				"path_image_uploads" => $doc_root . "//uploads//",
				//"facebook_fmc_admin_group_id" => '1920852034722181'  /* fmc_test2 page */
				"facebook_fmc_admin_group_id" => '347519535355326'   /*Fix My Campus page*/
				
		);
		break;
	default:
		$format = 'Cant find matching URL for server in config function.  \n Detected server is called %s';
		echo sprintf($format, $server_base_url);
		$array_config = array();
}


// This stays the same regardless of server or directory
$array_config['app_id'] = '670916569623587';
$array_config['app_secret'] = '34de18c63b1ea2574d19120c2897874f';


/*
 * DECIDE HERE WHICH PAGE TO POINT THE APPLICATION AT
 */
$useTestPage = 0;

if ($useTestPage==1){
	// Currently points to 'fmc_test' page, used for testing our application
	//$array_config['facebook_group_page_id'] = '1920852034722181'; // old fmc page - open group outside duke
	$array_config['facebook_group_page_id'] = '703437566430186'; // old fmc page - open group within duke
} else {
	// Currently points to live 'Fix My Campus' group on facebook
	//$array_config['facebook_group_page_id'] = '541134402572421';
	$array_config['facebook_group_page_id'] = '347519535355326';
}


?>