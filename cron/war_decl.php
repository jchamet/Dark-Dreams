<?php
// Connect to Database
try{
mysql_connect("localhost", "713981_admin", "paSsword2334") or die(mysql_error());
mysql_select_db("darkdreamshost_zzl_mydb") or die(mysql_error());
}
catch (Exception $e){
die('Error : ' . $e->getMessage());
}
	
$result0 = mysql_query("SELECT * FROM wars WHERE active=1") or die(mysql_error());
if(!mysql_num_rows($result)){
	// Setup Wars
	$breaker = 0;
	$counter = 0;
	$result3 = mysql_query("SELECT * FROM factions ORDER BY army DESC") or die(mysql_error());
	while ($row3 = mysql_fetch_array( $result3 ) AND $breaker == 0){
		$counter++;
		if ($counter == 1){
			$result = mysql_query("SELECT * FROM factions ORDER BY army DESC LIMIT 0,1") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			$who = $row['id'];
			$who2 = rand(1,4);
			while ($who2 == $who){
				$who2 = rand(1,4);
			}
			mysql_query("INSERT INTO wars (attacker, defender) VALUES('$who', '$who2') ") or die(mysql_error());
		}
		else {
			$result4 = mysql_query("SELECT * FROM factions WHERE id<>'$who' AND id<>'$who2' ORDER BY army DESC LIMIT 0,1") or die(mysql_error());
			$row4 = mysql_fetch_array( $result4 );
			$which = rand(1,2);
			if ($which == 1){
				$who3 = $row4['id'];
				$result7 = mysql_query("SELECT * FROM factions WHERE id<>'$who' AND id<>'$who2' AND id<>'$who3'") or die(mysql_error());
				$row7 = mysql_fetch_array( $result7 );
				$who4 = $row7['id'];
			}
			else {
				$who4 = $row4['id'];
				$result7 = mysql_query("SELECT * FROM factions WHERE id<>'$who' AND id<>'$who2' AND id<>'$who4'") or die(mysql_error());
				$row7 = mysql_fetch_array( $result7 );
				$who3 = $row7['id'];
			}
			mysql_query("INSERT INTO wars (attacker, defender) VALUES('$who3', '$who4') ") or die(mysql_error());
			$breaker = 1;
		}
	}
}

echo'success';

die();

?>