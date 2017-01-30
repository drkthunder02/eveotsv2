<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

function PrintNavBar($username) {
    printf("<div class=\"navbar navbar-inverse navbar-fixed-top\" style=\"height: 60px;\" role=\"navigation\">
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
                    </button>
                </div>
                <div class=\"collapse navbar-collapse pull-left\">
                    <ul class=\"nav navbar-nav\">
                        <li><a href=\"admin_panel.php?menu=main\">Main</a></li>
                        <li><a href=\"admin_panel.php?menu=change_password\">Change Password</a></li>
                        <li><a href=\"admin_panel.php?menu=logs\">Logs</a></li>
                        <li><a href=\"admin_panel.php?menu=admins_audit\">Admin Audit</a></li>
                        <li><a href=\"admin_panel.php?menu=members_audit\">Member Audit</a></li>
                        <li><a href=\"admin_panel.php?menu=members_discrepancies\">Discrepancies</a></li>
                        <li><a href=\"admin_panel.php?menu=whitelist\">Whitelist</a></li>                        
                    </ul>
                </div>
                <div class=\"collapse navbar-collapse pull-right\">
                    <ul class=\"nav navbar-nav\">
                        <li><h2>" .  $username . " </h2></li>
                        <li><a href=\"logout.php\">Log Out</a></li>
                    </ul>
                </div>
            </div>");
}

?>