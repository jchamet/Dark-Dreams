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

// Setup
$char = new char(3,17);
$char->soul = 47;
$turns = 6;

// Build Ranker
$pre = ($char->getSTAT('PRE'))/100;			$acc = ($char->getSTAT('ACC'))/100;
$acc = ($char->getSTAT('ACC'))/100;			$acc = 2.1*$acc + (1 - $acc);

$bestrating = 0;
$typedamage = 100; //round($turns*80/100*10/100*2.1*((20)*0.75+(10)*0.50));

$result1 = mysql_query("SELECT * FROM items WHERE mtype=1 and id=30 ORDER BY id DESC") or die(mysql_error());
while ($row1 = mysql_fetch_array( $result1 )){
	$char = new char(3,17);
	$char->inventory->obj1 = new item($row1['id']);
	$obj1 = $char->inventory->obj1;
	$result2 = mysql_query("SELECT * FROM items WHERE mtype=1 ORDER BY id DESC") or die(mysql_error());
	while ($row2 = mysql_fetch_array( $result2 )){
		$char = new char(3,17);
		$char->inventory->obj1 = $obj1;
		$char->inventory->obj2 = new item($row2['id']);
		$obj2 = $char->inventory->obj2;
		$result3 = mysql_query("SELECT * FROM items WHERE mtype=1 ORDER BY id DESC") or die(mysql_error());
		while ($row3 = mysql_fetch_array( $result3 )){
			$char = new char(3,17);
			$char->inventory->obj1 = $obj1;
			$char->inventory->obj2 = $obj2;
			$char->inventory->obj3 = new item($row3['id']);
			$obj3 = $char->inventory->obj3;
			$result4 = mysql_query("SELECT * FROM items WHERE mtype=1 ORDER BY id DESC") or die(mysql_error());
			while ($row4 = mysql_fetch_array( $result4 )){
				$char = new char(3,17);
				$char->inventory->obj1 = $obj1;
				$char->inventory->obj2 = $obj2;
				$char->inventory->obj3 = $obj3;
				$char->inventory->obj4 = new item($row4['id']);
				
				$char->loadGear();
				$pre = ($char->stats->PRE)/100;
				if($pre > 1){	$pre = 1; }
				if($pre < 0){	$pre = 0; }
				$acc = ($char->stats->ACC)/100;
				if($acc > 1){	$acc = 1; }
				if($acc < 0){	$acc = 0; }
				$acc = 2.1*$acc + (1 - $acc);
				$avgdamage = $turns*($pre)*($acc)*(($char->stats->STR)*0.75 + ($char->stats->FER)*0.50);
				$dod = ($char->stats->DOD)/100;
				if($dod > 1){	$dod = 1; }
				if($dod < 0){	$dod = 0; }
				$vit = ($char->stats->VIT);
				$def = ($char->stats->DEF)*0.45 - $typedamage;
				$avgdefense = $char->stats->END + $turns*($dod*($vit) + (1 - $dod)*($vit + $def));
				$rating = round($avgdamage + $avgdefense);
				echo $char->inventory->obj1->id.' '.$char->inventory->obj2->id.' '.$char->inventory->obj3->id.' '.$char->inventory->obj4->id.' -> '.$rating.'<br>';
				if($rating > $bestrating and $char->stats->END > 0){
					$bestrating = $rating;
					echo'i<br>';
				}
			}
		}
	}
}
echo '<title>Optima</title> o-'.$bestrating.'<br>';
echo '<br><br>'.$char->getStatSheet();

?>