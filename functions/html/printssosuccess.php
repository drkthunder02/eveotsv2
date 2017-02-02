<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrintSSOSuccess($characterID) {
    //Using the database, let's get the character's information along with any relevant corp or alliance information
    $character = false;
    $corporation = false;
    $alliance = false;
    $name = '';
    $config = new EVEOTS\Config\Config();
    $characterID;
    $corporationID;
    $allianceID;
    $blue = false;
    $success = false;
    $main = false;
    
    //Open the database
    $db = DBOpen();
    
    //Get the white lists
    $charWhiteList = $db->fetchRowMany('SELECT * FROM Blues WHERE EntityType= :type', array('type' => 1));
    $corpWhiteList = $db->fetchRowMany('SELECT * FROM Blues WHERE EntityType= :type', array('type' => 2));
    $allyWhiteList = $db->fetchRowMany('SELECT * FROM Blues WHERE EntityType= :type', array('type' => 3));
    $usWhiteList = $config->GetMainAlliance();
    
    //Get the character from the database stored using previous functions
    $character = $db->fetchRow('SELECT * FROM Characters WHERE CharacterID= :id', array('id' => $characterID));
    if($character != false) {
        //Get the corporation from the database stored using previous functions
        $corporation = $db->fetchRow('SELECT * FROM Corporations WHERE CorporationID= :id', array('id' => $character['CorporationID']));
        $characterID = $character['CharacterID'];
    }
    if($corporation != false ) {
        //Attempt to get the alliance info
        $alliance = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $corporation['AllianceID']));
        $corporationID = $corporation['CorporationID'];
    }
    if($alliance != false) {
        $allianceID = $alliance['AllianceID'];
    }

    //Check if the character is part of the main alliance / corp
    if($corporation != false && $alliance != false) {
        if($usWhiteList == $alliance['Alliance']) {
            $blue = true;
            $success = true;
        }
    } else if ($corporation != false) {
        if($usWhiteList == $corporation['Corporation']) {
            $blue = true;
            $success = true;
        }
    }
    //Cycle through the alliances and see if the character is in a blue group
    if($alliance != false && $success == false) {
        foreach($allyWhiteList as $list) {
            if($list['EntityID'] == $allianceID) {
                $blue = true;
                $success = true;
            }
        }
    }
    if($corporation != false && $success == false) {
        foreach($corpWhiteList as $list) {
            if($list['EntityID'] == $corporationID) {
                $blue = true;
                $success = true;
            }
        }
    }
    if($character != false && $success == false) {
        foreach($charWhiteList as $list) {
            if($list['EntityID'] == $characterID) {
                $blue = true;
                $success = true;
            }
        }
    }
    
    //Print the header for the data below
    PrintHTMLHeader();
    
    if($blue == true) {
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
    
    
    
    
    //Insert the name if it's not '' into the Users table
    if($name != '') {
        $db->insert('Users', array(
            'CharacterID' => $characterID,
            'Blue' => $blue,
            'TSName' => $name
        ));   
    }
    
    //Print out the form to let the user update their own permissions on the teamspeak server
    printf("<div class=\"container\">");
    printf("<div class=\"jumbotron\">");
    printf("<form class=\"form-goup\" method=\"POST\" action=\"teamspeak.php\">
                <input class=\"form-control\" type=\"hidden\" name=\"characterID\" value=\"" . $characterID . "\">
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
