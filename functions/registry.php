<?php

//Autoload Functions
require_once __DIR__.'/../vendor/autoload.php';

//Classes
require_once __DIR__.'/../functions/class/config.php';
require_once __DIR__.'/../functions/class/sessions.php';
require_once __DIR__.'/../functions/class/version.php';
require_once __DIR__.'/../functions/class/esi.php';

//cURL Functions
require_once __DIR__.'/../functions/curl/multicurl.php';

//Database Functions
require_once __DIR__.'/../functions/database/dbclose.php';
require_once __DIR__.'/../functions/database/dbopen.php';

//ESI Functions
require_once __DIR__.'/../functions/esi/storecharacterinfo.php';

//HTML Functions
require_once __DIR__.'/../functions/html/printhtmlheader.php';
require_once __DIR__.'/../functions/html/printssosuccess.php';

//Process Functions
require_once __DIR__.'/../functions/process/format.php';
require_once __DIR__.'/../functions/process/storessodata.php';

//SSO Functions
require_once __DIR__.'/../functions/sso/getfooter.php';
require_once __DIR__.'/../functions/sso/getheader.php';
require_once __DIR__.'/../functions/sso/getssocallbackurl.php';
require_once __DIR__.'/../functions/sso/printssourl.php';
require_once __DIR__.'/../functions/sso/redirecttonew.php';

?>