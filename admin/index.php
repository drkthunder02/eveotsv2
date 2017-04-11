<?php

require_once __DIR__.'/functions/registry.php';
require_once __DIR__.'/../functions/registry.php';

$session = new \Custom\Sessions\session();
$version = new \EVEOTS\Version\Version();
$v = $version->release;

if (isset($_SESSION["EVEOTSusername"])) {
	header("location:admin_panel.php");
}

PrintAdminHTMLHeader();
PrintAdminIndexLogin();

?>
