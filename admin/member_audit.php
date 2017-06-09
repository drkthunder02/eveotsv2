<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

// PHP debug mode
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __DIR__.'/functions/registry.php';

$db = DBOpen();

$session = new Custom\Sessions\session();
$config = new EVEOTS\Config\Config();

//encrypt the unique session id in the form of a key for the form
$_SESSION['key'] = uniqid();
$unique = $_SESSION['key'] . $config->GetSalt();
$unique = md5($unique);

PrintAdminHTMLHeader();
printf("<body style=\"padding-top: 70px\">");
PrintAdminNavBar($db, $_SESSION['EVEOTSusername']);

//Check to make sure the user is logged in
$username = CheckLogin();
if($username == "") {
    PrintAdminHTMLHeader();
    PrintAdminNavBar($username);
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("<h1>You are not logged in.<br>");
    printf("</div></div>");
    die();
}
printf("<div class=\"container\">");
printf("<div class=\"jumbotron\">");
printf("<h1>Search for Members</h1><br>");
printf("<h3><strong>NOTE:</strong> This is an UNRESTRICTED search, ALL hits will be displayed on one page.</h3>");
printf("<form action=\"process/membersearch.php\" method=\"POST\">");
printf("<div class=\"form-group\">");
printf("<label for=\"search\">Character Search</label>");
printf("<input class=\"form-control\" type=\"text\" id=\"search\" name=\"search\">");
printf("</div>");
printf("<button class=\"btn btn-default\" type=\"submit\">Search</button>");
printf("</form>");
printf("</div>");
printf("</div>");

$listAmount = 50;
$userCount = $db->fetchColumn('SELECT COUNT(id) FROM Users');
$blueCount = $db->fetchColumn('SELECT COUNT(id) FROM Users WHERE Blue=\"true\">');
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

//Get the rows to populate the table
$rows = $db->fetchRowMany('SELECT * FROM Users ORDER BY TSName AND ASC LIMIT ' . $listFrom . ',' . $listAmount);

//Print the table of members
printf("<div class=\"container\">");
printf($userCount . " Registered members.<br>");
printf($userCountMinusBlues . " Excluding blues.<br><br>");
printf("<form class=\"form-group\" action=\"process/memberedit\" method=\"POST\">");
printf("<table class=\"table table-striped\">");
foreach($rows as $row) {
    $id = $row['id'];
    $charId = $row['CharacterID'];
    $blue = $row['Blue'];
    $tsDatabaseId = $row['TSDatabaseID'];
    $tsUniqueId = $row['TSUniqueID'];
    $tsName = $row['TSName'];
    if($blue == 1) {
        $icon = "images/blue.png";
    } else {
        $icon = "images/ally.png";
    }
    printf("<tr>");
    printf("<td><img src=\"http://image.eveonline.com/Character/" . $charId . "_32.jpg\"></td>");
    printf("<td><img src=\"" . $icon . "\"> " . $tsName . "<br> Unique ID: " . $tsUniqueId . "</td>");
    printf("<td align=\"right\">" . $tsDatabaseId . "</td>");
    printf("<td>");
    printf("<input class=\"form-control\" type=\"radio\" id=\"delete\" name=\"delete\" value=\"" . $id . "\">");
    printf("</td>");
    printf("<td><input class=\"form-control\" type=\"Submit\" value=\"Delete\"></td>");
    printf("</tr>");
    printf("<input class=\"form-control\" type=\"hidden\" name=\"key\" id=\"key\" value=\"" . $unique . "\">");
}
printf("</table>");
printf("</form>");
//BACK / NEXT
if(!isset($backPage)) {
    printf("Back | <a href=\"member_audit.php?page=" . $nextPage . "\">Next</a><br>");
} else if(!isset($nextPage)) {
    printf("<a href=\"member_audit.php?page=" . $backPage . "\">Back</a> | Next<br>");
} else if($page >= $maxPages) {
    printf("<a href=\"member_audit.php?page=" . $backPage . "\">Back</a> | Next<br>");
} else {
    printf("<a href=\"member_audit.php?page=" . $backPage . "\">Back</a> | <a href=\"member_audit.php?page=" . $nextPage . "\">Next</a><br>");
}
printf("</div>");

?>
