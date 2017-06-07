<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintAdminTable($admins, \EVEOTS\ESI\ESI $esi) {
    $fetchCharacterInfo = "";
    $fetchCorporationInfo = "";
    $fetchAllianceInfo = "";
    
    printf("<div class=\"container\">");
        printf("<thead>
                    <tr>
                        <td>Portrait</td>
                        <td>Username</td>
                        <td align=\"center\">Corporation</td>
                        <td align=\"center\">Alliance</td>
                        <td align=\"center\">Security Level</td>
                    </tr>
                </thead>");
        printf("<tbody>");
        foreach($admins as $row) {
            if($row['characterID'] != "") {
                $fetchCharacterInfo = $esi->GetESIInfo($row['characterID'], 'Character');
                $fetchCorporationInfo = $esi->GetESIInfo($fetchCharacterInfo['corporation_id'], 'Corporation');
                if(isset($fetchCorporationInfo['alliance_id'])) {
                    $fetchAllianceInfo = $esi->GetESIInfo($fetchCorporationInfo['alliance_id'], 'Alliance');
                }
            } else {
                $fetchCorporationInfo = "";
                $fetchAllianceInfo = "";
            }
            
            printf("<tr>");
            printf("<td width=\"32px\">");
            if($row['characterID'] != "") {
                printf("<img src=\"http://image.eveonline.com/Character/" . $row['characterID'] . "_32.jpg\" border=\"0\">");
            } else {
                printf("<img strc=\"images/admin.png\" border=\"0\">");
            }
            printf("</td>");
            printf("<td>" . $row['username'] . "</td>");
            if($row['corporationID'] != "") {
                printf("<td><img src=\"http://image.eveonline.com/Corporation/" . $row['corporationID'] . "_32.jpg\" border=\"0\"></td>");
            } else {
                printf("<td>N/A</td>");
            }
            if($row['allianceID'] != "") {
                printf("<td><img src=\"http://image.eveonline.com/Alliance/" . $row['allianceID'] . "_32.jpg\" border=\"0\"></td>");
            } else {
                printf("<td>N/A</td>");
            }          
            printf("<td align=\"center\">" . $row['securityLevel'] . "</td>");
            printf("</tr>");
        }
        printf("</tbody>");
        printf("</table>");
        printf("</div>");
}

?>
