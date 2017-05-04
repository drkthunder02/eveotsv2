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
require_once __DIR__.'/../../functions/database/sqlcheck.php';
require_once __DIR__.'/../../functions/database/sqlchecknames.php';

//Class Functions
require_once __DIR__.'/../../functions/class/sessions.php';

//HTML Functions
require_once __DIR__.'/../functions/html/printadminindexlogin.php';
require_once __DIR__.'/../functions/html/printadmintable.php';
require_once __DIR__.'/../functions/html/printhtmlheader.php';
require_once __DIR__.'/../functions/html/printmainpanel.php';
require_once __DIR__.'/../functions/html/printnavbar.php';
require_once __DIR__.'/../functions/html/printwhitelist.php';
require_once __DIR__.'/../functions/html/printwhitelistform.php';

//Process Functions
require_once __DIR__.'/../functions/process/addlogentry.php';
require_once __DIR__.'/../functions/process/adminchangepassword.php';
require_once __DIR__.'/../functions/process/checksecuritylevel.php';
require_once __DIR__.'/../functions/process/whitelistadd.php';
require_once __DIR__.'/../functions/process/whitelistdelete.php';
