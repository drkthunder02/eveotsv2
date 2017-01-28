<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintAdminAdd(\Simplon\Mysql\Mysql $db) {
    printf("<strong>Add administrator:</strong><br>");
    printf("Character Name: Must be their exact in-game name.<br>");
    printf("Password: a-Z & 0-9 only.<br>");
    printf("Security Level: 1 = Super Admin.  2 = Disabled Audit Menus.");
    printf("<form class=\"form-group\" method=\"POST\" action=\"?menu=admins_add\">
                <label>Character Name:</label>
                <input class=\"form-control\" name=\"adminCharacterName\" size=\"16\" type=\"text\">
                <label>Password:</label>
                <input class=\"form-control\" name=\"adminPassword\" size=\"16\" type=\"text\">
                <label>Security Level (1/2):</label>
                <input class=\"form-control\" name=\"adminSecurityLevel\" size=\"1\" type=\"text\">
                <input class=\"form-control\" name=\"submit\" type=\"submit\" value=\"Add\">
            </form>");
    printf("<br><br>");
    $admins = $db->fetchRowMany('SELECT * FROM Admins');
    printf("<table class=\"table-striped\">");
    printf("<tr><td>Username</td><td>Corporation</td><td>Security Level</td></tr>");
    foreach($admins as $admin) {
        $corp = $db->fetchColumn('SELECT Corporation FROM Members WHERE Character= :char', array('char' => $admin['username']));
        printf("<tr>");
        printf("<td>" . $admin['username'] . "</td>");
        printf("<td>" . $corp . "</td>");
        printf("<td>" . $admin['securityLevel'] . "</td>");
        printf("</tr>");
    }
}

?>

