<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintChangeSecurityLevel($id) {
    printf("<form class=\"form-group\" method=\"POST\" action=\"?menu=admins_edit&id=" . $id . "\">
                <label>New Security Level (1/2):</label>
                <input class=\"form-control\" name=\"newSL\" size=\"1\">
                <input class=\"form-control\" name=\"submit\" type=\"submit\" value=\"Change\">
            </form>");
}

?>
