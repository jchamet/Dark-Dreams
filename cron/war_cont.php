<?php
// Connect to Database
try{
mysql_connect("localhost", "713981_admin", "paSsword2334") or die(mysql_error());
mysql_select_db("darkdreamshost_zzl_mydb") or die(mysql_error());
}
catch (Exception $e){
die('Error : ' . $e->getMessage());
}

include('../classes/faction.php');

// Find the Factions
$result0 = mysql_query("SELECT * FROM wars WHERE active=1") or die(mysql_error());
while ($row0 = mysql_fetch_array( $result0 )){
	$id = $row0['id'];
	$casualties = $row0['casualties'];
	$faction1 = new faction($row0['attacker']);
	$faction2 = new faction($row0['attacker']);
	$damage1 = round(rand($faction1->getArmy()/2,$faction1->getArmy())/3);
	if ($faction2->getArmy() <= 0){
		$damage1 = $damage1*2;
	}
	if($faction2->getArmy() - $damage1 >=0){
		$casualties += $damage1;
	}
	else{
		$casualties += $faction2->getArmy();
	}
	$damage2 = round(rand($faction2->getArmy()/2,$faction2->getArmy())/3);
	if ($faction1->getArmy() <= 0){
		$damage2 = $damage2*2;
	}
	if($faction1->getArmy() - $damage2 >=0){
		$casulaties += $damage2;
	}
	else{
		$casualties += $faction1->getArmy();
	}
	
	$faction1->chgArmy(-$damage2);
	$faction1->chgInfluence(-$damage2);
	$faction1->update();
	
	$faction2->chgArmy(-$damage1);
	$faction2->chgInfluence(-$damage1);
	$faction2->update();
	
	mysql_query("UPDATE wars SET casualties='$casualties' WHERE id='$id'") or die(mysql_error());	
}

echo'success';
?>