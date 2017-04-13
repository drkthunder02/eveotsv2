<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintWhiteListForm() {
    printf("<div class=\"container\">");
    printf("<div class=\"jumbotron\">");
    printf("<h2>If a corporation is in an alliance it is advised <em>not</em> to give the corporation access, but to give the alliance the access. Also remember to keep an eye out for corporations with access joining alliances that shouldn't have it, you should remove these corporations.<br /><strong>TL;DR:</strong> All corporations in your corporation list should NOT be in an alliance.</h2>");
    printf("</div>");
    printf("</div>");
    printf("<div class=\"container\">");
    printf("<form class=\"form-control\" action=\"?menu=whitelist_add&type=alliance\" method=\"post\">
                <table class=\"table>
                    <tr>
                        <td style=\"text-align: right;\"><font size=\"2\">Alliance Name:</font></td>
                        <td style=\"text-align: left;\"><input class=\"form-control\" name=\"allianceName\" size=\"16\"/></td>
                        <td colspan=\"2\" style=\"text-align: right;\"><input class=\"form-control\" name=\"submit\" type=\"submit\" value=\"Add Alliance\" /></td>
                    </tr>
                </table>
                </form>
                <form class=\"form-control\" action=\"?menu=whitelist_add&type=corp\" method=\"post\">
                <table>
                    <tr>
                        <td style=\"text-align: right;\"><font size=\"2\">Corporation Name:</font></td>
                        <td style=\"text-align: left;\"><input class=\"form-control\" name=\"corpName\" size=\"16\"/></td>
                        <td colspan=\"2\" style=\"text-align: right;\"><input name=\"submit\" type=\"submit\" value=\"Add Corporation\" /></td>
                    </tr>
                </table>
                </form>
                <form class=\"form-control\" action=\"?menu=whitelist_add&type=char\" method=\"post\">
                <table>
                    <tr>
                        <td style=\"text-align: right;\"><font size=\"2\">Character Name:</font></td>
                        <td style=\"text-align: left;\"><input class=\"form-control\" name=\"charName\" size=\"16\"/></td>
                        <td colspan=\"2\" style=\"text-align: right;\"><input name=\"submit\" type=\"submit\" value=\"Add Character\" /></td>
                    </tr>
                </table>
                </form>");
    printf("</div>");
}

?>
