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
	
/*
// Create Inventories
$result = mysql_query("SELECT * FROM chars") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['ownerid'];
	mysql_query("INSERT INTO invents (ownerid) VALUES('$who') ") or die(mysql_error());  
}
*/

// Update Inventories
$result0 = mysql_query("SELECT * FROM chars") or die(mysql_error());
while ($row0 = mysql_fetch_array( $result0 )){
	$who = $row0['ownerid'];
	$result1 = mysql_query("SELECT * FROM inventories WHERE ownerid='$who'") or die(mysql_error());
	while ($row1 = mysql_fetch_array( $result1 )){
		$result = mysql_query("SELECT * FROM inventories WHERE ownerid='$who'") or die(mysql_error());
		while ($row = mysql_fetch_array( $result )){
			if ($row['whichslot'] == 0){
				$item1 = $row['item'];
			}
			else if ($row['whichslot'] == 1){
				$item2 = $row['item'];
			}
			else if ($row['whichslot'] == 2){
				$item3 = $row['item'];
			}
			else if ($row['whichslot'] == 3){
				$item4 = $row['item'];
			}
			else if ($row['whichslot'] == 4){
				$item5 = $row['item'];
			}
		}
		mysql_query("UPDATE invents SET item1='$item1' WHERE ownerid='$who'") or die(mysql_error());
		mysql_query("UPDATE invents SET item2='$item2' WHERE ownerid='$who'") or die(mysql_error());
		mysql_query("UPDATE invents SET item3='$item3' WHERE ownerid='$who'") or die(mysql_error());
		mysql_query("UPDATE invents SET item4='$item4' WHERE ownerid='$who'") or die(mysql_error());
		mysql_query("UPDATE invents SET item5='$item5' WHERE ownerid='$who'") or die(mysql_error());
	}
}


die();

?>