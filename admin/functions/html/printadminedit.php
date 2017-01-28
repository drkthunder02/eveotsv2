<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintAdminEdit($id) {
    printf("<form class=\"form-group\" medthod=\"POST\" action=\"?menu=admins_edit&id=" . $id . "\">
                <label>New Password:</label>
                <input class=\"form-control\" type=\"password\" name=\"newPassword\" size=\"16\">
                <label>Confirm:</label>
                <input class=\"form-control\" type=\"password\" name=\"newPConfirm\" size=\"16\">
                <input class=\"form-control\" name=\"submit\" value=\"Change\">
            </form>");
}

?>