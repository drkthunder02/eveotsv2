<?php

/*
 *
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK
 * */

namespace EVEOTS\Config;

class Config {
    //Teamspeak3 Server Info
    private $tshost = ""; //Teamspeak3 Server URL
    private $tsname = ""; //Teamspeak3 ServerQuery User Name
    private $tspass = ""; //Teamspeak3 ServerQuery User Password
    private $tsport = "10011"; // ServerQuery Port
    private $tscport = "9987"; // Teamspeak3 Client Port
    private $tspassword = ""; //(OPTIONAL) Teamspeak3 Client Connect Password

    //ESI Configuration
    private $clientid = "";
    private $secret = "";
    private $accessToken = "";
    private $refreshToken = "";

    //Administrators Character
    private $admin = "";
    //Database ID of the ROOT admin (from "admins" table), this admin cannot be deleted by a rogue.  Make it your ID.
    private $adminID = 1;
    
    //Test Character ID and Character Name.
    //Must be the character id and character name fo the same character respectively
    private $testid = 9353338328;
    private $testname = "MJ Maverick";
	
    // (REQUIRED) Your alliance / corp name
    private $ourname = "";
    //Debug Mode?
    private $verbose = false;
    //(REQUIRED) Teamspeak3 group for alliance / corp members
    private $group = 0;
    //(REQUIRED) Teamspeak3 group for people on whitelist but not in your alliance / corp
    private $bluegroup = 0;
    //Banner Image
    private $banner = "images/banner.jpg";
    //(OPTIONAL) Ticker Spacers. - Example for "IRNP | MJ Maverick" use "|".  Leave blank for "IRNP MJ Maverick".
    private $spacer = "";
    
    public function __construct() {
        
    }
    
    public function GetAdminID() {
        return $this->adminID;
    }
    
    public function GetSpacer() {
        return $this->spacer;
    }
    
    public function GetBlueGroup() {
        return $this->bluegroup;
    }
    
    public function GetMainGroup() {
        return $this->group;
    }
    
    public function GetBanner() {
        return $this->banner;
    }
    
    public function GetMainAlliance() {
        return $this->ourname;
    }
    
    public function GetDebugMode() {
        return $this->verbose;
    }
    
    public function GetTestCharacter() {
        $info = array(
            'testid' => $this->testid,
            'testname' => $this->testname
        );
        
        return $info;
    }
	
    public function GetTSConfig() {
        $info = array(
            'tshost' => $this->tshost,
            'tsname' => $this->tsname,
            'tspass' => $this->tspass,
            'tsport' => $this->tsport,
            'tcsport' => $this->tcsport,
            'tspassword' => $this->tspassword
        );
        
        return $info;
    }
    
    public function GetTSServerQuery() {
        $query = "serverquery://".$this->tsname.":".$this->tspass."@".$his->tshost.":".$this->tsport."/?server_port=".$this->tscport;
        
        return $query;
    }
    
    public function GetESIConfig() {
        $info = array(
            'clientid' => $this->clientid,
            'secret' => $this->secret,
            'accessToken' => $this->accessToken,
            'refreshToken' => $this->refreshToken
        );
        
        return $info;
    }
    
    public function GetAdminConfig() {
        $info = array(
            'admin' => $this->admin,
            'adminID' => $this->adminID
        );
        
        return $info;
    }
}