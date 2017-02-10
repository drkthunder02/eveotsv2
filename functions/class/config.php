<?php

/*
 *
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK
 * */

namespace EVEOTS\Config;

class Config {
    //Teamspeak3 Server Info
    private $tshost;
    private $tsname;
    private $tspass;
    private $tsport; 
    private $tscport; 
    private $tspassword;

    private $admin;
    private $adminID;
    
    //Test Character ID and Character Name.
    //Must be the character id and character name fo the same character respectively
    private $testid = 9353338328;
    private $testname = "MJ Maverick";
	
    private $ourname;
    private $verbose;
    private $group;
    private $bluegroup;
    private $banner;
    private $spacer;
    
    //ESI Configuration
    private $clientid;
    private $secret;
    
    public function __construct() {
        //Parse the data for the ESI configuration
        $esi = parse_ini_file(__DIR__.'/../configuration/esi.ini');
        $this->clientid = $esi['client_id'];
        $this->secret = $esi['secret'];
        
        //Parse the data for Teamspeak configuration
        $ts = parse_ini_file(__DIR__.'/../configuration/teamspeak.ini');
        $this->tshost = $ts['tshost'];
        $this->tsname = $ts['tsname'];
        $this->tspass = $ts['tspass'];
        $this->tsport = $ts['tsport'];
        $this->tscport = $ts['tscport'];
        $this->tspassword = $ts['tspassword'];
        
        //parse the data for EVEOTS Configuration
        $eveots = parse_ini_file(__DIR__.'/../configuration/eveots.ini');
        $this->admin = $eveots['admin'];
        $this->adminID = $eveots['adminID'];
        $this->ourname = $eveots['ourname'];
        $this->verbose = $eveots['verbose'];
        $this->group = $eveots['group'];
        $this->bluegroup = $eveots['bluegroup'];
        $this->banner = $eveots['banner'];
        $this->spacer = $eveots['spacer'];
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
        if($this->verbose == "true") {
            return true;
        } else {
            return false;
        }
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
            'secretkey' => $this->secret
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