<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

function PrintAdminNavBar($db, $username) {

    //Check the security level of the user to see which menus they are displayed
    $security = CheckSecurityLevel($db, $username);

    if($security['SecurityLevel'] == 1) {
        printf("<div class=\"navbar navbar-default navbar-fixed-top bg-fade\" style=\"height: 60px;\" role=\"navigation\">
                    <div class=\"navbar-header\">
                        <button class=\"navbar-toggle\" data-target=\".navbar-collapse\" data-toggle=\"collapse\" type=\"button\">
                            <span class=\"sr-only\">Toggle navigation</span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                        </button>
                    </div>
                    <div class=\"collapse navbar-collapse pull-left\">
                        <ul class=\"nav navbar-nav\">
                            <li><a href=\"admin_panel.php\">Main</a></li>                   
                            <li><a href=\"change_password.php\">Change Password</a></li>
                            <li><a href=\"logs.php\">Logs</a></li>
                            <li><a href=\"admin_audit.php\">Admin Audit</a></li>
                            <li><a href=\"member_audit.php\">Member Audit</a></li>
                            <li><a href=\"discrepancies.php\">Discrepancies</a></li>
                            <li><a href=\"whitelistadd.php\">White List Add</a></li>
                            <li><a href=\"whitelistdel.php\">White List Delete</a></li
                        </ul>
                    </div>
                    <div class=\"collapse navbar-collapse pull-right\">
                        <ul class=\"nav navbar-nav\">
                            <li><h2>" .  $username . " </h2></li>
                            <li><a href=\"logout.php\">Log Out</a></li>
                        </ul>
                    </div>
                </div>");
    } else if ($security['SecurityLevel'] == 2) {
        printf("<div class=\"navbar navbar-default navbar-fixed-top bg-fade\" style=\"height: 60px;\" role=\"navigation\">
                    <div class=\"navbar-header\">
                        <button class=\"navbar-toggle\" data-target=\".navbar-collapse\" data-toggle=\"collapse\" type=\"button\">
                            <span class=\"sr-only\">Toggle navigation</span>
                            <span class=\"icon-bar\"></span>
                            <span class=\"icon-bar\"></span>
                        </button>
                    </div>
                    <div class=\"collapse navbar-collapse pull-left\">
                        <ul class=\"nav navbar-nav\">
                            <li><a href=\"whitelistadd.php\">White List Add</a></li>                        
                            <li><a href=\"whitelistdel.php\">White List Delete</a></li>
                        </ul>
                    </div>
                    <div class=\"collapse navbar-collapse pull-right\">
                        <ul class=\"nav navbar-nav\">
                            <li><h2>" .  $username . " </h2></li>
                            <li><a href=\"logout.php\">Log Out</a></li>
                        </ul>
                    </div>
                </div>");
    } else {
        printf("<div class=\"navbar navbar-default navbar-fixed-top bg-fade\" style=\"height: 60px;\" role=\"navigation\">
                    <div class=\"navbar-header\">
                        <button class=\"navbar-toggle\" data-target=\".navbar-collapse\" data-toggle=\"collapse\" type=\"button\">
                            <span class=\"icon-bar\"></span>
                        </button>
                    </div>
                    <div class=\"collapse navbar-collapse pull-right\">
                        <ul class=\"nav navbar-nav\">
                            <li><h2>" .  $username . " </h2></li>
                            <li><a href=\"logout.php\">Log Out</a></li>
                        </ul>
                    </div>
                </div>");
    }
}

?>