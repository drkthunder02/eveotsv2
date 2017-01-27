<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

function PrintMainPanel() {
    printf("<div class=\"container\">
                <div class=\"panel-default\">
                    <div class=\"panel-heading\">
                        <h2>Welcome to your EVEOTS V2 Admin Panel</h2>
                    </div>
                    <div class=\"panel-body\">
                        Here are a few things you should know:<br>
                        <ul>
                            <li>
                                <strong>Root Administrator - </strong>This person should be the main administrator.
                            </li>
                            <li>
                                <strong>Security Level (SL) - </strong>This is the level of control someone has.<br>
                                1 = Super Admin - Edit, Delete, and Create other Admins via the Audit Menus.<br>
                                2 = Normal Admin - Make changes to the whitelist.  Canno access the Audit Menus.                        
                            </li>
                        </ul>
                    </div>
                </div>
            </div>");
}

?>