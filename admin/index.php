<?php
session_start();
if (isset($_SESSION["EVEOTSusername"])) {
	header("location:admin_panel.php");
}
?>
<html>
<head>
	<title>EVEOTS Admin Panel</title>
	<link rel="stylesheet" href="admin.css" type="text/css"/>
</head>

<body class="login">
<center><img src="images/banner.png" border="0"></center><br />
<table width="300" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#666666">
	<tr>
		<form name="loginform" method="post" action="login.php">
		<td>
			<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#222222">
			<tr>
				<td colspan="3"><strong>Administrator Login</strong></td>
			</tr>
			<tr>
				<td width="78">Username</td>
				<td width="6">:</td>
				<td width="294"><input name="username" type="text" id="username"></td>
			</tr>
			<tr>
				<td>Password</td>
				<td>:</td>
				<td><input name="password" type="password" id="password"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><input type="submit" name="Submit" value="Login"></td>
			</tr>
			</table>
		</td>
		</form>
	</tr>
</table>
</body>
</html>