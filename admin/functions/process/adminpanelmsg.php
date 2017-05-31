<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function AdminPanelMsg($msg) {
    if($msg == 'AdminPasswordSuccess') {
        printf("<div class=\"junbotron\">");
        printf("<div class=\"container col-md-3\">");
        printf("<h2>Admin Password successfully changed.</h2><br>");
        printf("</div><div>");
    } else if ($msg == 'AdminEditSuccess') {
        printf("<div class=\"jumbotron\">");
        printf("<div class=\"container col-md-3\">");
        printf("<h2>Admin edited succesfully.</h2><br>");
        printf("</div></div>");
    } else if ($msg == 'AdminDeleteSuccess') {
        printf("<div class=\"jumbotron\">");
        printf("<div class=\"container col-md-3\">");
        printf("<h2>Admin deleted successfully.</h2><br>");
        printf("</div></div>");
    } else if ($msg == 'AdminAddFailDuplicate') {
        printf("<div class=\"jumbotron\">");
        printf("<div class=\"container col-md-3\">");
        printf("<h2>Admin Account Add Failed.</h2><br>");
        printf("<h2>User already has an admin account.<br>");
        printf("</div></div>");
    }
}