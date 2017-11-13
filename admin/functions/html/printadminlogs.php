<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintAdminLogs(\Simplon\Mysql\Mysql $db) {
    $logs = $db->fetchRowMany('SELECT * FROM Logs ORDER BY id DESC');
    $logCount = $db->getRowCount();
    printf("<div class=\"container\">");
    if($logCount > 0) {
        printf("<table class=\"table table-striped\">");
        foreach($logs as $log) {
            printf("<tr>");
            printf("<td align=\"center\">" . $log['time'] . "</td>");
            printf("<td align=\"center\">" . $log['entry'] . "</td>");
            printf("</tr>");
        }
        printf("</table>");
    } else {
        printf("No Logs found!");
    }
    printf("</div>");
}

?>
