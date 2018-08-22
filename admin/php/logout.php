<?php
	session_start();
	session_destroy();
	
	header('Location: /securityApp/hackathon/index.html');
?>