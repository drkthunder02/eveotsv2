<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

// PHP debug mode
ini_set('display_errors', 'On');
ini_set('date.timezone', 'Europe/London');
error_reporting(E_ALL | E_STRICT);

//Required files
require_once __DIR__.'/../functions/registry.php';

//Activate ESI API Namepsaces
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Log;

//Activate Classes
$config = new \EVEOTS\Config\Config();
$version = new \EVEOTS\Version\Version();
$session = new \Custom\Session\Sessions();

//Prepare logging for ESI API
$log = new Seat\Eseye\Log\FileLogger();
// Prepare an authentication container for ESI
$authentication = PrepareESIAuthentication();
// Instantiate a new ESI instance.
$esi = new Eseye($authentication);


if(!isset($_SESSION['EVEOTSusername'])) {
    $username = "";
    header("location:index.php");
} else {
    $username = $_SESSION['EVEOTSusername'];
}

//Connect to the database
$db = DBOpen();

?>

<html>
    <head>
        <!--metas-->
        <meta content="text/html" charset="utf-8" http-equiv="Content-Type">
        <meta content="EVEOTS V2 Admin Panel" name="description">
        <meta content="index,follow" name="robots">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <title>EVEOTS V2 Admin Panel</title>
        <link href="/../css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link rel="shortcut icon" href="/../images/banner.jpg" type="image/x-icon">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php
            PrintNavBar($username); 
            $queryAdmin = $db->fetchColumn('SELECT username FROM Admins WHERE username=: user', array('user' => 'admin'));
            $count = $db->getRowCount();
            if($count > 0) {
                $installAccount = true;
            } else {
                $installAccount = false;
            }
            if(isset($_GET['menu'])) {
                $menu = filter_input('GET', 'menu');
            } else {
                $menu = "main";
            }
            
            switch($menu) {
                case "main":
                    if($installAccount == true) {
                        printf("<div class=\"container\">
                                    <div class=\"panel-default\">
                                        <div class=\"panel-heading\">
                                            <h2>Warning</h2>
                                        </div>
                                        <div class=\"panel-body\">
                                            Warning: Setup not complete!<br>
                                            User \"admin\" is still in the database.<br>
                                            This is a massive security risk.<br>
                                            Please create yourself a new account, set it as the root admin and delete the default \"admin\" account immediately.<br>
                                            See the readme \"How to Setup A New Root Admin\" for more detailed instructions.                    
                                        </div>
                                    </div>
                                </div>");
                    }
                    PrintMainPanel();
                    $admins = $db->fetchRowMany('SELECT * FROM Admins ORDER BY username');
                    PrintAdminTable($db, $esi, $admins, $log, $config);
                    break;
                case "change_password":
                    break;
                case "logs":
                    break;
                case "admins_add":
                    break;
                case "admins_audit":
                    break;
                case "admins_delete":
                    break;
                case "admins_edit":
                    break;
                case "members_audit":
                    break;
                case "members_delete":
                    break;
                case "members_discrepancies":
                    break;
                case "members_edit":
                    break;
                case "whitelist":
                    break;
                case "whitelist_add":
                    break;
                case "whitelist_delete":
                    break;
            }
        ?>        
        
        
        
        
        
    </body>
</html>