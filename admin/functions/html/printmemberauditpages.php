<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintMemberAuditPages(\Simplon\Mysql\Mysql $db, $listFrom, $listAmount) {
    $query = "SELECT * FROM users ORDER BY tsName ASC LIMIT " . $listFrom . "," . $listAmount;
    $rows = $db->fetchRowMany($query);
    
    printf("<table class=\"table-striped\">");
    printf("<tr><td></td><td></td><td>DatabaseID</td><td></td></tr>");
    
    
    foreach($rows as $row) {
        $id = $row['id'];
        $characterID = $row['CharacterID'];
        $blue = $row['Blue'];
        $tsDatabaseID = $row['TSDatabaseID'];
        $tsUniqueID = $row['TSUniqueID'];
        $tsName = $row['TSName'];
        if($blue == "Yes") {
            $icon = "images/blue.png";
        } else {
            $icon = "images/ally.png";
        }
        printf("<tr>");
        printf("<td width=\"32px\"><img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\"></td>");
        printf("<td><img src=\"".$icon."\" border\"0\"> ".$tsName."<br /><font size=\"2\">Unique ID: ".$tsUniqueID."</font></td>");
        printf("<td align=\"right\">".$tsDatabaseID."</td>");
        printf("<td><a href=\"?menu=members_edit&id=".$id."\"><img src=\"images/edit.png\" border=\"0\" title=\"Edit\"></a> <a href=\"?menu=members_delete&id=".$id."\" onclick=\"return confirm('Confirm removal of &quot;".$tsName."&quot; from Teamspeak?')\"><img src=\"images/delete.png\" border=\"0\" title=\"Delete\"></a></td>\n");
        printf("</tr>");        
    }
    printf("</table>");
}

?>
