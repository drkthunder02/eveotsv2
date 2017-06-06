<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintAdminIndexLogin() {
    printf("<body>
                <div class=\"container\">
                    <center><img src=\"images/banner.png\" border=\"0\"></center><br>
                </div>
                <div class=\"jumbotron\">
                    <div class=\"container col-md-4\">
                            <form class=\"form-control\" name=\"loginform\" method=\"POST\" action=\"login.php\">
                                <label for=\"username\">Username:</label>
                                <input class=\"form-control\" name=\"username\" type=\"text\" id=\"username\">
                                <label for=\"password\">Password:</label>
                                <input class=\"form-control\" name=\"password\" type=\"password\" id=\"password\">
                                <br>
                                <input class=\"form-control\" type=\"Submit\" name=\"Submit\" value=\"Login\">
                            </form>
                    </div>
                </div>
            </body>
            </html>");
}

?>
