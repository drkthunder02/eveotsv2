<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function MembersAudit($db, $search) {
    if(isset($_POST["search"]) && $search != "") {
        printf("<em>Note: This is an UNRESTRICTED search. ALL hits will be displayed and on one page.</em><br><br>");
        printf("<a href=\"?menu=members_audit\">Reset filter</a><br>");
        $row = $db->fetchRowMany('SELECT * FROM Users WHERE LOWER(TSName) LIKE LOWER(\"' . $search . '%\") ORDER BY TSName ASC');
        if($db->getRowCount() < 1) {
            printf("<br><strong>NO RESULTS</strong><br>");
        } else {
            printf("<table width=\"100%\" cellspacing=\"2\">");
            printf("<tr><td</td><td></td><td width=\"90px\" align=\"right\">Database ID</td><td width=\"16px\"></td></tr>");
            foreach($row as $r) {
                $id = $r['id'];
                $characterID = $r['CharacterID'];
                $blue = $r['Blue'];
                $tsDatabaseID = $r['TSDatabaseID'];
                $tsUniqueID = $r['TSUniqueID'];
                $tsName = $r['TSName'];
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
        printf("<br><a href=\"?menu=members_audit\">Reset filter</a>");
    } else {
        $listAmount = 50;
        $queryRows = $db->fetchColumnMany('SELECT Blue FROM Users');
        $userCount = $db->getRowCount();
        $queryBlue = $db->fetchColumnMany('SELECT Blue FROM Users WHERE Blue=\"Yes\"');
        $blueCount = $db->getRowCount();
        $userCountMinusBlues = $userCount - $blueCount;
        $exactPages = $userCount / $listAmount;
        $maxPages = ceil($exactPages);
        if(isset($_GET['page'])) {
            $page = filter_input(INPUT_GET, 'page');
            if($page == 1 || $page == NULL || $page == 0) {
                $page = 1;
                $listFrom = 0;
                $nextPage = 2;
            } else {
                $listFrom = $page * $listAmount - $listAmount;
                $nextPage = $page + 1;
                $backPage = $page - 1;
            }
        } else {
            $page = 1;
            $listFrom = 0;
            $nextPage = 2;
        }
        printf($userCount . " Registered members.<br>" . $userCountMinusBlues . " Excluding blues.<br><br>");
        //Search
        printf("<form action=\"?menu=members_audit\" method=\"POST\">");
        printf("<table>
                    <tr>
                        <td style=\"text-align: left;\"><input name=\"search\" size=\"20\" /></td>
                        <td style=\"text-align: right;\"><input name=\"submit\" type=\"submit\" value=\"Search\" /></td>
                    </tr>
                </table>");
        printf("</form>");

        //BACK / NEXT
        if(!isset($backPage)) {
            printf("Back | <a href=\"?menu=members_audit&page=" . $nextPage . "\">Next</a><br>");
        } else if(!isset($nextPage)) {
            printf("<a href=\"?menu=members_audit&pagee=" . $backPage . "\">Back</a> | Next<br>");
        } else if($page >= $maxPages) {
            printf("<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | Next<br />");
        } else {
            printf("<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | <a href=\"?menu=members_audit&page=".$nextPage."\">Next</a><br />");
        }
        printf("<strong>" . $page . " / " . $maxPages . "</strong><br>");
        $rows = $db->fetchRowMany('SELECT * FROM Users ORDER BY TSName ASC LIMIT ' . $listFrom . ',' . $listAmount);
        printf("<table width=\"100%\" cellspacing=\"2\">");
        printf("<tr> <td></td> <td></td> <td width=\"90px\" align=\"right\">Database ID</td> <td width=\"16px\"></td> </tr>");
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
        // BACK / NEXT
        if (!isset($backPage)) {
            printf("Back | <a href=\"?menu=members_audit&page=".$nextPage."\">Next</a><br />");
        } else if (!isset($nextPage)) {
            printf("<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | Next<br />");
        } else if ($page >= $maxPages) {
            printf("<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | Next<br />");
        } else {
            printf("<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | <a href=\"?menu=members_audit&page=".$nextPage."\">Next</a><br />");
        }
    }  
}