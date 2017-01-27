<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintLogs(\Simplon\Mysql\Mysql $db) {
    $logs = $db->fetchRowMany('SELECT * FROM Logs ORDER BY id DESC');
    $count = $db->getRowCount();
    if($count > 0 ) {
        printf("<table class=\"table-striped\">");
        printf("<tr><td>Time</td><td>Log</td></tr>");
        foreach($logs as $log) {
            $timestamp = $log['time'];
            $entry = $log['entry'];
            printf("<tr><td>" . $timestamp . "</td><td>" . $entry . "</td></tr>");
        }
        printf("</table>");
    } else {
        printf("No logs found.");
    }
}

?>
