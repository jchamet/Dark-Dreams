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
include('../classes/company.php');
include('../classes/item.php');

// Artificial Workers
$counter = 0;
while($counter < 3){
	$result = mysql_query("SELECT * FROM companies WHERE hired<4") or die(mysql_error());
	while ($row = mysql_fetch_array( $result )){
		$company = new company($row['id']);
		$item = new item($company->getProduct());
		if($company->getPay() >= $item->itemprice*.1 and rand(0,100) <=  (4 - $company->aspects->hired)*5 and $company->checkSpend($company->getPay()*3)){
			$company->chgFunds(-$company->getPay()*3);
			$company->chgQuantity(3);
		}
	}
	$counter++;
}

// Artificial Buyers
$counter = 0;
while($counter < 3){
	$result = mysql_query("SELECT * FROM companies") or die(mysql_error());
	while ($row = mysql_fetch_array( $result )){
		$company = new company($row['id']);
		$item = new item($company->getProduct());
		if(rand(0,100) <=  (round($item->itemprice*1.2 - $company->getPrice())/10)*5 and $company->getQuantity() > 0){
			$company->chgFunds($company->getPrice());
			$company->chgQuantity(-1);
		}
	}
	$counter++;
}

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
	$user->loadGear();
	$user->recover();
	$user->update();
}

// Work Reset
$result = mysql_query("SELECT * FROM chars") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE chars SET job1=0 WHERE id='$who'") or die(mysql_error());
	mysql_query("UPDATE chars SET job2=0 WHERE id='$who'") or die(mysql_error());
}

// Mission Reset
$result = mysql_query("SELECT * FROM chars WHERE factionquestnum<>'0'") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE chars SET factionquestnum='0' WHERE id='$who'") or die(mysql_error());
}

// Quest Reset
$result = mysql_query("SELECT * FROM chars WHERE questnum<>'0'") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE chars SET questnum='0' WHERE id='$who'") or die(mysql_error());
}

// Passive Faction Rep
$result = mysql_query("SELECT * FROM chars WHERE faction>'0'") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$char = new char(3,$row['id']);
	$char->chgFacRep(100);
	$char->update();
}

echo'success';
?>