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
$esi = new EVEOTS\ESI\ESI();

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
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("<h1>You are not logged in.<br>");
    printf("</div></div>");
    die();
}

$entities = $db->fetchRowMany('SELECT * FROM Blues');
PrintWhiteList($entities);

printf("<div class=\"container\">");
printf("<div class=\"jumbotron\">");
printf("<h1>White List Delete Form</h1><br>");
printf("<form action=\"functions/form/whitelistdel.php\" method=\"POST\">");
printf("<div class=\"form-group\">");
printf("<label for=\"entity\">Entity</label>");
printf("<select class=\"form-control\" id=\"entity\" name=\"entity\">");
foreach($entities as $entity) {
    //Serealize the data into json first
    $data = array('EntityID' => $entity['EntityID'], 'EntityType' => $entity['EntityType']);
    $formData = json_encode($data);
    if($entity['EntityType'] == 1) {
        //Character
        $dbChar = $db->fetchRow('SELECT * FROM Characters WHERE CharacterID= :id', array('id' => $entity['EntityID']));
        if($dbChar['Character'] == "") {
            $data = $esi->GetESIInfo($entity['EntityID'], 'Character');
            printf("<option value=\"" . $formData . "\">" . $data['name'] . "</option>");
        } else {
            printf("<option value=\"" . $formData . "\">" . $dbChar['Character'] . "</option>");
        }
    } else if($entity['EntityType'] == 2) {
        //Corporation
        $dbCorp = $db->fetchRow('SELECT * FROM Corporations WHERE CorporationID= :id', array('id' => $entity['EntityID']));
        if($dbCorp['Corporation'] == "") {
            $data = $esi->GetESIInfo($entity['EntityID'], 'Corporation');
            printf("<option value=\"" . $formData . "\">" . $data['corporation_name'] . "</option>");
        } else {
            printf("<option value=\"" . $formData . "\">" . $dbCorp['Corporation'] . "</option>");
        }
    } else if ($entity['EntityType'] == 3) {
        //Alliance
        $dbAlly = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $entity['EntityID']));
        if($dbAlly['Alliance'] == "") {
            $data = $esi->GetESIInfo($entity['EntityID'], 'Alliance');
            printf("<option value=\"" . $formData . "\">" . $data['alliance_name'] . "</option>");
        } else {
            printf("<option value=\"" . $formData . "\">" . $dbAlly['Alliance'] . "</option>");
        }
    }
}
printf("</select>");
printf("<input class=\"form-control\" type=\"hidden\" id=\"key\" name=\"key\" value=\"" . $unique . "\">");
printf("</div>");
printf("<button type=\"submit\" class=\"btn btn-default\">White List Delete</button>");
printf("</form>");
printf("</div>");
printf("</div>");

/*
printf("<div class=\"container\">");
printf("<h1>White List Delete Form</h1><br>");
printf("<form action=\"functions/form/whitelistdel.php\" method=\"POST\">");
printf("<div class=\"form-group\">");
printf("<label for=\"entity\">Entity</label>");
printf("<input class=\"form-control\" type=\"text\" id=\"entity\" name=\"entity\">");
printf("</div>");
printf("<div class=\"form-group\">");
printf("<select class=\"form-control\" id=\"type\" name=\"type\">");
printf("<option value=\"alliance\">Alliance</option>");
printf("<option value=\"corporation\">Corporation</option>");
printf("<option value=\"character\">Character</option>");
printf("</select>");
printf("<input class=\"form-control\" type=\"hidden\" id=\"key\" name=\"key\" value=\"" . $unique . "\">");
printf("</div>");
printf("<button type=\"submit\" class=\"btn btn-default\">White List Delete</button>");
printf("</form>");
printf("</div>");
 *
 */

printf("</body></html>");

?>
