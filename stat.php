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
include('classes/item.php');
include('classes/char.php');


// Item Rankers
echo'<table style="font-size:10pt;">';
$turns = 6;
$defpre = 80;	$defacc = 10;	$defstr = 20;	$deffer = 10;	$defdod = 10;	$defend = 100;	$defvit = 5;	$defdef = 10;
$typedamage = round($turns*$defpre/100*$defacc/100*2.1*(($defstr)*0.75 + ($deffer)*0.50));
$emptydamage = 0;	$emptydefense = 0;
$result = mysql_query("SELECT * FROM items WHERE mtype<>3 ORDER BY mtype,name") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$item = new item($row['id']);
	$pre = ($defpre + $item->stats->PRE)/100;
	$acc = ($defacc + $item->stats->ACC)/100;
	$acc = 2.1*$acc + (1 - $acc);
	$avgdamage = $turns*($pre)*($acc)*(($defstr + $item->stats->STR)*0.75 + ($deffer + $item->stats->FER)*0.50);
	$dod = ($defdod + $item->stats->DOD)/100;
	$vit = ($defvit + $item->stats->VIT);
	$def = ($defdef + $item->stats->DEF)*0.45 - $typedamage;
	$avgdefense = $defend + $item->stats->END + $turns*($dod*($vit) + (1 - $dod)*($vit + $def));
	if($item->id == 0){
		$emptydamage = $avgdamage;
		$emptydefense = $avgdefense;
	}
	$avgdamage -= $emptydamage;	$avgdefense -= $emptydefense;
	$rating = round($avgdamage + $avgdefense);
	if ($rating < 15 or $rating > 20){
		$rating = '<font color=red>'.$rating.'</font>';
	}
	elseif ($rating == 20){
		$rating = '<font color=blue>'.$rating.'</font>';
	}
	else{
		$rating = '<font color=green>'.$rating.'</font>';
	}
	if ($item->id != 0){
		echo '<tr><td>'.$item->id.'</td><td>'.$item->name.'</td><td>a-'.round($avgdamage).'</td><td>d-'.round($avgdefense).'</td><td>o-'.$rating.'</td><td></td></tr>';
	}
}
echo'</table>';

?>