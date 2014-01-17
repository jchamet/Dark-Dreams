<?php
// ********** This file and all of its containing script is property of James Hamet **********

// Connect to Database
try{
	mysql_connect("localhost", "504810_site5", "paSsword2334") or die(mysql_error());
	mysql_select_db("darkdreams_zzl_mydatabase") or die(mysql_error());
	}
catch (Exception $e){
    die('Error : ' . $e->getMessage());
	}

// Arena Reset
$result = mysql_query("SELECT * FROM arena WHERE validreg=1") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE arena SET validreg=0 WHERE id='$who'") or die(mysql_error());
}

// Daily Tax
$result = mysql_query("SELECT * FROM chars WHERE gold<>0") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	$gold = $row['gold'];
	$gold = $gold - ($gold / 5);
	mysql_query("UPDATE chars SET gold='$gold' WHERE id='$who'") or die(mysql_error());
}

// Daily Tax (Companies)
$result = mysql_query("SELECT * FROM companies WHERE funds>1000") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	$gold = $row['funds'];
	$gold = $gold - ($gold / 5);
	mysql_query("UPDATE companies SET funds='$gold' WHERE id='$who'") or die(mysql_error());
}

// End of War
$wars = 0;
$result0 = mysql_query("SELECT * FROM wars WHERE active=1") or die(mysql_error());
while ($row0 = mysql_fetch_array( $result0 )){
	$wars++;
	if ($wars == 1){
		$attacker1 = $row0['attacker'];
		$result = mysql_query("SELECT * FROM factions WHERE id='$attacker1'") or die(mysql_error());
		$row = mysql_fetch_array( $result );
		$a1influence = $row['influence'];
		$a1wins = $row['wins'];
		$a1losses = $row['losses'];
		$defender1 = $row0['defender'];
		$result = mysql_query("SELECT * FROM factions WHERE id='$defender1'") or die(mysql_error());
		$row = mysql_fetch_array( $result );
		$d1influence = $row['influence'];
		$d1wins = $row['wins'];
		$d1losses = $row['losses'];
		if ($a1influence < $d1influence){
			$a1losses = $a1losses + 1;
			$d1wins = $d1wins + 1;
			mysql_query("UPDATE factions SET losses='$a1losses' WHERE id='$attacker1'") or die(mysql_error());
			mysql_query("UPDATE factions SET wins='$d1wins' WHERE id='$defender1'") or die(mysql_error());
			mysql_query("UPDATE wars SET winner='0' WHERE attacker='$attacker1'") or die(mysql_error());
		}
		else {
			$d1losses = $d1losses + 1;
			$a1wins = $a1wins + 1;
			mysql_query("UPDATE factions SET wins='$a1wins' WHERE id='$attacker1'") or die(mysql_error());
			mysql_query("UPDATE factions SET wins='$d1losses' WHERE id='$defender1'") or die(mysql_error());
			mysql_query("UPDATE wars SET winner='1' WHERE attacker='$attacker1'") or die(mysql_error());
		}
	}
	else {
		$attacker2 = $row0['attacker'];
		$result = mysql_query("SELECT * FROM factions WHERE id='$attacker2'") or die(mysql_error());
		$row = mysql_fetch_array( $result );
		$a2influence = $row['influence'];
		$a2wins = $row['wins'];
		$a2losses = $row['losses'];
		$defender2 = $row0['defender'];
		$result = mysql_query("SELECT * FROM factions WHERE id='$defender2'") or die(mysql_error());
		$row = mysql_fetch_array( $result );
		$d2influence = $row['influence'];
		$d2wins = $row['wins'];
		$d2losses = $row['losses'];
		if ($a2influence < $d1influence){
			$a2losses = $a2losses + 1;
			$d2wins = $d2wins + 1;
			mysql_query("UPDATE factions SET losses='$a2losses' WHERE id='$attacker2'") or die(mysql_error());
			mysql_query("UPDATE factions SET wins='$d2wins' WHERE id='$defender2'") or die(mysql_error());
			mysql_query("UPDATE wars SET winner='0' WHERE attacker='$attacker2'") or die(mysql_error());
		}
		else {
			$d1losses = $d1losses + 1;
			$a1wins = $a1wins + 1;
			mysql_query("UPDATE factions SET wins='$a2wins' WHERE id='$attacker2'") or die(mysql_error());
			mysql_query("UPDATE factions SET wins='$d2losses' WHERE id='$defender2'") or die(mysql_error());
			mysql_query("UPDATE wars SET winner='1' WHERE attacker='$attacker2'") or die(mysql_error());
		}
	}
}
	

die();

?>