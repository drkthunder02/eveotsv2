<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function AdminPanelMsg($msg) {
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    
    if($msg == 'AdminPasswordSuccess') {
        printf("<h4>Admin Password successfully changed.</h4>");
    } else if ($msg == 'AdminEditSuccess') {
        printf("<h4>Admin edited succesfully.</h4>");
    } else if ($msg == 'AdminDeleteSuccess') {
        printf("<h4>Admin deleted successfully.</h4>");
    } else if ($msg == 'AdminAddFailDuplicate') {
        printf("<h4>Admin Account Add Failed.</h4><br>");
        printf("<h4>User already has an admin account.</h4>");
    } else if ($msg == 'MemberDeleteSuccess') {
        printf("<h4>Member Deleted successfully.</h4>");
    } else if ($msg == 'MemberDeleteTSFail') {
        printf("<h4>Deleted member from the database.</h4><br>");
        printf("<h4>Failed to kick member from teamspeak.</h4>");
    } else if ($msg == 'WhiteListSuccess') {
        printf("<h4>White List was successfully modified.</h4>");
    } else if ($msg == 'WhiteListFail') {
        printf("<h4>White List was not modified successfully.</h4>");
    } else if ($msg == 'AdminAddSuccess') {
        printf("<h4>A new Administrator was added to the database.</h4>");
    } else if ($msg == 'ClearLogsSuccess') {
        printf("<h4>Admin Logs have been cleared successfully.</h4>");
    }
    
    printf("</div></div>");
}