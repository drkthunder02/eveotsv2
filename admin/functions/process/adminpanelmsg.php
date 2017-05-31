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
        printf("<h2>User already has an admin account.</h2><br>");
    } else if ($msg == 'MemberDeleteSuccess') {
        printf("<h2>Member Deleted successfully.</h2><br>");
    } else if ($msg == 'MemberDeleteTSFail') {
        printf("<h2>Deleted member from the database.</h2><br>");
        printf("<h2>Failed to kick member from teamspeak.</h2><br>");
    } else if ($msg == 'WhiteListSuccess') {
        printf("<h2>White List was successfully modified.</h2><br>");
    } else if ($msg == 'WhiteListFail') {
        printf("<h2>White List was not modified successfully.</h2><br>");
    }
    
    printf("</div></div>");
}