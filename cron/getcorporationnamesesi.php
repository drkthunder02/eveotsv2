<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

//Get corporation names from the known alliances
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$db = DBOpen();

// create a log channel
$log = new Logger('ESI-Corporation');
$log->pushHandler(new StreamHandler('esi-corporation.log', Logger::WARNING));

//Get the EVEOTS configuration so we can call the config parameters for client_id and secretkey for ESI
$conf = new EVEOTS\Config\Config();
$config = $conf->GetESIConfig();

$authentication = new \Seat\Eseye\Containers\EsiAuthentication([
    'client_id' => $config['clientid'],
    'secret' => $config['secretkey']
]);
$esi = new \Seat\Eseye\Eseye($authentication);

$alliances = $db->fetchRowMany('SELECT * FROM Alliances');

//For each alliance, let's get a list of the member corporations
foreach($alliances as $alliance) {
    //Do the ESI Call to get the member corporations
    try {
            $allianceCorps = $esi->invoke('get', '/alliances/{alliance_id}/corporations',[
                'alliance_id' => $alliance['AllianceID']
        ]);
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        // The HTTP Response code and message can be retreived
        // from the exception...
        print $e->getCode() . PHP_EOL;
        print $e->getMessage() . PHP_EOL;
    }
    
    foreach($allianceCorps as $corp) {
        try {
            $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
                'corporation_id' => $corp,
            ]);
        } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
            // The HTTP Response code and message can be retreived
            // from the exception...
            print $e->getCode() . PHP_EOL;
            print $e->getMessage() . PHP_EOL;
        }
        //Information about the corporation to be checked against the database
        $found = $db->fetchRow('SELECT * FROM Corporations WHERE CorporationID= :id', array('id' => $corporation['corporation_id']));
        if($found == false) {
            //Insert the corporation as a new corporation into the database
            $db->insert('Corporations', array(
                'AllianceID' => $corporation['alliance_id'],
                'Corporation' => $corporation['corporation_name'],
                'CorporationID' => $corp,
                'MemberCount' => $corporation['member_count'],
                'Ticker' => $corporation['ticker']
            ));
        } else {
            //Update the details of the corporation
            $db->update('Corporations', array('CorporationID' => $corp), array(
                'AllianceID' => $corporation['alliance_id'],
                'Corporation' => $corporation['corporation_name'],
                'MemberCount' => $corporation['member_count'],
                'Ticker' => $corporation['ticker']
            ));
        }
    }
}