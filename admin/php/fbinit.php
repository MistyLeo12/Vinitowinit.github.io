<?php 
	error_log("---------------------------------");

	require_once("config_fmc.php");
	//Wait is this really how this is supposed to be done?
	//Seriously...
	//I can't find anything to show otherwise... Facebook your documentation looks beautiful, but it is pretty unhelpful...
	//I'm just praying for no cyclic dependencies
	require_once("Facebook/HttpClients/FacebookStream.php");
	require_once("Facebook/HttpClients/FacebookHttpable.php");
	require_once("Facebook/HttpClients/FacebookStreamHttpClient.php");
	require_once("Facebook/Entities/AccessToken.php");
	require_once("Facebook/FacebookRequest.php");
	require_once("Facebook/FacebookSession.php");
	require_once("Facebook/FacebookSDKException.php");
	require_once("Facebook/FacebookRequestException.php");
	require_once("Facebook/FacebookAuthorizationException.php");
	require_once("Facebook/FacebookRedirectLoginHelper.php");
	require_once("Facebook/GraphObject.php");
	require_once("Facebook/GraphUser.php");

	use Facebook\FacebookRequest;
	use Facebook\GraphUser;
	use Facebook\FacebookRequestException;
	use Facebook\FacebookSession;
	use Facebook\FacebookRedirectLoginHelper;

	session_start();//start up PHP session
	

	//Set up Facebook PHP Session
	FacebookSession::setDefaultApplication($array_config['app_id'],$array_config['app_secret']);
	error_log("http://".$_SERVER['HTTP_HOST'].explode("?",$_SERVER['REQUEST_URI'])[0].'/');
	$helper = new FacebookRedirectLoginHelper("http://".$_SERVER['HTTP_HOST'].explode("?",$_SERVER['REQUEST_URI'])[0].'/');
	
	try {//Check if the user has just logged in, attempt to get their session.
  		$session = $helper->getSessionFromRedirect();
  		//print_r($session);
  	} catch(Exception $e) {
  		//print_r($e);
  	}//do nothing on fb error, we'll just redirect them to the login when there's no session found
	
	if (!$session) {
			error_log("no session");
			//$helper = new Facebook\FacebookRedirectLoginHelper();
			$loginUrl = $helper->getLoginUrl();//set the redirect back here again once the user is finished logging in
			if ($_SESSION['hits']++%2==0) {header('Location: '.$loginUrl);} else {print_r($e);}//and send the user on their way.
			exit();
	}

	try {
			$user_profile = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
			print_r($user_profile);
	} catch(FacebookRequestException $e) {
			echo "ERROR";
			print_r($e);
	}





?>