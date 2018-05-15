## Description

EVEOTS V2 is built to work with the ESI API for EVE Online.  Those that need to secure their Teamspeak3 servers can utilize this application to allow certain people or groups to have access.
This application automatically assigns certain groups to certain users based on the settings in the configuration file.

## Installation Guide

* Go into the eveotsv2 directory and run the command "composer install"
* Go into eveotsv2/functions/configuration and modify the ini files
* Load the database.sql file into the sql database
* Setup scheduled tasks or cron for the files in the eveotsv2/cron directory:

*    bot.php (Recommended 5 minute interval)

*    checks/alliancecheck.php (Recommended 1 hour interval)

*    checks/corporationcheck.php (Recommended 1 hour interval)

*    checks/charactercheck.php (Recommended 1 hour interval)

*    checks/characterremoval.php (Recommended 1 hour interval)
