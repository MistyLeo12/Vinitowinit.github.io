<?php

require_once("config_fmc.php");

require_once( 'Facebook/Entities/AccessToken.php');
require_once( 'Facebook/HttpClients/FacebookStream.php');
require_once( 'Facebook/HttpClients/FacebookHttpable.php');
require_once( 'Facebook/HttpClients/FacebookStreamHttpClient.php');
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

$appSecret = $array_config['app_secret'];
$appId = $array_config['app_id'];
$at = $_REQUEST['access_token'];

session_start();
FacebookSession::setDefaultApplication($appId, $appSecret);
$session = new FacebookSession($at);
		
// To validate the session:
try {
	$session->validate();
} catch (FacebookRequestException $ex) {
	// Session not valid, Graph API returned an exception with the reason.
	http_response_code(403);
	echo json_encode("Not a valid FB session.");
	exit();
} catch (\Exception $ex) {
	// Graph API returned info, but it may mismatch the current app or have expired.
	http_response_code(500);
	echo json_encode("Error running facebook API on the backend.");
	exit();
}

?>
