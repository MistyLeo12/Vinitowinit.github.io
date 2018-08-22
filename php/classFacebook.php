<?php

/**
 * This class is used for (mostly) generic interactions with the 
 *  Facebook API.  It requires that all the FACEBOOK helper classes
 *  are available in a directory called 'FACEBOOK' so that they 
 *  can be included with the 'require_once' statement.  Was designed to work 
 *  with the Facebook API v2.0. 
 *  
 * @author Jordan Malof, jmmalo03@gmail.com, 11/09/2014
 * @version 5.0
 * @abstract This version adds several new methods:
 * 	
 */

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
	public $facebookQueryResultLengthLimit = '15';
	
	//Fixed values
	private $appId = null;
	private $appSecret = null;
	private $group_page_id = null;
	// fmc_test page
	//private $appId = '670916569623587';
	//private $appSecret = '34de18c63b1ea2574d19120c2897874f';
	
	
	function __construct($config_object) 
	{
		
		// sets the app id
		$this->appId = $config_object['app_id'];
		$this->appSecret = $config_object['app_secret'];
		
		// Sets the group page with which we wish to interact
		$this->group_page_id = $config_object['facebook_group_page_id'];
		
		// Sets the id of the group which fmc admin members must join 
		// to be able to login to the admin page and view request
		$this->admin_page_id = $config_object['facebook_fmc_admin_group_id'];
		
		$x=0; 
	}
		
	public function set_page($page_id)
	{
		$this->group_page_id = $page_id;
	}
	
	public function getMembersOfFacebookGroup($user_access_token,$isAdminOnly){
		// $user_access_token is an access token provided by 
		// a member of the group that we wish to query about.  The access token
		// must also have 'user_groups' persmissions
		// $isAdminOnly is binary variable indicating whether to return 
		// all members (false) or just administrators (true)
		
		session_start();
		FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
		$session = new FacebookSession($user_access_token);
		$this->session = $session;
		
		
		// CHECK IF THE USER IS A MEMBER FO THE FMC ADMIN GROUP
		$queryStr = "/".$this->admin_page_id."/members";
		//$queryStr = "/me/groups";
		$request = new FacebookRequest($this->session,'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		$responseData = $d['data'];
		
		$groupMembers = array();
		foreach ($d['data'] as $value)
		{
			if ($value->administrator || !$isAdminOnly){
				$post_item = array();
				$post_item['member'] = $value->name;
				$post_item['member_id'] = $value->id;
				$post_item['is_deleted'] = '0';
				array_push($groupMembers,$post_item); //add item to facebook posts list
			}
				// Add the item to the items to add
		}
		return $groupMembers;
		
	}
	
	public function checkIfFacebookAccessTokenIsValid($user_access_token){
		session_start();
		FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
		$session = new FacebookSession($user_access_token);
		
		$isValidToken = false;
		// To validate the session:
		try {
			$session->validate();
			$isValidToken=true;
			echo 'facebook access token is valid';
		} catch (FacebookRequestException $ex) {
			// Session not valid, Graph API returned an exception with the reason.
			echo 'Not a valid access token';
			echo $ex->getMessage();
		} catch (\Exception $ex) {
			// Graph API returned info, but it may mismatch the current app or have expired.
			echo 'Graph API returned info, but it may mismatch the current app or have expired';
			echo $ex->getMessage();
		}
		// return
		$this->session = $session;
		return $isValidToken;
		
	}
	
	
	
	public function checkAdminAccessToken($user_access_token){

		
		//if access token is bad, return, else start session and continue
		if (!$this->checkIfFacebookAccessTokenIsValid($user_access_token)){
			return;
		}
		
		// CHECK IF THE USER IS A MEMBER FO THE FMC ADMIN GROUP
		$queryStr = "/me/groups";
		$request = new FacebookRequest($this->session, 'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		$responseData = $d['data'];
		foreach ($responseData as $key => $value){
			$group_name = $value->name;
			$group_id = $value->id;
			$isAdmin = $value->administrator;
			if (strcmp($this->admin_page_id,$group_id)==0 && isAdmin) {
				$isValidToken=true;
				break;
			}
		}
		// Check if the user was detected as a member of the admin group
		if ($isValidToken==false){
			echo 'Valid user token but user is not a member of the fmc admin group';
			return $isValidToken;
		}
		
		return $isValidToken;
		
		
	}
	

	public function checkIfUserIsAppAdministrator($user_access_token){
		// Returns a binary indicator variable indicating if the facebook user
		//  with access token $user_access_token has a valid access token, AND is 
		//  an FMC app administrator who  should have access to page info
		
		//if access token is bad, return
		// THIS WILL START A SESSION
		if (!$this->checkIfFacebookAccessTokenIsValid($user_access_token)){
			return;
		}
		
		//get user id for access token
		//need this to search for users of this app
		$queryStr = "/me";
		$request = new FacebookRequest($this->session, 'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		$user_facebook_unique_id = $d['id'];
		//echo 'GETTING USER ID';
		//$user_facebook_unique_id = $d['data']->id;
		 
		//foreach ($responseData as $key => $value){
			//$user_facebook_unique_id = $value->id;
		//}
		
		// START APP SESSION, AND CHECK IF USER IS AN APP ADMINISTRATOR 
		$this->startAppSession();
		$isFmcAdministrator=false;
		// CHECK IF THE USER IS A MEMBER FO THE FMC ADMIN GROUP
		$appId = $this->appId;
		$queryStr = "/$appId/roles";
		$request = new FacebookRequest($this->session, 'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		$responseData = $d['data'];
		foreach ($responseData as $key => $value){
			$user_id = $value->user;
			$role = $value->role;
			if (strcmp($role,'administrators')!=0 && strcmp($user_id,$user_facebook_unique_id)!=0) {
				$isFmcAdministrator=true;
				break;
			}
		}
			
		// Check if the user was detected as a member of the admin group
		if ($isValidToken==false){
			echo 'Valid facebook user token but user is not an FMC App administrator.  
					Add user as an app administrator in roles panel in app settings';
			return $isFmcAdministrator;
		}
	
		return $isFmcAdministrator;
	
	
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
		/**
		 * This starts a facebook session with the current app.  Most operations
		 * require starting a session
		 */
		session_start();
		FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
		$this->session = FacebookSession::newAppSession();
	}
	
	public function getAppAccessToken()
	{
		/**
		 * Get a facebook access token for the app.  This allows basic operations
		 * on the facebook open graph, such as reading basic information of users 
		 *  or changing settings for the app (I think)
		 */
		
		//Use the app to get an access token to read the page wall
		$queryStr = "/oauth/access_token?client_id=$this->appId&client_secret=$this->appSecret&grant_type=client_credentials";
		$request = new FacebookRequest($this->session, 'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		$this->appAccessToken = $d['access_token'];
	}
	
	
	public function postMessageAndImageToGroupWall($user_access_token,$upload_message,$upload_image_data)
	{
		
		/**
		 * Posts a message (text only) to the group wall on behalf of a user
		 * that is identified by the string $user_access_token.  The users message
		 * is contained in the string in $upload_message.  This also attaches a link
		 * to the posted message that (presumably) links to any media (e.g., an image).  
		 * The URL of the media is contained as a string in $upload_image_data
		 */
		
		//Check if session is set.  If it isn't then create one?
		$this->startUserSession($user_access_token);
		
		// mention media is attached
//		$upload_message = sprintf('%s
//%s',$upload_message," [sent via FMC mobile app]");

		//Check if session is set.  If it isn't then create one?
		//$this->startUserSession($user_access_token_facebook);
		$image_url = $upload_image_data;  // fyi, this is the actual address of the image
		
		echo 'image link
				';
		echo $image_url;
		echo ' 
				';
		
		if($this->session) {
			echo $image_url;
			
			try {
				$response = (new FacebookRequest(
						$this->session, 'POST', "/$this->group_page_id/feed", array(
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
	
	
	public function postMessageToGroupWall($user_access_token,$upload_message)
	{
		/**
		 * Posts a message (text only) to the group wall on behalf of a user
		 * that is identified by the string $user_access_token.  The users message
		 * is contained in the string in $upload_message.   
		 */
		
		//Check if session is set.  If it isn't then create one?
		$this->startUserSession($user_access_token);
		
		//Modify the string
		//$upload_message = sprintf('%s \n %s',$upload_message,"[sent via FMC mobile app]");
		
		if($this->session) {
		
			try {
				$response = (new FacebookRequest(
						$this->session, 'POST', "/$this->group_page_id/feed", array(
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
	
	public function getNewGroupWallPosts($date_of_most_recent_db_item,$user_access_token)
	{
		/**
		 * Gets any posts made to the group facebook wall since 
		 * the date $date_of_most_recent_db_item.  This variable is a 
		 * date number indicating the number of seconds since 1979?
		 */
		
		echo 'Access token is   
				';
		echo $user_access_token;
		echo '
				';
		$this->startUserSession($user_access_token);
		
		/*
		// start an app session
		$this->startAppSession();
		
		//Get app access token 
		$this->getAppAccessToken();
		*/
		$queryStr = "/$this->group_page_id/feed?limit=" . $this->facebookQueryResultLengthLimit ."&access_token=$user_access_token";
	     //$queryStr = "/$this->group_page_id/feed?limit=$this->facebookQueryResultLengthLimit&access_token=$user_access_token";
		//$queryStr = "/$this->group_page_id/feed?access_token=$user_access_token";
		//$queryStr = "/$this->group_page_id/feed?access_token=$this->appAccessToken";
		$request = new FacebookRequest($this->session, 'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		
		// Set the retrieval limit to 25 in case it has previously been set very large
		$objFb->facebookQueryResultLengthLimit = '15';
		
		/*
		//I got this access token from "Graph API explorer" GUI.  Search it in google
		//$access_token = 'CAACEdEose0cBAF8aYRRIrTSrGJcqXo16VBnw6eIV0WT5BCPQP8t4Q0fwaWzMtBZCtU6KMgrGJYt7BSiAuEvjOwH07BKOZB7YBPvnl7JXaWqhMahbLvB4HirO6wdEPaLbZAbhUpzjqVdkEFpdJ3ZCfZCWaouZBH8rNGevP0vYcojTpztehwBL0H7MFHV1xFduZBZCvVs5XvFgoSBxKQOhF86G';
		//$access_token = '670916569623587|VNS-l66r7IE-gBBr_KOtrt8YQEw';
		$queryStr = "/$this->group_page_id/feed?access_token=$this->appAccessToken";
		$request = new FacebookRequest($this->session, 'GET',$queryStr);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$d = $graphObject->asArray();
		*/
		
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
				// CLEAN TEXT FOR ANY SPECIAL CHARACTERS
				$post_item['upload_text'] = mysql_real_escape_string($value->message);
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
