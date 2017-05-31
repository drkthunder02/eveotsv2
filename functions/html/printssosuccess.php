<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintSSOSuccess($CharacterID, $CorporationID, $AllianceID) {
    //Using the database, let's get the character's information along with any relevant corp or alliance information
    $character = false;
    $corporation = false;
    $alliance = false;
    $name = '';
    $config = new EVEOTS\Config\Config();
    $blue = false;
    $success = false;
    $main = false;
    $us = false;
    
    $_SESSION['us'] = false;
    $_SESSION['blue'] = false;
    $_SESSION['CharacterId'] = $CharacterID;
    $_SESSION['CorporationId'] = $CorporationID;
    $_SESSION['AllianceId'] = $AllianceID;
    
    //Open the database
    $db = DBOpen();
    
    //Get the white lists
    $charWhiteList = $db->fetchRowMany('SELECT * FROM Blues WHERE EntityType= :type', array('type' => 1));
    $corpWhiteList = $db->fetchRowMany('SELECT * FROM Blues WHERE EntityType= :type', array('type' => 2));
    $allyWhiteList = $db->fetchRowMany('SELECT * FROM Blues WHERE EntityType= :type', array('type' => 3));
    var_dump($charWhiteList);
    printf("<br>");
    var_dump($corpWhiteList);
    printf("<br>");
    var_dump($allyWhiteList);
    printf("<br>");
    $usWhiteList = $config->GetMainAlliance();
    
    //Check if the person is part of the main alliance / corp
    if($CorporationID || $AllianceID == $usWhiteList) {
        $us = true;
        $blue = true;
    }
    
    //Check if the person is on the character whitelist
    if($charWhiteList != null) {
        foreach($charWhiteList as $en) {
            if($en['EntityID'] == $CharacterID) {
                $blue = true;
                break;
            }
        }
    }
    
    //Check if the person is on the corporation whitelist
    if($corpWhiteList != null) {
        foreach($corpWhiteList as $en) {
            if($en['EntityID'] == $CorporationID) {
                $blue = true;
                break;
            }
        }
    } else if($usWhiteList == $CorporationID) {
        $us = true;
        $blue = true;
    }
    
    //Check if the person is on the alliance whitelist
    if($allyWhiteList != null) {
        foreach($allyWhiteList as $en) {
            if($en['EntityID'] == $AllianceID) {
                $blue = true;
                break;
            }
        }
    } else if($usWhiteList == $AllianceID) {
        $us = true;
        $blue = true;
    }
    
    //Print the header for the data below
    PrintHTMLHeader();
    
    if($blue == true) {
        //If the user is blue, then let's build the alliance, corporation, and character information
        $character = $db->fetchRow('SELECT * FROM Characters WHERE CharacterID= :id', array('id' => $CharacterID));
        $corporation = $db->fetchRow('SELECT * FROM Corporations WHERE CorporationID= :id', array('id' => $CorporationID));
        $alliance = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $AllianceID));
        //Now we need to build the teamspeak name
        if($config->GetSpacer() != "") {
            //Form the name of the pilot
            if($alliance != false && $corporation != false && $character != false) {
                //Form the ts name
                $name = $alliance['Ticker'] . " " . $config->GetSpacer() . " " . $corporation['Ticker'] . " " . $config->GetSpacer() . " " . $character['Character'];
            } else if ($corporation != false && $character != false) {
                //Form the TS3 name
                $name = $corporation['Ticker'] . " " . $config->GetSpacer() . " " . $character['Character'];
            } else if ($character != false) {
                $name = $character['Character'];
            } else {
                printf("<div class=\"container\">");
                printf("<div class=\"jumbotron\">");
                printf("<2>Your character was not found in the database.  Please try again later.</h2>");
                printf("</div>");
                printf("</div>");
                DBClose($db);
                return;
            } 
            
            
        } else {
            //Form the name of the pilot
            if($alliance != false && $corporation != false && $character != false) {
                //Form the ts name
                $name = $alliance['Ticker'] . " " . $corporation['Ticker'] . " " . $character['Character'];
            } else if ($corporation != false && $character != false) {
                //Form the TS3 name
                $name = $corporation['Ticker'] . " " . $character['Character'];
            } else if ($character != false) {
                $name = $character['Character'];
            } else {
                printf("<div class=\"container\">");
                printf("<div class=\"jumbotron\">");
                printf("<2>Your character was not found in the database.  Please try again later.</h2>");
                printf("</div>");
                printf("</div>");
                DBClose($db);
                return;
            } 
        } 
    } else {
        printf("<div class=\"container\">");
        printf("<div class=\"jumbotron\">");
        printf("<h2>Your character is not allowed on this Teamspeak3 Server.</h2>");
        printf("</div>");
        printf("</div>");
        return;
    }
    
    //Set the name in the session
    $_SESSION['name'] = $name;
    
    
    //Insert the name if it's not '' into the Users table
    if($name != '') {
        $db->insert('Users', array(
            'CharacterID' => $CharacterID,
            'Blue' => $blue,
            'TSName' => $name
        ));   
    }
    
    //Print out the form to let the user update their own permissions on the teamspeak server
    printf("<div class=\"container\">");
    printf("<div class=\"jumbotron\">");
    printf("<form class=\"form-goup\" method=\"POST\" action=\"teamspeak.php\">
                <input class=\"form-control\" type=\"hidden\" name=\"characterID\" value=\"" . $CharacterID . "\">
                <input class=\"form-control\" type=\"hidden\" name=\"corporationID\" value=\"" . $CorporationID . "\">
                <input class=\"form-control\" type=\"hidden\" name=\"allianceID\" value=\"" . $AllianceID . "\">
                <input class=\"form-control\" type=\"hidden\" name=\"us\" value=\"" . $us ."\">
                <input class=\"form-control\" type=\"hidden\" name=\"blue\" value=\"" . $blue . "\">
                <input class=\"form-control\" type=\"hidden\" name=\"tsname\" value=\"" . $name . "\">
                <input class=\"form-conotrol\" type=\"submit\" value=\"Update TS Permissions\">
            </form>");
    printf("</div>");
    printf("</div>");
    
    //Close the database connection
    DBClose($db);
    return;
}

?>
