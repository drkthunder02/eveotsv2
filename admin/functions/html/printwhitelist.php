<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintWhiteList($entities) {
    
    $esi = new EVEOTS\ESI\ESI();

    printf("<div class=\"container\">");
    printf("<div class=\"jumbotron\">");
    printf("<table class=\"table table-striped\">");
    printf("<thead>");
    printf("<tr>");
    printf("<td>Name</td><td>Character</td><td>Corporation</td><td>Alliance</td>");
    printf("</tr>");
    printf("<tbody>");
    foreach($entities as $entity) {
        printf("<tr>");
        if($entity['EntityType'] == 1) {
            $name = $esi->GetESIInfo($entity['EntityID'], 'Character');
            printf("<td>" . $name['name'] . "</td><td>X</td><td></td><td></td>");
        } else if($entity['EntityType'] == 2) {
            $name = $esi->GetESIInfo($entity['EntityID'], 'Corporation');
            printf("<td>" . $name['corporation_name'] . "</td><td></td><td>X</td><td></td>");
        } else if($entity['EntityType'] == 3) {
            $name = $esi->GetESIInfo($entity['EntityID'], 'Alliance');
            printf("<td>" . $name['alliance_name'] . "</td><td></td><td></td><td>X</td>");
        }
        printf("</tr>");
    }
    printf("<tbody>");
    printf("</table>");
    printf("</div>");
    printf("</div>");
}

?>
