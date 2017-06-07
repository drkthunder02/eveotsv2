<?php

// PHP debug mode
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __DIR__.'/../functions/registry.php';
require_once __DIR__.'/functions/registry.php';

$session = new \Custom\Sessions\session();
session_destroy();

PrintAdminHTMLHeader();
printf("<body style=\"padding-top: 70px\">");

?>
<html>
<head>
	<title>EVEOTS V2 Admin Panel</title>
</head>
<body>
	<center>
		You are now logged out.<br />
		<br />
		<a href="index.php">Login</a><br />
	</center>
</body>
</html>
