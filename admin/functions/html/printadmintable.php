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
                        <td width=\"32px\"></td>
                        <td>Username</td>
                        <td align=\"center\">Corporation</td>
                        <td align=\"center\">Alliance</td>
                        <td width=\"100px\">Security Level</td>
                    </tr>
                </thead>");
        
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
            printf("<td>" . $fetchCorporationInfo['corporation_name'] . "</td>");
            if(isset($fetchAllianceInfo['alliance_name'])) {
                printf("<td>" . $fetchAllianceInfo['alliance_name'] . "</td>");
            } else {
                printf("<td>N/A</td>");
            }
            
            printf("<td align=\"center\">" . $row['securityLevel'] . "</td>");
            printf("</tr>");
        }
        printf("</table>");
        printf("</div>");
}

?>
