<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function AdminPanelMsg($msg) {
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container col-md-3\">");
    
    if($msg == 'AdminPasswordSuccess') {
        printf("<h2>Admin Password successfully changed.</h2><br>");
    } else if ($msg == 'AdminEditSuccess') {
        printf("<h2>Admin edited succesfully.</h2><br>");
    } else if ($msg == 'AdminDeleteSuccess') {
        printf("<h2>Admin deleted successfully.</h2><br>");
    } else if ($msg == 'AdminAddFailDuplicate') {
        printf("<h2>Admin Account Add Failed.</h2><br>");
        printf("<h2>User already has an admin account.<br>");
    } else if ($msg == 'MemberDeleteSuccess') {
        printf("<h2>Member Deleted successfully.<br>");
    } 
    
    printf("</div></div>");
}