<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

require_once __DIR__.'/../../functions/registry.php';

//Open the database connection
$db = DBOpen();

$url = 'https://esi.tech.ccp.is/latest/alliances/?datasource=tranquility';
$header = 'Accept: application/json';
$useragent = 'EVEOTSv2 Auth';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
$result = curl_exec($ch);
$alliances = json_decode($result, true);

//Cycle through the alliances, and if they are not in the database, then insert the alliance id into the database
foreach($alliances as $alliance) {
    $found = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $alliance));
    if($found == false) {
        $db->insert('Alliances', array('AllianceID' => $alliance));
        $db->insert('ESILogs', array(
            'Time' => gmdate('d.m.Y H:i'),
            'Type' => 'BuildAlliance',
            'Call' => 'buildallianceidesi.php',
            'Entry' => 'Inserted Alliance of ID: ' . $alliance . ' into the database.'
        ));
    }
}

DBClose($db);

?>

