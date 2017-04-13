<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintAdminTable($admins, $esi) {
    printf("<div class=\"container\">");
        printf("<thead>
                    <tr>
                        <td width=\"32px\"></td>
                        <td>Username</td>
                        <td align=\"center\">Corporation</td>
                        <td width=\"100px\">Security Level</td>
                    </tr>
                </thead>");
        
        foreach($admins as $row) {
            if($row['characterID'] != "") {
                $fetchCharacterInfo = $esi->GetCharacterInfo($row['characterID']);
                $fetchCorporationInfo = $esi->GetCorporationInfo($fetchCharacterInfo['corporation_id']);
                if($fetchCorporationInfo['alliance_id']) {
                    $fetchAllianceInfo = $esi->GetAllianceInfo($fetchCorporationInfo['alliance_id']);
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
            printf("<td>" . $fetchCorporationInfo['name'] . "</td>");
            printf("<td>" . $fetchAllianceInfo['name'] . "</td>");
            printf("<td align=\"center\">" . $row['securityLevel'] . "</td>");
            printf("</tr>");
        }
        printf("</table>");
        printf("</div>");
}

?>
