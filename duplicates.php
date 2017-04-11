<?php
/*
========== * EVE ONLINE TEAMSPEAK BY MJ MAVERICK * ==========
*/
// PHP debug mode
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);
// Required files
require_once __DIR__.'/functions/registry.php';

$config = new \EVEOTS\Config\Config();


//--------------------------------------------------------------------------------------------------------
?>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<title>Teamspeak 3 Registration</title>
	<style type='text/css'> 
		body {
			background-color: #000;
			font-family: Calibri, Verdana, Geneva, sans-serif;
			color: #C4C4C4;
			text-align: center;
			margin: 0;
		}
		body a:active {
			color:#F9D600;
			text-decoration: none;
			font-weight: bold;
		}
		body a:link, body a:visited {
			color: #FFA600;
			text-decoration: none;
			font-weight: bold;
		}
		.footer {
			text-align: center;
		}
		h3 {
			color: #FFA600;
			text-decoration: none;
			font-weight: bold;
		}
		left {
			float: left;
		}
		right {
			float: right;
		}
		.footer {
			text-align: center;
		}
	</style>
</head>
<body>
<img src="<?php echo $c->banner ?>" border="0"><br />
<div class='left'>
<table border='0' width='1100' align='center'>
<tr><td valign='top'>

<table border='0' width='100%'>
	<tr>
		<td align='center'><hr width='360'><h3>I want to register on 2 computers</h3><hr width='360'></td>
	</tr>
	<tr>
		<td>On the computer you are registered on:</td>
	</tr>
	<tr>
		<td>
			<ol>
				<li>Log into the server.</li>
				<li>At the top of Teamspeak navigate to 'Settings -> Identities'.</li>
				<li>Select your Identity, default is 'Main' if you haven't changed it.</li>
				<li>Click Export, read the warning message. This is deadly serious.</li>
				<li>Click Export after closing the message if a box doesn't automatically pop up.</li>
				<li>Save you Identity to your desktop.</li>
				<li>Email it to yourself or otherwise transfer it to your other computer.</li>
			</ol>
		</td>
	</tr>
	<tr>
		<td>Then on your other computer:</td>
	</tr>
	<tr>
		<td>
			<ol>
				<li>At the top of Teamspeak navigate to 'Settings -> Identities -> Import'.</li>
				<li>Import the file you exported from the first computer.</li>
				<li>If it shows as 'Main_1' then select it and on the right rename it to something appropriate like 'Sev3rance'.</li>
				<li>At the top of Teamspeak navigate to 'Bookmarks -> Manage Bookmarks'.</li>
				<li>Select the Sev3rance server.</li>
				<li>At the bottom click 'More'.</li>
				<li>Select your imported Identity in the Identity drop down box.</li>
				<li>Click 'Apply' then 'Ok'.</li>
				<li>You will now be registered on your other computer when you connect.</li>
			</ol>
		</td>
	</tr>
	<tr>
		<td>Finally (IMPORTANT):</td>
	</tr>
	<tr>
		<td>
			<ol>
				<li>Delete ALL traces of the file you exported unless you are keeping it in a SECURE and SAFE location for backup.</li>
				<li>Remember to also delete it from your e-mail then your 'Deleted' e-mails, leaving no trace.</li>
				<li>NEVER, EVER EVER EVER... EVER! Give out your Identity file. This gives someone the same rights as you in every server you have rights in. NEVER do it. EVER.</li>
			</ol>
		</td>
	</tr>
</table>

</td>
<td valign='top'>

<table border='0' width='100%'>
	<tr>
		<td align='center'><hr width='360'><h3>I have lost my old Teamspeak Identity</h3><hr width='360'></td>
	</tr>
	<tr>
		<td>Log in <a href='https://support.eveonline.com/api/Key/Index' target='_blank'>here</a>:</td>
	</tr>
	<tr>
		<td>
			<ol>
				<li>You can either click "[Create API Key]", create a new EVEOTS API and delete the old one, or the better option of "[Update]" your key and "[generate]" a new Verification Code. Either will verify with us that we are dealing with you.</li>
				<li>Register using your new API details <a href='index.php' target='_blank'>here</a>.</li>
				<li>If it says invalid API don't panic, it can take 24 hours for the API server to be updated but it is usually immediately.</li>
			</ol>
		</td>
	</tr>
</table>

</td></tr>
</table>
<div class='footer'>
	<br />
	<span style='font-size: 11px;'>Teamspeak 3 Registration for EVE Online by <a onClick='CCPEVE.showInfo(1377,935338328)' href='#'>MJ Maverick</a><br />
	Powered by the TS3 PHP Framework & Pheal<br /></span>
	<span style='font-size: 10px;'>EVEOTS <?php echo $v->release ?></span>
</div>
</div>
</body>
