<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintChangePassword() {
    printf("Passwords can only contain A-Z, a-z and 0-9.<br /><br />");
    
    //Print the form for changing a password
    printf("<form class=\"form-group\" method=\"POST\" action=\"?menu=change_password\">
                <label>Password: </label>
                <input class=\"form-control\" type=\"password\" name=\"newPassword\">
                <label>Confirm: </label>
                <input class=\"form-control\" type=\"password\" name=\"newPConfirm\">
                <input class=\"form-control\" type=\"submit\" value=\"Change\">
            </form>");
}

?>



