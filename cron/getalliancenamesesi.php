<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

//Only run once a day
//--------------- Run once a day ----------------
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$db = DBOpen();

// create a log channel
$log = new Logger('ESI-Alliance');
$log->pushHandler(new StreamHandler('esi-alliance.log', Logger::WARNING));

//Get the EVEOTS configuration so we can call the config parameters for client_id and secretkey for ESI
$conf = new EVEOTS\Config\Config();
$config = $conf->GetESIConfig();

$authentication = new \Seat\Eseye\Containers\EsiAuthentication([
    'client_id' => $config['clientid'],
    'secret' => $config['secretkey']
]);
$esi = new \Seat\Eseye\Eseye($authentication);

try {
    $alliances = $esi->invoke('get', '/alliances/');
} catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
    // The HTTP Response code and message can be retreived
    // from the exception...
    print $e->getCode() . PHP_EOL;
    print $e->getMessage() . PHP_EOL;
}
//For each of the returned values, look for it in the database.  If it's not there, then get its public information and log it in the database
foreach($alliances as $alliance) {
    try {
        $esiInfo = $esi->invoke('get', '/alliances/{alliance_id}/',[
            'alliance_id' => $alliance,
        ]);
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        // The HTTP Response code and message can be retreived
        // from the exception...
        print $e->getCode() . PHP_EOL;
        print $e->getMessage() . PHP_EOL;
    }
    //Try to find the alliance information in the database
    $found = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $alliance));
    if($found == false) {
        $db->insert('Alliances', array(
            'Alliance' => $esiInfo['alliance_name'],
            'AllianceID' => $alliance,
            'Ticker' => $esiInfo['ticker']
        ));
    } else {
        if($found['AllianceID'] != $alliance || $found['Alliance'] != $esiInfo['alliance_name'] || $found['Ticker'] != $esiInfo['ticker']) {
            //Update the alliance in the database
            $db->update('Alliances', array('AllianceID' => $found['AllianceID']), array(
                'AllianceID' => $alliance,
                'Alliance' => $esiInfo['alliance_name'],
                'Ticker' => $esiInfo['ticker'],
            ));
        }
    }
}


DBClose($db);