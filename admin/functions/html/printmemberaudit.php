<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintMemberAudit(\Simplon\Mysql\Mysql $db, $members) {
    printf("<table class=\"table-striped\">");
    printf("<tr><td></td><td></td><td>Database ID</td><td></td></tr>");
    foreach($members as $member) {
        $id = $member['id'];
        $characterId = $member['CharacterID'];
        $blue = $member['Blue'];
        $tsDatabaseID = $member['TSDatabaseID'];
        $tsUniqueID = $member['TSUniqueID'];
        $tsName = $member['TSName'];
        if($blue == "Yes") {
            $icon = "images/blue.png";
        } else {
            $icon = "images/ally.png";
        }
        echo "<tr>";
        printf("<td width=\"32px\"><img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\"></td>");
        printf("<td><img src=\"".$icon."\" border\"0\"> ".$tsName."<br /><font size=\"2\">Unique ID: ".$tsUniqueID."</font></td>");
        printf("<td align=\"right\">".$tsDatabaseID."</td>");
        printf("<td><a href=\"?menu=members_edit&id=".$id."\"><img src=\"images/edit.png\" border=\"0\" title=\"Edit\"></a> <a href=\"?menu=members_delete&id=".$id."\" onclick=\"return confirm('Confirm removal of &quot;".$tsName."&quot; from Teamspeak?')\"><img src=\"images/delete.png\" border=\"0\" title=\"Delete\"></a></td>\n");
        printf("</tr>");
    }
    printf("</table>");
    
}

?>