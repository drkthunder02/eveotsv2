<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintWhiteList($whiteList) {
    $allyList = array();
    $corpList = array();
    $charList = array();
    $allyNum = 0;
    $corpNum = 0;
    $charNum = 0;
    foreach($whiteList as $list) {
        if($list['EntityType'] == 3) {
            $allyList[$allyNum] = $list['EntityID'];
            $allyNum++;
        } else if($list['EntityType'] == 2) {
            $corpList[$corpNum] = $list['EntityID'];
            $corpNum++;
        } else if($list['EntityType'] ==1) {
            $charList[$charNum] = $list['EntityID'];
            $charNum++;
        }
    }
    printf("<div class=\"container\">");
    printf("<table class=\"table-striped\">");
    printf("<tr><td><Type/td><td>Name</td><td>Members</td></tr>");
    for($i = 0; $i < $allyNum; $i++) {
        $ally = $db->fetchRow('SELECT Alliance,Members FROM Alliances WHERE AllianceID= :id', array('id' => $allyList[$i]));
        printf("<tr><td>Alliance</td><td>" . $ally['Alliance'] . "</td><td>" . $ally['Members'] . "</td></tr>");
    }
    for($i = 0; $i < $corpNum; $i++) {
        $corp = $db->fetchRow('SELECT Corporation,Members FROM Corporations WHERE CorporationID= :id', array('id' => $corpList[$i]));
        printf("<tr><td>Corporation</td><td>" . $corp['Corporation'] . "</td><td>" . $corp['Members'] . "</td></tr>");
    }
    for($i = 0; $i < $charNum; $i++) {
        $char = $db->fetchColumn('SELECT Character FROM Characters WHERE CharacterID= :id', array('id' => $charList[$i]));
        printf("<tr><td>Character</td><td>" . $char . "</td><td>N/A</td></tr>.");
    }
    printf("</table>");
    printf("</div>");
}

?>
