<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

//Autoload Modules
require_once __DIR__.'/../../vendor/autoload.php';

//Database Functions
require_once __DIR__.'/../../functions/database/dbclose.php';
require_once __DIR__.'/../../functions/database/dbopen.php';

//Class Functions
require_once __DIR__.'/../../functions/class/sessions.php';
require_once __DIR__.'/../../functions/class/esi.php';

//HTML Functions
require_once __DIR__.'/../functions/html/printadminindexlogin.php';
require_once __DIR__.'/../functions/html/printadminlogs.php';
require_once __DIR__.'/../functions/html/printadmintable.php';
require_once __DIR__.'/../functions/html/printhtmlheader.php';
require_once __DIR__.'/../functions/html/printnavbar.php';
require_once __DIR__.'/../functions/html/printwhitelist.php';

//Process Functions
require_once __DIR__.'/../functions/process/addlogentry.php';
require_once __DIR__.'/../functions/process/adminpanelmsg.php';
require_once __DIR__.'/../functions/process/checklogin.php';
require_once __DIR__.'/../functions/process/checksecuritylevel.php';