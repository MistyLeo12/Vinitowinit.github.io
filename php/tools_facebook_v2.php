<?php

require_once( 'Facebook/FacebookSession.php' );
require_once( 'Facebook/FacebookRedirectLoginHelper.php' );
require_once( 'Facebook/FacebookRequest.php' );
require_once( 'Facebook/FacebookResponse.php' );
require_once( 'Facebook/GraphObject.php' );
require_once( 'Facebook/GraphSessionInfo.php' );

require_once( 'Facebook/FacebookSDKException.php' );
require_once( 'Facebook/FacebookRequestException.php' );
require_once( 'Facebook/FacebookAuthorizationException.php' );
require_once( 'Facebook/FacebookPermissionException.php' );
require_once( 'Facebook/FacebookOtherException.php' );
require_once( 'Facebook/FacebookServerException.php' );
require_once( 'Facebook/FacebookClientException.php' );
require_once( 'Facebook/FacebookThrottleException.php' );

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;

use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\FacebookPermissionException;
use Facebook\FacebookOtherException;
use Facebook\FacebookServerException;
use Facebook\FacebookClientException;
use Facebook\FacebookThrottleException;

class classFacebookFmc
{
	
	//Will hold the sesion 
	public $session = null;
	public $appAccessToken = null;
	//Fixed values
	private $appId = '670916569623587';
	private $appSecret = '34de18c63b1ea2574d19120c2897874f';
	
	//private $fmc_page_id = '1920852034722181'; // test_fmc page
	//private $fmc_page_id = '713011352123023'; // test user fmc page
	private $fmc_page_id = null; 
	
	//private $fmc_page_id = null;
	//private $user_access_token = "CAAJiMeZCObCMBAPZBSKYBOvVjjk7y3OYDyKOTkkOrGBjTN9ENjtev6RZBwuVVHwnKOw8f0zfotee05NDOuWjuwp8oZBMAZBZCpK6HNbftuRZBZCoTSbEia6M5XyvxWE9nHxINsuN1yPWwY5ZABV9B3S8ZAojShU1O3hSqj0GLZCgVKMXNlBDbPwqc8HANjZBYxqLwmrTdxxQkIGIUA30I29w3M6T";
	
	/*
	private $test_user_access_token = "CAAJiMeZCObCMBAF017pDjbpKBuZAaaQmph9ExUKp4xKdfqrDKNG2VJKJ7fa1UOh8ty8dKMifLe9tquDVELYAR5UaaySUQvYZAR9tUbnDludjFPYcLKABkLzklIZCheyOxPlYmwacksAxVQSTL0G3IapFsxpcrlZCWSZCp3ml9VWV1vyx0rZAZCB9W20DbuyW3khmDC2EMcm18vs267n7gy68";
	private $test_user_id = "100006403969442";
	private $test_queryStr = '/1920852034722181/feed?access_token=CAAJiMeZCObCMBAF017pDjbpKBuZAaaQmph9ExUKp4xKdfqrDKNG2VJKJ7fa1UOh8ty8dKMifLe9tquDVELYAR5UaaySUQvYZAR9tUbnDludjFPYcLKABkLzklIZCheyOxPlYmwacksAxVQSTL0G3IapFsxpcrlZCWSZCp3ml9VWV1vyx0rZAZCB9W20DbuyW3khmDC2EMcm18vs267n7gy68&message=hello world';
	
	private $test_appId = '711457678902809';
	private $test_appSecret = '34de18c63b1ea2574d19120c2897874f';
	private $fmc_page_id = '1920852034722181';
	*/
	
	
	function __construct($id_of_upload_facebook_page) 
	{
		//$id_of_upload_facebook_page
		$this->fmc_page_id = $id_of_upload_facebook_page;
		$x=0; 
	}
		
	public function set_page($page_id)
	{
		$this->fmc_page_id = $page_id;
	}
	
	public function startUserSession($user_access_token)
	{
		session_start();
		FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
		$session = new FacebookSession($user_access_token);
		$this->session = $session;
	}
	
	public function startAppSession()
	{
		session_start();
		FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
		$this->session = FacebookSession::newAppSession();
	}
	
	public function getAppAccessToken()
	{
		//Use the app to get an access token to read the page wall
		$queryStr = "/oauth/access_token?client_id=$this->appId&client_secret=$this->appSecret&grant_type=client_credentials";
		$request = new FacebookRequest($this->session, 'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		$this->appAccessToken = $d['access_token'];
	}
	
	
	public function postImageToFmcWall($user_access_token,$upload_message,$upload_image_data)
	{
		//Check if session is set.  If it isn't then create one?
		$this->startUserSession($user_access_token);

		//Check if session is set.  If it isn't then create one?
		//$this->startUserSession($user_access_token_facebook);
		$image_url = $upload_image_data;  // fyi, this is the actual address of the image
		
		//$image_url = 'http://campuscompanion.co/uploads/48_attachment_image.jpg';
		
		
		if($this->session) {
		/*
			try {
				$response = (new FacebookRequest(
						$this->session, 'POST', "/me/photos", array(
						//$this->session, 'POST', "/$this->fmc_page_id/feed", array(
								'message' => "$upload_message",
								'source' => "@".$upload_image_data
						)
				))->execute()->getGraphObject();
		
						$upload_image_facebook_id = $response->getProperty('id');
						echo "Posted with id: " . $response->getProperty('id');
			} catch(FacebookRequestException $e) {
				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
			}
			*/
			echo $image_url;
			
			try {
				$response = (new FacebookRequest(
						$this->session, 'POST', "/$this->fmc_page_id/feed", array(
								'message' => "$upload_message",
								//'object_attachment' => "$upload_image_facebook_id"
								'link' => "$image_url"
						)
				))->execute()->getGraphObject();
			
						$post_id = $response->getProperty('id');
						echo "Posted with id: " . $response->getProperty('id');
			} catch(FacebookRequestException $e) {
				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
			}
			
		}
		// READ DETAILS OF NEW POST
		$queryStr = "/$post_id/";
		$request = new FacebookRequest($this->session, 'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		//$new_facebook_posts_arr = array();
		//foreach ($d['data'] as $value)
		// Create array of info about new post
		$post_item = array();
		$post_item['upload_unique_facebook_id'] = $post_id;
		$post_item['user_unique_facebook_id'] = $d['from']->id;
		$post_item['user_name_facebook'] = $d['from']->name;  //$d['data'][0]->from->name
		$post_item['upload_timestamp'] = strtotime($d['created_time']);
		$post_item['upload_text'] = $d['message'];
		$post_item['upload_origin'] = 2;
		// Now add to the database
		return $post_item;
		
	}
	
	
	public function postToFmcWall($user_access_token,$upload_message)
	{
		//Check if session is set.  If it isn't then create one?
		$this->startUserSession($user_access_token);
		
		if($this->session) {
		
			try {
				$response = (new FacebookRequest(
						$this->session, 'POST', "/$this->fmc_page_id/feed", array(
								'message' => "$upload_message",
								//'source' => new CURLFile('http://localhost:92/fmc_web/uploads/71_attachment_image.jpg', 'image/jpg'),
								//'object_attachment' => $photo_id
						)
				))->execute()->getGraphObject();
						
				$post_id = $response->getProperty('id');
				
						echo "Posted with id: " . $response->getProperty('id');
			} catch(FacebookRequestException $e) {
				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
			}
		}
		//$xx = $response->asArray();
		//echo var_dump($xx);
		
		// READ DETAILS OF NEW POST
		$queryStr = "/$post_id/";
		$request = new FacebookRequest($this->session, 'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		//$new_facebook_posts_arr = array();
		//foreach ($d['data'] as $value)
		// Create array of info about new post
		$post_item = array();
		$post_item['upload_unique_facebook_id'] = $post_id;
		$post_item['user_unique_facebook_id'] = $d['from']->id;
		$post_item['user_name_facebook'] = $d['from']->name;  //$d['data'][0]->from->name
		$post_item['upload_timestamp'] = strtotime($d['created_time']);
		$post_item['upload_text'] = $d['message'];
		$post_item['upload_origin'] = 2;
		// Now add to the database
		return $post_item;
		
		
	}
	
	public function getNewFmcWallPosts($date_of_most_recent_db_item)
	{
		// start an app session
		$this->startAppSession();
		
		//Get app access token 
		$this->getAppAccessToken();
		
		//I got this access token from "Graph API explorer" GUI.  Search it in google
		//$access_token = 'CAACEdEose0cBAF8aYRRIrTSrGJcqXo16VBnw6eIV0WT5BCPQP8t4Q0fwaWzMtBZCtU6KMgrGJYt7BSiAuEvjOwH07BKOZB7YBPvnl7JXaWqhMahbLvB4HirO6wdEPaLbZAbhUpzjqVdkEFpdJ3ZCfZCWaouZBH8rNGevP0vYcojTpztehwBL0H7MFHV1xFduZBZCvVs5XvFgoSBxKQOhF86G';
		//$access_token = '670916569623587|VNS-l66r7IE-gBBr_KOtrt8YQEw';
		$queryStr = "/$this->fmc_page_id/feed?access_token=$this->appAccessToken";
		$request = new FacebookRequest($this->session, 'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		$new_facebook_posts_arr = array();
		foreach ($d['data'] as $value)
		{
			if (strtotime($value->created_time)>$date_of_most_recent_db_item)
			{
				$post_item = array();
				$post_item['upload_unique_facebook_id'] = $value->id;
				$post_item['user_unique_facebook_id'] = $value->from->id;
				$post_item['user_name_facebook'] = $value->from->name;  //$d['data'][0]->from->name
				$post_item['upload_timestamp'] = strtotime($value->created_time);
				$post_item['upload_text'] = $value->message;
				$post_item['upload_origin'] = 3;
				$post_item['upload_image_url'] = $value->picture; 
				array_push($new_facebook_posts_arr,$post_item); //add item to facebook posts list
			 // Add the item to the items to add
			}
		}
		return $new_facebook_posts_arr;
		
		//	$upload_directory = get_server_uploads_directory();
	}
	
	
} // End of object


?>
