<?php
// ********** This file and all of its containing script is property of James Hamet **********

// Connect to Database
try{
	mysql_connect("localhost", "504810_site", "password4") or die(mysql_error());
	mysql_select_db("darkdreams_zzl_mydatabase") or die(mysql_error());
	}
catch (Exception $e){
    die('Error : ' . $e->getMessage());
	}


// Health Regeneration
$result = mysql_query("SELECT * FROM chars") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	$maxhealth = 100 + ($row['level'] - 1) * 10;
	$newhealth = $row['health'] + ($maxhealth / 4);
	if ($newhealth >= $maxhealth){
		$newhealth = $maxhealth;
	}
	mysql_query("UPDATE chars SET health='$newhealth' WHERE id='$who'") or die(mysql_error());
}


// Work Reset
$result = mysql_query("SELECT * FROM chars WHERE job=1") or die(mysql_error());
while ($row = mysql_fetch_array( $result )){
	$who = $row['id'];
	mysql_query("UPDATE chars SET job=0 WHERE id='$who'") or die(mysql_error());
}

die();

?>