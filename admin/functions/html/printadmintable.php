<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

function PrintAdminTable(\Simplon\Mysql $db, \Seat\Eseye\Eseye $esi, $admins, \Seat\Eseye\Log $log, \EVEOTS\Config\Config $config) {
    printf("<table class=\"table-bordered\">
                <tr>
                    <td>Username</td>
                    <td>Corporation</td>
                    <td>Security Level</td>
                </tr>");
    
    foreach($admins as $admin) {
        $characterID = $admin['CharacterID'];
        $username = $admin['Username'];
        $securityLevel = $admin['SecurityLevel'];
        if($characterID !== "") {
            //Get the characters current corporation
            try {
                $character = $esi->invoke('get', '/characters/{character_id}/contacts', [
                    'character_id' => (int)$characterID,
                ]);
            } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
                $errorCode = $e->getCode();
                $errorMsg = $e->getMessage();
                $error = "Code: " . $errorCode . ", Message: " . $errorMsg . "\n";
                //Print out the error to the log
                $log->error($error);
            }
            //Set the corporation ID
            $corporationID = $character->corporation_id;
            //Get the corporation name from the corporation ID
            $data = GetCorpAllyName($db, $esi, $log, $corporationID);
            $fetchCorporation = $data['corpName'];
            $fetchAlliance = $data['allianceName'];
        } else {
            $fetchCorporation = "";
            $fetchAlliance = "";
        }
        printf("<tr>");
        printf("<td>");
        //If we have a character ID then print the character's image
        if($characterID !== "") {
            printf("<img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\">");
        } else {
            printf("<img src=\"images/admin.png\" border=\"0\">");
        }
        printf("</td><td>" . $username . "</td><td align=\"center\">" . $fetchCorporation . "<br /><font size=\"2\">" . $fetchAlliance . "</font></td><td align=\"center\">" . $securityLevel . "</td></tr>"); 
    }
    printf("</table");
}

?>
