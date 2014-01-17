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

// Includes
include('classes/char.php');

// Hired Reset
$result = mysql_query("SELECT * FROM companies WHERE hired>0") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE companies SET hired='0' WHERE id='$who'") or die(mysql_error());
}

// Health Regeneration
$result = mysql_query("SELECT * FROM chars") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	$user = new char(3,$who);
	$user->recover();
	$user->update();
}


// Work Reset
$result = mysql_query("SELECT * FROM chars WHERE job>'0'") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE chars SET job=0 WHERE id='$who'") or die(mysql_error());
}

// Default Restock
$result = mysql_query("SELECT * FROM companies WHERE ownerid='0'") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE companies SET quantity=1000 WHERE id='$who'") or die(mysql_error());
}

// Quest Reset
$result = mysql_query("SELECT * FROM chars WHERE questnum<>'0'") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE chars SET questnum='0' WHERE id='$who'") or die(mysql_error());
}

// Mission Reset
$result = mysql_query("SELECT * FROM chars WHERE factionquest<>'0'") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE chars SET factionquest='0' WHERE id='$who'") or die(mysql_error());
}






// Find the Factions
$wars = 0;
$result0 = mysql_query("SELECT * FROM wars WHERE active=1") or die(mysql_error());
while ($row0 = mysql_fetch_array( $result0 )){
	$wars++;
	if ($wars == 1){
		$casualties = $row0['casualties'];
		$attacker1 = $row0['attacker'];
		$result = mysql_query("SELECT * FROM factions WHERE id='$attacker1'") or die(mysql_error());
		$row = mysql_fetch_array( $result );
		$a1influence = $row['influence'];
		$a1army = $row['army'];
		$a1wins = $row['wins'];
		$a1losses = $row['losses'];
		$defender1 = $row0['defender'];
		$result = mysql_query("SELECT * FROM factions WHERE id='$defender1'") or die(mysql_error());
		$row = mysql_fetch_array( $result );
		$d1influence = $row['influence'];
		$d1army = $row['army'];
		$d1wins = $row['wins'];
		$d1losses = $row['losses'];
	}
	else {
		$casualties2 = $row0['casualties'];
		$attacker2 = $row0['attacker'];
		$result = mysql_query("SELECT * FROM factions WHERE id='$attacker2'") or die(mysql_error());
		$row = mysql_fetch_array( $result );
		$a2influence = $row['influence'];
		$a2army = $row['army'];
		$a2wins = $row['wins'];
		$a2losses = $row['losses'];
		$defender2 = $row0['defender'];
		$result = mysql_query("SELECT * FROM factions WHERE id='$defender2'") or die(mysql_error());
		$row = mysql_fetch_array( $result );
		$d2influence = $row['influence'];
		$d2army = $row['army'];
		$d2wins = $row['wins'];
		$d2losses = $row['losses'];
	}
}
	
// Execute Combat
$counter = 0;
$result2 = mysql_query("SELECT * FROM wars WHERE active=1") or die(mysql_error());
while ($row2 = mysql_fetch_array( $result2 )){
	$counter++;
	if ($counter == 1){
		$a1damage = intval(rand($a1army/6,$a1army/4))*4;
		$d1damage = intval(rand($d1army/6,$d1army/4))*4;
		if ($a1army <= 0){
			$d1damage = 2*$d1damage;
		}
		else {
			$a1army = $a1army - $d1damage;
			if ($a1army < 0){
				$a1army = 0;
			}
			mysql_query("UPDATE factions SET army='$a1army' WHERE id='$attacker1'") or die(mysql_error());
		}
		if ($d1army <= 0){
			$a1damage = 2*$a1damage;
		}
		else {
			$d1army = $d1army - $a1damage;
			if ($d1army < 0){
				$d1army = 0;
			}
			mysql_query("UPDATE factions SET army='$d1army' WHERE id='$defender1'") or die(mysql_error());
		}
		$a1influence = $a1influence - $d1damage;
		if ($a1influence < 0){
			$a1influence = 0;
		}
		$d1influence = $d1influence - $a1damage;
		if ($d1influence < 0){
			$d1influence = 0;
		}
		mysql_query("UPDATE factions SET influence='$a1influence' WHERE id='$attacker1'") or die(mysql_error());
		mysql_query("UPDATE factions SET influence='$d1influence' WHERE id='$defender1'") or die(mysql_error());
	}
	else if ($counter == 2){
		$a2damage = intval(rand($a2army/6,$a2army/4))*4;
		$d2damage = intval(rand($d2army/6,$d2army/4))*4;
		if ($a2army <= 0){
			$d2damage = 2*$d2damage;
		}
		else {
			$a2army = $a2army - $d2damage;
			if ($a2army < 0){
				$a2army = 0;
			}
			mysql_query("UPDATE factions SET army='$a2army' WHERE id='$attacker2'") or die(mysql_error());
		}
		if ($d2army <= 0){
			$a2damage = 2*$a2damage;
		}
		else {
			$d2army = $d2army - $a2damage;
			if ($d2army < 0){
				$d2army = 0;
			}
			mysql_query("UPDATE factions SET army='$d2army' WHERE id='$defender2'") or die(mysql_error());
		}
		$a2influence = $a2influence - $d2damage;
		if ($a2influence < 0){
			$a2influence = 0;
		}
		$d2influence = $d2influence - $a2damage;
		if ($d2influence < 0){
			$d2influence = 0;
		}
		mysql_query("UPDATE factions SET influence='$a2influence' WHERE id='$attacker2'") or die(mysql_error());
		mysql_query("UPDATE factions SET influence='$d2influence' WHERE id='$defender2'") or die(mysql_error());
	}	
}

$casualties = $casualties + $a1damage + $d1damage;
mysql_query("UPDATE wars SET casualties='$casualties' WHERE active='1' AND attacker='$attacker1'") or die(mysql_error());
$casualties2 = $casualties2 + $a2damage + $d2damage;
mysql_query("UPDATE wars SET casualties='$casualties2' WHERE active='1' AND attacker='$attacker2'") or die(mysql_error());

// End War
if (isset($attacker1) AND ($a1influence <= 0 OR $d1influence <= 0)){
	mysql_query("UPDATE wars SET active='0' WHERE attacker='$attacker1'") or die(mysql_error());
	if ($a1influence <= 0){
		$a1losses = $a1losses + 1;
		$d1wins = $d1wins + 1;
		mysql_query("UPDATE factions SET losses='$a1losses' WHERE id='$attacker1'") or die(mysql_error());
		mysql_query("UPDATE factions SET wins='$d1wins' WHERE id='$defender1'") or die(mysql_error());
		mysql_query("UPDATE wars SET winner='0' WHERE attacker='$attacker1'") or die(mysql_error());
		mysql_query("UPDATE wars SET active='0' WHERE attacker='$attacker1'") or die(mysql_error());
	}
	else {
		$d1losses = $d1losses + 1;
		$a1wins = $a1wins + 1;
		mysql_query("UPDATE factions SET wins='$a1wins' WHERE id='$attacker1'") or die(mysql_error());
		mysql_query("UPDATE factions SET wins='$d1losses' WHERE id='$defender1'") or die(mysql_error());
		mysql_query("UPDATE wars SET winner='1' WHERE attacker='$attacker1'") or die(mysql_error());
		mysql_query("UPDATE wars SET active='0' WHERE attacker='$attacker1'") or die(mysql_error());
	}
}
if (isset($attacker2) AND ($a2influence <= 0 OR $d2influence <= 0)){
	mysql_query("UPDATE wars SET active='0' WHERE attacker='$attacker2'") or die(mysql_error());
	if ($a2influence <= 0){
		$a2losses = $a2losses + 1;
		$d2wins = $d2wins + 1;
		mysql_query("UPDATE factions SET losses='$a2losses' WHERE id='$attacker2'") or die(mysql_error());
		mysql_query("UPDATE factions SET wins='$d2wins' WHERE id='$defender2'") or die(mysql_error());
		mysql_query("UPDATE wars SET winner='0' WHERE attacker='$attacker2'") or die(mysql_error());
		mysql_query("UPDATE wars SET active='0' WHERE attacker='$attacker2'") or die(mysql_error());
	}
	else {
		$d2losses = $d2losses + 1;
		$a2wins = $a2wins + 1;
		mysql_query("UPDATE factions SET wins='$a2wins' WHERE id='$attacker2'") or die(mysql_error());
		mysql_query("UPDATE factions SET wins='$d2losses' WHERE id='$defender2'") or die(mysql_error());
		mysql_query("UPDATE wars SET winner='1' WHERE attacker='$attacker2'") or die(mysql_error());
		mysql_query("UPDATE wars SET active='0' WHERE attacker='$attacker2'") or die(mysql_error());
	}
}

die();

?>