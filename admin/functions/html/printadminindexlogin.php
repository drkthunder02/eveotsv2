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
                <div class=\"jumbotron col-md-4 col-md-offset-4\">
                    <form name=\"loginform\" action=\"login.php\" method =\"post\">
                        <div class=\"form-group\">
                            <label for=\"username\">Username:</label>
                            <input class=\"form-control\" type=\"text\" name=\"username\" id=\"username\" placeholder=\"Username\">
                        </div>
                        <div class=\"form-group\">
                            <label for=\"password\">Password:</label>
                            <input class=\"form-control\" type=\"password\" name=\"password\" id=\"password\" placeholder=\"Password\">
                        </div>
                        <button type=\"submit\" class=\"btn btn-default\">Submit</button>
                    </form>
                </div>
            </body>
            </html>");
}

?>
