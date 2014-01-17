<?php
// Connect to Database
try{
mysql_connect("localhost", "713981_admin", "paSsword2334") or die(mysql_error());
mysql_select_db("darkdreamshost_zzl_mydb") or die(mysql_error());
}
catch (Exception $e){
die('Error : ' . $e->getMessage());
}

// Includes
include('../classes/char.php');
include('../classes/faction.php');
include('../classes/item.php');

// Daily Tax
$result = mysql_query("SELECT * FROM chars WHERE GOLD<>0") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	$gold = $row['GOLD'];
	$gold = round($gold*0.95);
	mysql_query("UPDATE chars SET GOLD='$gold' WHERE id='$who'") or die(mysql_error());
}

// Daily Tax (Companies)
$result = mysql_query("SELECT * FROM companies WHERE funds>1000") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	$gold = $row['funds'];
	$gold = round($gold*0.95);
	mysql_query("UPDATE companies SET funds='$gold' WHERE id='$who'") or die(mysql_error());
}

// Default Restock
$result = mysql_query("SELECT * FROM companies WHERE ownerid='1'") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE companies SET quantity=1000 WHERE id='$who'") or die(mysql_error());
} 

// End of War
$wars = 0;
$result0 = mysql_query("SELECT * FROM wars WHERE active=1") or die(mysql_error());
while ($row0 = mysql_fetch_array( $result0 )){
	$id = $row0['id'];
	$attacker1 = new faction($row0['attacker']);
	$defender1 = new faction($row0['defender']);
	if ($attacker1->getInfluence() < $defender1->getInfluence){
		$attacker1->chgFunds($row0['casualties']*180);
		$defender1->chgFunds($row0['casualties']*320);
		mysql_query("UPDATE wars SET winner='$defender1->id' WHERE id='$id'") or die(mysql_error());
	}
	else {
		$attacker1->chgFunds($row0['casualties']*320);
		$defender1->chgFunds($row0['casualties']*180);
		mysql_query("UPDATE wars SET winner='$attacker1->id' WHERE id='$id'") or die(mysql_error());
	}
	$attacker1->update();
	$defender1->update()
}

// Faction Distributions
$result = mysql_query("SELECT * FROM factions") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$faction = new faction($row['id']);
	if($faction->getMembers() > 0){
		$dist = round($faction->getFunds()*0.30);
		$faction->chgFunds(-$dist);
		$faction->update();
		$cut = round($dist/$faction->getMembers());
		$result1 = mysql_query("SELECT * FROM chars WHERE faction='$faction->id'") or die(mysql_error());
		while ($row1 = mysql_fetch_array( $result )){
			$who = new char(3,$row1['id']);
			$who->chgSTAT('GOLD',$cut);
			$who->update();
		} 	
	}
} 

// Remove old Wars
$result = mysql_query("SELECT * FROM wars WHERE active<>0") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE wars SET active=0 WHERE id='$who'") or die(mysql_error());
}
	
// Recover Factions
$result = mysql_query("SELECT * FROM factions WHERE influence<>1000") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE factions SET influence=1000 WHERE id='$who'") or die(mysql_error());
}
$result = mysql_query("SELECT * FROM factions WHERE army<50") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE factions SET army=50 WHERE id='$who'") or die(mysql_error());
}
	
	
echo'success';

?>