<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintDeleteDiscrepancies(\Simplon\Mysql\Mysql $db, $discrepancy) {
    $deleted = 0;
    printf("Deleting discrepancies...<br><br>");
    printf("<table class=\"table-striped\">");
    printf("<tr><td>Deleteing...</td> <td></td> <td></td> <td>TS Database ID</td> <td></td></tr>");
    
    foreach($discrepancy as $discrep) {
        $row = $db->fetchRow('SELECT * FROM Users WHERE id= :id', array('id' => $discrep));
        $characterID = $row['CharacterID'];
        $blue = $row['Blue'];
        $tsDatabaseID = $row['TSDatabaseID'];
        $tsName = $row['TSName'];
        
        $db->delete('Users', array(
            'id' => $discrep,
        ));
        if($blue == "Yes") {
            $icon = "images/blue.png";
        } else {
            $icon = "images/ally.png";
        }
        printf("<tr bgcolor=\"#151515\"><td>".$discrep."</td> <td><img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\"></td> <td><img src=\"".$icon."\" border\"0\"> ".$tsName."<br /><font size=\"2\">Unique ID: ".$tsUniqueID."</font></td> <td align=\"center\">".$tsDatabaseID."</td> <td align=\"center\">Deleted</td></tr>");
    }
    
    printf("</table><br>");
    printf("Discrepancies removed: " . $deleted . "<br>");
    
}