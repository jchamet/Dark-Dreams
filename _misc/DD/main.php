<?php  
// This file and all of its containing script is property of James Hamet.

// Connect to Database
try{
mysql_connect("localhost", "504810_site5", "paSsword2334") or die(mysql_error());
mysql_select_db("darkdreams_zzl_mydatabase") or die(mysql_error());
}
catch (Exception $e){
die('Error : ' . $e->getMessage());
}

// Functions
function clean_string($string) {
		$bad = array("content-type","bcc:","to:","cc:","href");
		return str_replace($bad,"",$string);
	}
function fix_string($string) {
		$string = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$string;
		$string = preg_replace("%\n%", "<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $string);
		$string = preg_replace('#\[color=(red|green|blue|yellow|purple|olive)\](.+)\[/color\]#isU', '<span style="color:$1">$2</span>', $string);
		$string = preg_replace('#\[i\](.+)\[/i\]#isU', '<em>$1</em>', $string);
		$string = preg_replace('#\[b\](.+)\[/b\]#isU', '<b>$1</b>', $string);
		$string = preg_replace('#\[u\](.+)\[/u\]#isU', '<u>$1</u>', $string);
		$string = preg_replace('#http://[a-z0-9._/-]+#i', '<a href="$0">$0</a>', $string);
		$string = preg_replace('#\[img\](.+)\[/img\]#isU', '<img src="http://$1" />', $string);
		$string = preg_replace('#\[table\](.+)\[/table\]#isU', '<table>$1</table>', $string);
		$string = preg_replace('#\[tr\](.+)\[/tr\]#isU', '<tr>$1</tr>', $string);
		$string = preg_replace('#\[td\](.+)\[/td\]#isU', '<td>$1</td>', $string);
		$string = preg_replace('#\[center\](.+)\[/center\]#isU', '<center>$1</center>', $string);
		return $string;
	}
function indent($string){
		$string = '<center><table width=90%><tr><td>'.$string.'</td></tr></table></center>';
		return $string;
	}

// *** Account System ***
if (isset($_GET['logout'])){
	$loggedout = 1;
	$id = $_COOKIE["userid"];
	mysql_query("DELETE FROM sessions WHERE mykey='$id'") or die(mysql_error());
	setcookie("userid", "", time()-3600);
	?><div onload="displayNewScreen('myFrame','main.php?type=1');"></div><?php
}
else if (isset($_GET['euser']) and isset($_GET['epass'])){
	if (isset($_GET['email'])){
		$nuser = htmlspecialchars($_GET['euser']);
		$nemail = htmlspecialchars($_GET['email']);
		$npass = htmlspecialchars($_GET['epass']);
		$ipadd = $_SERVER['REMOTE_ADDR'];
		$nresult1 = mysql_query("SELECT * FROM users WHERE username='$nuser' ") or die(mysql_error());
		$nresult2 = mysql_query("SELECT * FROM users WHERE email='$nemail' ") or die(mysql_error());
		$nresult3 = mysql_query("SELECT * FROM users WHERE ipadd='$ipadd' ") or die(mysql_error());
		if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $nemail)){
			if (mysql_num_rows($nresult1)){
				?><script type="text/javascript">
				alert("The username -<?php echo $nuser; ?>- is already registered.");
				history.back();			
				</script><?php
			}
			else if (mysql_num_rows($nresult2)){
				?><script type="text/javascript">
				alert("The email -<?php echo $nemail; ?>- is already registered.");
				history.back();			
				</script><?php
			}
			else if (mysql_num_rows($nresult3)){
				?><script type="text/javascript">
				alert("The ip address -<?php echo $ipadd; ?>- is already registered.");
				history.back();			
				</script><?php
			}
			else{
				mysql_query("INSERT INTO users (creation, username, email, password, ipadd) VALUES(NOW(), '$nuser', '$nemail', '$npass', '$ipadd') ") or die(mysql_error());  
				$result = mysql_query("SELECT * FROM users WHERE username='$nuser'") or die(mysql_error());
				$row = mysql_fetch_array( $result );
				$ownerid = $row['id'];
				mysql_query("INSERT INTO chars (ownerid, name, creation) VALUES('$ownerid','$nuser', NOW()) ") or die(mysql_error()); 
				mysql_query("INSERT INTO invents (ownerid) VALUES('$ownerid') ") or die(mysql_error());  
				mysql_query("INSERT INTO arena (ownerid) VALUES('$ownerid') ") or die(mysql_error());  
			}
		}
	}
	$euser = htmlspecialchars($_GET['euser']);
	$epass = htmlspecialchars($_GET['epass']);
	$result = mysql_query("SELECT * FROM users WHERE username='$euser'") or die(mysql_error());  
	if (mysql_num_rows($result)){
		$row = mysql_fetch_array( $result );
		if (isset($row['username']) AND $epass == $row['password']){
			$myid = $row['id'];
			$expire = time() + 3600;
			
			$result = mysql_query("SELECT * FROM sessions WHERE ownerid='$myid'") or die(mysql_error()); 
			while($row = mysql_fetch_array( $result )){
				$id = $row['id'];
				mysql_query("DELETE FROM sessions WHERE id='$id'") or die(mysql_error());
			}
			mysql_query("INSERT INTO sessions (ownerid, logtime) VALUES('$myid', NOW()) ") or die(mysql_error());  
			$result = mysql_query("SELECT * FROM sessions WHERE ownerid='$myid'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			$id = md5($row['id']);
			mysql_query("UPDATE sessions SET mykey='$id' WHERE ownerid='$myid'") or die(mysql_error());
			setcookie("userid", $id, $expire);
		}
	}
}
if ((isset($_COOKIE["userid"]) or isset($myid)) and !isset($loggedout)){
	$id = $_COOKIE["userid"];
	$result = mysql_query("SELECT * FROM sessions WHERE mykey='$id'") or die(mysql_error());
	if (mysql_num_rows($result) or isset($myid)){
		$row = mysql_fetch_array( $result );
		$id = $row['ownerid'];
		if (isset($myid)){
			$id = $myid;
		}
		$result = mysql_query("SELECT * FROM users WHERE id='$id'") or die(mysql_error());
		if (mysql_num_rows($result)){
			$row = mysql_fetch_array( $result );
			$myid = $row['id'];
			$myrights = $row['rights'];
			if (isset($myid) and $myrights > 0){
				$result = mysql_query("SELECT * FROM chars WHERE ownerid='$myid'") or die(mysql_error());
				if (mysql_num_rows($result)){
					$charrow = mysql_fetch_array( $result );
					
					$cid = $charrow['id'];
					$cname = $charrow['name'];
					$cgold = $charrow['gold'];
					$cimage = $charrow['image'];
					$clevel = $charrow['level'];
					$cexp = $charrow['experience'];
					$cexpmax = ($clevel + 1) * 10 * intval(($clevel + 11) / 10);
					if ($cexp >= $cexpmax){
						if ($clevel < 40){
							$cexp = $cexp - $cexpmax;
							$clevel++;
							$chealth = 100 + ($clevel - 1) * 10;
							$cexpmax = ($clevel + 1) * 10 * intval(($clevel + 11) / 10);
							mysql_query("UPDATE chars SET health='$chealth' WHERE id='$cid'") or die(mysql_error());
							mysql_query("UPDATE chars SET level='$clevel' WHERE id='$cid'") or die(mysql_error());
						}
						else{
							$cexp = $cexpmax;
						}
						mysql_query("UPDATE chars SET experience='$cexp' WHERE id='$cid'") or die(mysql_error());
					}
					$chealth = $charrow['health'];
					if ($chealth < 0){
						$chealth = 0;
						mysql_query("UPDATE chars SET health='$chealth' WHERE ownerid='$myid'") or die(mysql_error());
					}
					$chealthmax = 100 + ($clevel) * 10;
					if ($chealth >= intval($chealthmax * 1.2)){
						$chealth = intval($chealthmax * 1.2);
						mysql_query("UPDATE chars SET health='$chealth' WHERE ownerid='$myid'") or die(mysql_error());
					}
					if (isset($myid) AND isset($_GET['remove'])){
						$which = (int) $_GET['remove'];
						$result1 = mysql_query("SELECT * FROM invents WHERE ownerid='$myid'") or die(mysql_error());
						$row1 = mysql_fetch_array( $result1 );
						switch($which){
							case 1:
							$item = $row1['item1'];
							mysql_query("UPDATE invents SET item1='0' WHERE ownerid='$myid'") or die(mysql_error());
							break;
							case 2:
							$item = $row1['item2'];
							mysql_query("UPDATE invents SET item2='0' WHERE ownerid='$myid'") or die(mysql_error());
							break;
							case 3:
							$item = $row1['item3'];
							mysql_query("UPDATE invents SET item3='0' WHERE ownerid='$myid'") or die(mysql_error());
							break;
							case 4:
							$item = $row1['item4'];
							mysql_query("UPDATE invents SET item4='0' WHERE ownerid='$myid'") or die(mysql_error());
							break;
							case 5:
							$item = $row1['item5'];
							mysql_query("UPDATE invents SET item5='0' WHERE ownerid='$myid'") or die(mysql_error());
							break;
						}
						$result1 = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
						$row1 = mysql_fetch_array( $result1 );
						$type = $row1['mtype'];
						if ($type == 1){
							$effect = $row1['effect'];
							$chealth = $chealth + $effect;
							mysql_query("UPDATE chars SET health='$chealth' WHERE id='$cid'") or die(mysql_error());
						}
					}
					$eattbonus = 0;
					$edefbonus = 0;
					$result = mysql_query("SELECT * FROM invents WHERE ownerid='$myid'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
					$counter = 0;
					while ($counter <= 5){
						$counter++;
						switch($counter){
							case 1:
							$item = $row['item1'];
							break;
							case 2:
							$item = $row['item2'];
							break;
							case 3:
							$item = $row['item3'];
							break;
							case 4:
							$item = $row['item4'];
							break;
							case 5:
							$item = $row['item5'];
							break;
						}
						$result1 = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
						$row1 = mysql_fetch_array( $result1 );
						if ($row1['mtype'] == 2){
							$cattbonus = $cattbonus + $row1['effect'];
							$cdefbonus = $cdefbonus + $row1['effect2'];
						}
					}
					
					$cdamage = 5 + intval(1.5*($clevel - 1)) + $cattbonus;
					$cdefense = ($clevel - 1) + $cdefbonus;
					
					$cprog = $charrow['questprogress'];
					$ccompany = $charrow['company'];
					$cjob = $charrow['job'];
					$cfaction = $charrow['faction'];
					$cfacrep = $charrow['factionrep'];
					if ($cfacrep > 2000){
						mysql_query("UPDATE chars SET factionrep='2000' WHERE id='$cid'") or die(mysql_error());
					}
					$cfacques = $charrow['factionquest'];
					if ($cfaction != 0){
						$commendations = '<b>Comm.:</b> '.$cfacrep.' / 2000<br />';
					}
					$cquestlimit = $charrow['questnum'];
					
					$result = mysql_query("SELECT * FROM arena WHERE ownerid='$myid'") or die(mysql_error());
					$arenarow = mysql_fetch_array( $result );
					$cenlist = $arenarow['validreg'];
				}
			}
		}
	}
}

if (isset($_GET['sendhelp'])){
	$fromwhere = 'jamiz51@aim.com';
	$email_to = htmlspecialchars(clean_string($_GET['email']));
	$email_subject = "Dark Dream: Forgotten Password";
	$result = mysql_query("SELECT * FROM users WHERE email='$email_to'") or die(mysql_error());
	$row = mysql_fetch_array( $result );
	$final_message = 'Your account credentials: (Username: '.$row['username'].' | Password: '.$row['password'];
	$headers = 'From: '.$fromwhere."\r\n".
	'Reply-To: '.$fromwhere."\r\n" .
	'X-Mailer: PHP/' . phpversion();
	mail($email_to, $email_subject, $fromwhere, $final_message); 
	?><div onload="displayNewScreen('myFrame','main.php?type=1');"></div><?php
}
else if (isset($_GET['nimage']) and $myrights > 0){	
	$cimage = htmlspecialchars($_GET['nimage']);
	mysql_query("UPDATE chars SET image='$cimage' WHERE id='$cid'") or die(mysql_error());
	?><div onload="displayNewScreen('myFrame','main.php?type=1');"></div><?php
}
else if (isset($_GET['insert']) and isset($_GET['ntitle']) and isset($_GET['ncontent']) and $myrights > 1){	
	$cat = htmlspecialchars($_GET['insert']);
	$ntitle = htmlspecialchars($_GET['ntitle']);
	$ncontent = htmlspecialchars($_GET['ncontent']);
	mysql_query("INSERT INTO entrylog (author, title, type, content) VALUES('$myid', '$ntitle', '$cat', '$ncontent') ") or die(mysql_error());  
	?><div onload="displayNewScreen('myFrame','main.php?type=1');"></div><?php
}

// *** Game System ***
if (isset($myid) and $myrights > 0){

	// Market Aspect
	if (isset($_GET['market'])){
		// Create Company
		if (isset($_GET['ncompname'])){
			if ($cgold < 2000){
				?><script type="text/javascript">
				alert("You do not have enough funding!");
				window.location = "http://darkdreams.zzl.org/index.php?play=y";				
				</script><?php
				}
			else {
				$compname = htmlspecialchars($_GET['ncompname']);
				$result = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
				if (mysql_num_rows($result))
					{
					?><script type="text/javascript">
					alert("You already own a company!");
					window.location = "http://darkdreams.zzl.org/index.php?play=y";	
					</script><?php
					}
				else {
					$result = mysql_query("SELECT * FROM companies WHERE brand='$compname'") or die(mysql_error());
					if (mysql_num_rows($result) OR strlen($compname) < 8 OR strlen($compname) > 60){
						?><script type="text/javascript">
						alert("Company name already in use, or is too short (or even too long).\nPlease pick another one.");
						history.back();		
						</script><?php
					}
					else {
						$cgold = $cgold - 2000;
						mysql_query("UPDATE chars SET gold='$cgold' WHERE id='$cid'") or die(mysql_error());
						mysql_query("INSERT INTO companies (ownerid, brand) VALUES('$myid', '$compname') ") or die(mysql_error());
						$result = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
						$row = mysql_fetch_array($result);
						$id = $row['id'];
						mysql_query("UPDATE chars SET company='$id' WHERE ownerid='$myid'") or die(mysql_error());
						?><script type="text/javascript">
						alert("Company successfully created.");
						window.location = "http://darkdreams.zzl.org/index.php?play=y&market=y&manage=y";
						</script><?php
					}
				}
			}
		}
		// Destroy Company
		else if (isset($_GET['destroy'])){
			$result = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
			if (mysql_num_rows($result)){
				$row = mysql_fetch_array($result);
				$cgold = $cgold + $row['funds'];
				mysql_query("UPDATE chars SET gold='$cgold' WHERE ownerid='$myid'") or die(mysql_error());
				mysql_query("UPDATE chars SET company='0' WHERE ownerid='$myid'") or die(mysql_error());
				mysql_query("DELETE FROM companies WHERE ownerid='$myid'") or die(mysql_error());
			}
		}
		// Manage Company
		else if (isset($_GET['type']) AND isset($_GET['quantic']) AND isset($_GET['exchange'])  AND isset($_GET['quanticeq']) AND isset($_GET['price']) AND isset($_GET['pay'])){
			$type = (int) $_GET['type'];
			$exchange = abs((int) $_GET['exchange']);
			$type2 = (int) $_GET['quantic'];
			$impexp = abs((int) $_GET['quanticeq']);
			$price = abs((int) $_GET['price']);
			$pay = abs((int) $_GET['pay']);
			$result = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
			if (mysql_num_rows($result)){
				$row = mysql_fetch_array($result);
				if (($exchange <= $cgold AND $type > 0) OR ($exchange <= $row['funds'] AND $type < 0)){
					if ($type < 0){
						$exchange = -$exchange;
					}
					$cgold = $cgold - $exchange;
					mysql_query("UPDATE chars SET gold='$cgold' WHERE ownerid='$myid'") or die(mysql_error());
					$newfunds = $row['funds'] + $exchange;
					mysql_query("UPDATE companies SET funds='$newfunds' WHERE ownerid='$myid'") or die(mysql_error());
					mysql_query("UPDATE companies SET mprice='$price' WHERE ownerid='$myid'") or die(mysql_error());
					mysql_query("UPDATE companies SET pay='$pay' WHERE ownerid='$myid'") or die(mysql_error());
					if ($impexp != 0){
						$item = $row['product'];
						$quantity = $row['quantity'];
						$profit = $row['profit'];
						$result = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
						$row = mysql_fetch_array($result);
						$price = $row['license'] / 2 * abs($impexp);
						if ($row['mtype'] == 1){
								$price = $price / 30;
							}
						if ($type2 > 0){
							$impexp = -$impexp;
							$price = -($price / 2);
						}
						if ($newfunds - $price >= 0 AND $quantity + $impexp >= 0){
							$quantity = $quantity + $impexp;
							$newfunds = $newfunds - $price;
							$profit = $profit - $price;
							mysql_query("UPDATE companies SET quantity='$quantity' WHERE ownerid='$myid'") or die(mysql_error());
							mysql_query("UPDATE companies SET funds='$newfunds' WHERE ownerid='$myid'") or die(mysql_error());
							mysql_query("UPDATE companies SET profit='$profit' WHERE ownerid='$myid'") or die(mysql_error());
						}
					}
				}
			}
		}
		// Manage Product
		else if (isset($_GET['productchoice'])){
			$item = (int) $_GET['productchoice'];
			$result = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			if ($item != $row['product']){
				$result1 = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
				$row1 = mysql_fetch_array( $result1 );
				$funds = $row['funds'] - $row1['license'];
				$profit = $row['profit'] - $row1['license'];				
				if ($funds > 0){
					mysql_query("UPDATE companies SET product='$item' WHERE ownerid='$myid'") or die(mysql_error());
					mysql_query("UPDATE companies SET funds='$funds' WHERE ownerid='$myid'") or die(mysql_error());
					mysql_query("UPDATE companies SET profit='$profit' WHERE ownerid='$myid'") or die(mysql_error());
					mysql_query("UPDATE companies SET quantity='0' WHERE ownerid='$myid'") or die(mysql_error());
				}
			}
		}
		// Go to Work
		else if ($cjob != 6 AND isset($_GET['worked'])){
			$where = (int) $_GET['worked'];
			$result = mysql_query("SELECT * FROM companies WHERE id='$where'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			if ($row['funds'] >= 0 AND $row['hired'] <= 3){
				$prod = $row['product'];
				$result1 = mysql_query("SELECT * FROM items WHERE id='$prod'") or die(mysql_error());
				$row1 = mysql_fetch_array( $result1 );
				$pay = $row['pay'];
				if ($row1['mtype'] == 1){
					$pay = $pay * 10;
					$quantity = $row['quantity'] + 10;
				}
				else {
					$pay = $pay * 2;
					$quantity = $row['quantity'] + 2;
				}
				$cgold = $cgold + $pay;
				$funds = $row['funds'] - $pay;
				$hired = $row['hired'] + 1;
				$cjob++;
				mysql_query("UPDATE chars SET gold='$cgold' WHERE id='$cid'") or die(mysql_error());
				mysql_query("UPDATE chars SET job='$cjob' WHERE id='$cid'") or die(mysql_error());
				
				mysql_query("UPDATE companies SET funds='$funds' WHERE id='$where'") or die(mysql_error());
				if ($myid != $row['ownerid']){
					$profit = $row['profit'] - $pay;
					mysql_query("UPDATE companies SET profit='$profit' WHERE id='$where'") or die(mysql_error());
				}
				mysql_query("UPDATE companies SET quantity='$quantity' WHERE id='$where'") or die(mysql_error());
				mysql_query("UPDATE companies SET hired='$hired' WHERE id='$where'") or die(mysql_error());
			}
		}
		// Buy Product
		else if (isset($_GET['bought'])){
			$where = (int) $_GET['bought'];
			$result = mysql_query("SELECT * FROM companies WHERE id='$where'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			
			$price = $row['mprice'];
			$funds = $row['funds'] + $price;
			$profit = $row['profit'] + $price;
			$quantity = $row['quantity'] - 1;
			$newgold = $cgold - $price;
			
			if ($newgold >= 0 AND $quantity >= 0){
				$item = $row['product'];
				$result = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
				$row = mysql_fetch_array( $result );
				if ($row['mtype'] == 1){
					$chealth = $chealth + $row['effect'];
					mysql_query("UPDATE chars SET health='$chealth' WHERE id='$cid'") or die(mysql_error());
				}
				else {
					$result = mysql_query("SELECT * FROM invents WHERE ownerid='$myid'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
					$counter = 0;
					while ($counter <= 5){
						$counter++;
						switch($counter){
							case 1:
							$item0 = $row['item1'];
							break;
							case 2:
							$item0 = $row['item2'];
							break;
							case 3:
							$item0 = $row['item3'];
							break;
							case 4:
							$item0 = $row['item4'];
							break;
							case 5:
							$item0 = $row['item5'];
							break;
						}
						if ($item0 == 0){
							switch($counter){
								case 1:
								mysql_query("UPDATE invents SET item1='$item' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 2:
								mysql_query("UPDATE invents SET item2='$item' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 3:
								mysql_query("UPDATE invents SET item3='$item' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 4:
								mysql_query("UPDATE invents SET item4='$item' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 5:
								mysql_query("UPDATE invents SET item5='$item' WHERE ownerid='$myid'") or die(mysql_error());
								break;
							}
							$counter = 6;
						}
					}
				}
				$cgold = $newgold;
				mysql_query("UPDATE chars SET gold='$cgold' WHERE id='$cid'") or die(mysql_error());
				mysql_query("UPDATE companies SET funds='$funds' WHERE id='$where'") or die(mysql_error());
				mysql_query("UPDATE companies SET profit='$profit' WHERE id='$where'") or die(mysql_error());
				mysql_query("UPDATE companies SET quantity='$quantity' WHERE id='$where'") or die(mysql_error());
			}
		}
	}
	// Faction Aspect
	else if (isset($_GET['factions'])){
		if (isset($_GET['party'])){
			$faction = (int) $_GET['party'];
			$result1 = mysql_query("SELECT * FROM factions WHERE id='$faction'") or die(mysql_error());
			$row1 = mysql_fetch_array( $result1 );
			if (isset($_GET['signature'])){
				if ($cgold >= 1000 AND $cfaction == 0 AND $clevel >= 20){
					$sig = htmlspecialchars($_GET['signature']);
					if ($cname == $sig){
						$cgold = $cgold - 1000;
						$funds = $row1['funds'] + 1000;
						mysql_query("UPDATE chars SET gold='$cgold' WHERE ownerid='$myid'") or die(mysql_error());
						mysql_query("UPDATE chars SET faction='$faction' WHERE ownerid='$myid'") or die(mysql_error());
						mysql_query("UPDATE factions SET funds='$funds' WHERE id='$faction'") or die(mysql_error());
						$cfaction = $faction;
					}
					else {
						?><script type="text/javascript">
						alert("Please spell your name correctly.");
						history.back();			
						</script><?php
					}
				}
				else {
					?><script type="text/javascript">
					alert("An error has occured! You either are already in a faction, don't have enough gold to join, or are too low-leveled!");
					history.back();			
					</script><?php
				}
			}
			else if (isset($_GET['factiongear'])){
				$gear = (int) $_GET['factiongear'];
				$result0 = mysql_query("SELECT * FROM items WHERE id='$gear'") or die(mysql_error());
				$row0 = mysql_fetch_array( $result0 );
				if ($cfacrep >= $row0['license']){
					$cfacrep = $cfacrep - $row0['license'];
					mysql_query("UPDATE chars SET factionrep='$cfacrep' WHERE ownerid='$myid'") or die(mysql_error());
					$result = mysql_query("SELECT * FROM invents WHERE ownerid='$myid'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
					$counter = 0;
					while ($counter <= 5){
						$counter++;
						switch($counter){
							case 1:
							$item = $row['item1'];
							break;
							case 2:
							$item = $row['item2'];
							break;
							case 3:
							$item = $row['item3'];
							break;
							case 4:
							$item = $row['item4'];
							break;
							case 5:
							$item = $row['item5'];
							break;
						}
						$result4 = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
						$row4 = mysql_fetch_array( $result4 );
						if ($row4['faction'] != 0){
							switch($counter){
								case 1:
								mysql_query("UPDATE invents SET item1='0' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 2:
								mysql_query("UPDATE invents SET item2='0' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 3:
								mysql_query("UPDATE invents SET item3='0' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 4:
								mysql_query("UPDATE invents SET item4='0' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 5:
								mysql_query("UPDATE invents SET item5='0' WHERE ownerid='$myid'") or die(mysql_error());
								break;
							}
						}
					}
					$result = mysql_query("SELECT * FROM invents WHERE ownerid='$myid'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
					$counter = 0;
					while ($counter <= 5){
						$counter++;
						switch($counter){
							case 1:
							$item = $row['item1'];
							break;
							case 2:
							$item = $row['item2'];
							break;
							case 3:
							$item = $row['item3'];
							break;
							case 4:
							$item = $row['item4'];
							break;
							case 5:
							$item = $row['item5'];
							break;
						}
						if ($item == 0){
							switch($counter){
								case 1:
								mysql_query("UPDATE invents SET item1='$gear' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 2:
								mysql_query("UPDATE invents SET item2='$gear' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 3:
								mysql_query("UPDATE invents SET item3='$gear' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 4:
								mysql_query("UPDATE invents SET item4='$gear' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 5:
								mysql_query("UPDATE invents SET item5='$gear' WHERE ownerid='$myid'") or die(mysql_error());
								break;
							}
							$counter = 6;
						}
					}
				}
				else {
					?><script type="text/javascript">
					alert("Inadequate commendations for the selected item.");
					history.back();			
					</script><?php
				}
			}
			else if (isset($_GET['recruit'])){
				$recruits = abs((int) $_GET['recruit']);
				$commendations = $recruits * 10;
				$price = $recruits * 200;
				if ($cfacrep >= $commendations AND $row1['funds'] >= $price AND $cfaction == $faction){
					$recruits = $row1['army'] + $recruits;
					$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction='$faction'") or die(mysql_error());
					$rows = mysql_fetch_array($result);
					$number = $rows['COUNT(*)'];
					if ($recruits <= (400 + $number * 10)){
						$cfacrep = $cfacrep - $commendations;
						$funds = $row1['funds'] - $price;
						mysql_query("UPDATE chars SET factionrep='$cfacrep' WHERE ownerid='$myid'") or die(mysql_error());
						mysql_query("UPDATE factions SET funds='$funds' WHERE id='$faction'") or die(mysql_error());
						mysql_query("UPDATE factions SET army='$recruits' WHERE id='$faction'") or die(mysql_error());
					}
					else {
						?><script type="text/javascript">
						alert("You cannot overpopulate the barracks!");
						history.back();			
						</script><?php
					}
				}
				else {
					?><script type="text/javascript">
					alert("Either the faction is too poor to afford the additional forces, or you yourself lack the necessary amount of Commendations.");
					history.back();			
					</script><?php
				}
			}
			else if (isset($_GET['recover'])){
				$recover = abs((int) $_GET['recover']);
				$ncommendations = $recover * 15;
				$price = $recover * 100;
				if ($cfacrep >= $ncommendations AND $row1['funds'] >= $price AND $cfaction == $faction AND $recover <= 1000){
					$recover = $row1['influence'] + $recover;
					$cfacrep = $cfacrep - $ncommendations;
					$funds = $row1['funds'] - $price;
					mysql_query("UPDATE chars SET factionrep='$cfacrep' WHERE ownerid='$myid'") or die(mysql_error());
					mysql_query("UPDATE factions SET funds='$funds' WHERE id='$faction'") or die(mysql_error());
					mysql_query("UPDATE factions SET influence='$recover' WHERE id='$faction'") or die(mysql_error());
				}
				else {
					?><script type="text/javascript">
					alert("Either the faction is too poor to make the repairs, or you yourself lack the necessary amount of Commendations. Also note that it is impossible to recover beyond 1000 influence.");
					history.back();			
					</script><?php
				}
			}
			else if (isset($_GET['donate'])){
				$donate = abs((int) $_GET['donate']);
				if ($cgold >= $donate AND $cfaction == $faction){
					$cgold = $cgold - $donate;
					$funds = $row1['funds'] + $donate;
					mysql_query("UPDATE chars SET gold='$cgold' WHERE ownerid='$myid'") or die(mysql_error());
					mysql_query("UPDATE factions SET funds='$funds' WHERE id='$faction'") or die(mysql_error());
				}
				else {
					?><script type="text/javascript">
					alert("You don't have the funding!");
					history.back();			
					</script><?php
				}
			}
		}
		else if (isset($_GET['signature2'])){
			if ($cfaction != 0){
				$sig = htmlspecialchars($_GET['signature2']);
				if ($cname == $sig){
					mysql_query("UPDATE chars SET faction='0' WHERE ownerid='$myid'") or die(mysql_error());
					mysql_query("UPDATE chars SET factionrep='0' WHERE ownerid='$myid'") or die(mysql_error());
					$result = mysql_query("SELECT * FROM invents WHERE ownerid='$myid'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
					$counter = 0;
					while ($counter <= 5){
						$counter++;
						switch($counter){
							case 1:
							$item = $row['item1'];
							break;
							case 2:
							$item = $row['item2'];
							break;
							case 3:
							$item = $row['item3'];
							break;
							case 4:
							$item = $row['item4'];
							break;
							case 5:
							$item = $row['item5'];
							break;
						}
						$result4 = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
						$row4 = mysql_fetch_array( $result4 );
						if ($row4['faction'] != 0){
							switch($counter){
								case 1:
								mysql_query("UPDATE invents SET item1='0' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 2:
								mysql_query("UPDATE invents SET item2='0' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 3:
								mysql_query("UPDATE invents SET item3='0' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 4:
								mysql_query("UPDATE invents SET item4='0' WHERE ownerid='$myid'") or die(mysql_error());
								break;
								case 5:
								mysql_query("UPDATE invents SET item5='0' WHERE ownerid='$myid'") or die(mysql_error());
								break;
							}
						}
					}
				}
				else {
					?><script type="text/javascript">
					alert("Please spell your name correctly.");
					history.back();			
					</script><?php
				}
			}
		}
	}
	
	// *** Situation Setup ***
		// Quest Aspect
		if (isset($_GET['quest'])){
			if (isset($_GET['engage'])){
				$quest = (int) $_GET['engage'];
				$result = mysql_query("SELECT * FROM quests WHERE id='$quest'") or die(mysql_error());
				$row5 = mysql_fetch_array( $result );
				if ($cquestlimit < 5 or $row5['type'] != 1){
					if (isset($_GET['distance'])){
						$distance = (int) $_GET['distance'];
						$tquest = (int) $_GET['tquest'];
					}
					else {
						$distance = $row5['length'];
						$tquest = $row5['id'];
						if ($row5['type'] == 1){
							$cquestlimit++;
							mysql_query("UPDATE chars SET questnum='$cquestlimit' WHERE id='$cid'") or die(mysql_error());
						}
					}
					if (isset($_GET['setup']) or (isset($_GET['mhealth']) and $_GET['mhealth'] <= 0)){
						if (isset($_GET['mhealth']) and $_GET['mhealth'] <= 0){
							$distance = $_GET['distance'] - 1;
						}
						if ($distance == 1 AND $row5['boss'] != 0){
							$id = $row5['boss'];
							$result = mysql_query("SELECT * FROM monsters WHERE id='$id'") or die(mysql_error());
							$row = mysql_fetch_array($result);
						}
						else {
							$id = $row5['minion'];
							$result = mysql_query("SELECT * FROM monsters WHERE id='$id'") or die(mysql_error());
							$row = mysql_fetch_array($result);
						}
						if (isset($id)){
							$monster = $row['id'];
							$mhealth = intval($row['health'] * ($clevel / $row['level']));	
						}
					}
				}
			}
		}
		// Hunt Aspect
		else if (isset($_GET['hunt'])){
			if (isset($_GET['setup'])){
				$class = intval( 1 + $clevel / 5 );
				
				$result = mysql_query("SELECT COUNT(*) FROM monsters WHERE mclass='$class'") or die(mysql_error());
				$validrows = mysql_fetch_array($result);
				$number = $validrows['COUNT(*)'];
					
				$which = rand(1, $number);
				$which0 = $which - 1;	
				$result = mysql_query("SELECT * FROM monsters WHERE mclass='$class' ORDER BY id LIMIT $which0,$which") or die(mysql_error());
				$monsterrow = mysql_fetch_array( $result );
				
				$chance = rand(1, 4);
				$mlevel = $clevel;
				if ($chance == 1 AND $mlevel != 1){
					$mlevel = $clevel - 1;
				}
				else if ($chance == 4){
					$mlevel = $clevel + 1;
				}
				
				$monster = $monsterrow['id'];
				$mhealth = intval($monsterrow['health'] * ( $mlevel / $monsterrow['level'] ));
			}
		}
		// Arena Aspect
		else if (isset($_GET['arena'])){
			// Arena Join
			if (isset($_GET['join'])){
				if ($cenlist == 1){
					$cenlist = 0;
				}
				else {
					$cenlist = 1;			
				}
				mysql_query("UPDATE arena SET validreg='$cenlist' WHERE ownerid='$myid'") or die(mysql_error());
			}
			// Arena Setup
			else if (isset($_GET['setup'])){
				$Bigresult = mysql_query("SELECT * FROM arena WHERE validreg='1' AND ownerid<>'$myid'") or die(mysql_error());
				$rangehigh = intval( ($clevel + 10) / 10 ) * 10 - 5;
				$rangelow = $rangehigh - 5;
				$number = 0;
				while($rows = mysql_fetch_array($Bigresult)){
					$id = $rows['ownerid'];
					$result = mysql_query("SELECT * FROM chars WHERE ownerid='$id'") or die(mysql_error());
					$validrows = mysql_fetch_array($result);
					if ($validrows['level'] <= $rangehigh AND $validrows['level'] >= $rangelow){
						$number++;
					}
				}
					
				if (mysql_num_rows($result) and $number != 0){	
					$impossible = false;
					$which = rand(1, $number);	
					$number = 0;
					$Bigresult2 = mysql_query("SELECT * FROM arena WHERE validreg='1' AND ownerid<>'$myid'") or die(mysql_error());
					while ($rows = mysql_fetch_array($Bigresult2)){
						$id = $rows['ownerid'];
						$result = mysql_query("SELECT * FROM chars WHERE ownerid='$id'") or die(mysql_error());
						$validrows = mysql_fetch_array($result);
						if ($validrows['level'] <= $rangehigh AND $validrows['level'] >= $rangelow){
							$number++;
							if ($number == $which){
								$id = $rows['ownerid'];
								$result = mysql_query("SELECT * FROM chars WHERE ownerid='$id'") or die(mysql_error());
								$validrows = mysql_fetch_array($result);
								$defender = $validrows['id'];
								$mhealth = 100 + ($validrows['level']) * 10;
								
								$eattbonus = 0;
								$edefbonus = 0;
								$result = mysql_query("SELECT * FROM invents WHERE ownerid='$id'") or die(mysql_error());
								$row = mysql_fetch_array( $result );
								$counter = 0;
								while ($counter <= 5){
									$counter++;
									switch($counter){
										case 1:
										$item = $row['item1'];
										break;
										case 2:
										$item = $row['item2'];
										break;
										case 3:
										$item = $row['item3'];
										break;
										case 4:
										$item = $row['item4'];
										break;
										case 5:
										$item = $row['item5'];
										break;
									}
									$result1 = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
									$row1 = mysql_fetch_array( $result1 );
									if ($row1['mtype'] == 2){
										$eattbonus = $eattbonus + $row1['effect'];
										$edefbonus = $edefbonus + $row1['effect2'];
									}
								}
								
								$edamage = 5 + intval(1.5*($elevel - 1)) + $eattbonus;
								$edefense = ($elevel - 1) + $edefbonus;
							}
						}
					}
				}
				else {
					$impossible = true;
				}
			}
		}
		// Mission Aspect
		else if (isset($_GET['mission'])){
			if ($cfacques <= 10){
				$quest = (int) $_GET['mission'];
				$result5 = mysql_query("SELECT * FROM quests WHERE id='$quest'") or die(mysql_error());
				$row5 = mysql_fetch_array( $result5 );
				if (!isset($_GET['distance'])){
					$cfacques++;
					mysql_query("UPDATE chars SET factionquest='$cfacques' WHERE ownerid='$myid'") or die(mysql_error());
					$distance = $row5['length'];
					$party = $row5['partysize'];
					
					$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction='$cfaction' AND ownerid<>'$myid'") or die(mysql_error());
					$validrows = mysql_fetch_array($result);
					$number = $validrows['COUNT(*)'];
					
					// Hero Fighters
					if ($number > 0 AND $party > 1){
						$first = rand(1,$number);
						if ($number > 1 AND $party > 2){
							$second = rand(1,$number);
							while ($first == $second){
								$second = rand(1,$number);
							}
						}
						$breaker = 0;
						$counter = 0;
						$result = mysql_query("SELECT * FROM chars WHERE faction='$cfaction' AND ownerid<>'$myid'") or die(mysql_error());
						while($rows = mysql_fetch_array($result) AND $breaker == 0){
							$counter++;
							if (isset($_GET['ally1type']) AND (!isset($second) OR isset($_GET['ally2type']))){
								$breaker = 1;
							}
							else if ($counter == $first){
								$ally1type = 1;
								$ally1who = $rows['id'];
								$a1health = 100 + ($rows['level']) * 10;
							}
							else if (isset($second) AND $counter == $second){
								$ally2type = 1;
								$ally2who = $rows['id'];
								$a2health = 100 + ($rows['level']) * 10;
							}
						}
					}
					// NPC Fighters
					$NPC = $row1['typicalwarrior'];
					$result = mysql_query("SELECT * FROM monsters WHERE id='$NPC'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
					if (!isset($first) AND $party > 1){
						$ally1type = 2;
						$ally1who = $NPC;
						$a1health = $row['health'];
					}
					if (!isset($second) AND $party > 2){
						$ally2type = 2;
						$ally2who = $NPC;
						$a2health = $row['health'];
					}
				}
				else {
					$distance = (int) $_GET['distance'];
					if (isset($_GET['ally1type'])){
						$ally1type = (int) $_GET['ally1type'];
						$ally1who = (int) $_GET['ally1who'];
						$a1health = (int) $_GET['a1health'];
						if (isset($_GET['ally2type'])){
							$ally2type = (int) $_GET['ally2type'];
							$ally2who = (int) $_GET['ally2who'];
							$a2health = (int) $_GET['a2health'];
						}
					}
				}
				if (isset($ally1type)){			
					if ($ally1type == 1){
						$result = mysql_query("SELECT * FROM chars WHERE id='$ally1who'") or die(mysql_error());
						$row = mysql_fetch_array($result);
						
						$a1owner = $row['ownerid'];
						
						$attbonus = 0;
						$defbonus = 0;
						$result = mysql_query("SELECT * FROM invents WHERE ownerid='$a1owner'") or die(mysql_error());
						$row = mysql_fetch_array( $result );
						$counter = 0;
						while ($counter <= 5){
							$counter++;
							switch($counter){
								case 1:
								$item = $row['item1'];
								break;
								case 2:
								$item = $row['item2'];
								break;
								case 3:
								$item = $row['item3'];
								break;
								case 4:
								$item = $row['item4'];
								break;
								case 5:
								$item = $row['item5'];
								break;
							}
							$result1 = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
							$row1 = mysql_fetch_array( $result1 );
							if ($row1['mtype'] == 2){
								$attbonus = $attbonus + $row1['effect'];
								$defbonus = $defbonus + $row1['effect2'];
							}
						}
						
						$a1damage = 5 + 2*($row['level'] - 1) + $attbonus;
						$a1defense = ($row['level'] - 1) + $defbonus;
					}
					else {
						$result = mysql_query("SELECT * FROM monsters WHERE id='$ally1who'") or die(mysql_error());
						$row = mysql_fetch_array($result);

						$a1damage = $row['damage'];
						$a1defense = $row['defense'];
					}
					if (isset($ally2type)){						
						if ($ally2type == 1){
							$result = mysql_query("SELECT * FROM chars WHERE id='$ally2who'") or die(mysql_error());
							$row = mysql_fetch_array($result);
							
							$a2owner = $row['ownerid'];
							
							$attbonus = 0;
							$defbonus = 0;
							$result = mysql_query("SELECT * FROM invents WHERE ownerid='$a2owner'") or die(mysql_error());
							$row = mysql_fetch_array( $result );
							$counter = 0;
							while ($counter <= 5){
								$counter++;
								switch($counter){
									case 1:
									$item = $row['item1'];
									break;
									case 2:
									$item = $row['item2'];
									break;
									case 3:
									$item = $row['item3'];
									break;
									case 4:
									$item = $row['item4'];
									break;
									case 5:
									$item = $row['item5'];
									break;
								}
								$result1 = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
								$row1 = mysql_fetch_array( $result1 );
								if ($row1['mtype'] == 2){
									$attbonus = $attbonus + $row1['effect'];
									$defbonus = $defbonus + $row1['effect2'];
								}
							}
							
							$a2damage = 5 + 2*($row['level'] - 1) + $attbonus;
							$a2defense = ($row['level'] - 1) + $defbonus;
						}
						else {
							$result = mysql_query("SELECT * FROM monsters WHERE id='$ally2who'") or die(mysql_error());
							$row = mysql_fetch_array($result);

							$a2damage = $row['damage'];
							$a2defense = $row['defense'];
						}
					}
				}
				if ($distance == 1 AND $row5['boss'] != 0){
					$monster = $row5['boss'];
				}
				else {
					$monster = $row5['minion'];
				}
				$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
				$row = mysql_fetch_array($result);
				
				if (isset($_GET['mhealth']) and $_GET['mhealth'] > 0){
					$mhealth = $_GET['mhealth'];
				}
				else {
					$mhealth = $row['health'];
				}
				
				$edamage = $row['damage'];
				$edefense = $row['defense'];
			}
		}

	// *** Action Phases ***
		// Battle Method
		if (isset($_GET['choice']) and $chealth > 0){
			$choice = (int) $_GET['choice'];
			
			if (!isset($monster) and isset($_GET['monster'])){
				$monster = $_GET['monster'];
				$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
				$mrow = mysql_fetch_array($result);
				$edamage = $mrow['damage'];
				$edefense = $mrow['defense'];
			}
			if (!isset($mhealth) and isset($_GET['mhealth'])){
				$mhealth = $_GET['mhealth'];
			}
			if (!isset($mlevel) and isset($_GET['mlevel'])){
				$mlevel = $_GET['mlevel'];
			}
			if (!isset($defender) and isset($_GET['defender'])){
				$defender = $_GET['defender'];
			}
			if (!isset($edamage) and isset($_GET['edamage'])){
				$edamage = $_GET['edamage'];
			}
			if (!isset($edefense) and isset($_GET['edefense'])){
				$edefense = $_GET['edefense'];
			}
			
			$party = 0;
			if (isset($ally1type)){
				$party++;
				if (isset($ally2type)){
					$party++;
				}
			}
			$target = rand(0,$party);
			switch($target){
				case 0:
					$defense = $cdefense;
				break;
				case 1:
					$defense = $a1defense;
				break;
				case 2:
					$defense = $a2defense;
				break;
			}
			
			$extra = ($edamage / rand(1,3));
			if ($cdefense < 0){
				$defense = rand ($defense, 0);
			}
			else {
				$defense = rand (0, $defense);
			}
			$mdamage = $edamage + rand (-$extra, $extra) - $defense;
			if ($mdamage < 0){
				$mdamage = 0;
			}
			
			$counter = 0;
			$tdamage = 0;
			while ($counter <= $party){
				switch($counter){
					case 0:
						$damage = $cdamage;
					break;
					case 1:
						$damage = $a1damage;
						$choice = rand(1,3);
					break;
					case 2:
						$damage = $a2damage;
						$choice = rand(1,3);
					break;
				}
				$extra = ($damage / $choice);
				if ($edefense < 0){
					$defense = rand ($edefense, 0);
				}
				else {
					$defense = rand (0, $edefense);
				}
				$pdamage = $damage + rand (-$extra, $extra) - $defense;
				if ($pdamage < 0){
					$pdamage = 0;
				}
				$tdamage = $tdamage + $pdamage;
				$counter++;
			}
			$mhealth = $mhealth - $tdamage;
			
			switch($target){
				case 0:
					$chealth = $chealth - $mdamage;
					mysql_query("UPDATE chars SET health='$chealth' WHERE id='$cid'") or die(mysql_error());
				break;
				case 1:
					$a1health = $a1health - $mdamage;
				break;
				case 2:
					$a2health = $a2health - $mdamage;
				break;
			}
		}
	
	}
	
?>

<div id="myDiv"><form name="myForm"><table><tr><td>
<div id="profzone"><table height=100% width=100%><tr><td valign="center">

<?php
if (isset($myid)){
	$result = mysql_query("SELECT * FROM invents WHERE ownerid='$myid'") or die(mysql_error());
	$row = mysql_fetch_array( $result );
	$counter = 0;
	while ($counter <= 5){
		$counter++;
		switch($counter){
			case 1:
			$item = $row['item1'];
			break;
			case 2:
			$item = $row['item2'];
			break;
			case 3:
			$item = $row['item3'];
			break;
			case 4:
			$item = $row['item4'];
			break;
			case 5:
			$item = $row['item5'];
			break;
		}
		$result1 = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
		$row1 = mysql_fetch_array( $result1 );
		if ($row1['mtype'] != 0){
			$inven[$counter] = '<font size=2>'.$row1['name'].'</font>';
			$use[$counter] = '<font size=1><a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=1&remove=".$counter."'".');">Remove</a></font>';
		}
		else {
			$inven[$counter] = '<font size=2>(empty slot)</font>';
		}
	}
	$inside = '<table height=100% width=100% ><tr><td valign="center">
		<center><b>'.$cname.' ( Level '.$clevel.' )</b><br/>
		<div id="ProfileBox"><img src="'.$cimage.'" height="160px"/></div>
		<font size=2><a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&modimg=1'".');">Change Image</a></font><br /><br />
		</center>'.indent('<font size=2>
		<b>Health:</b> '.$chealth.' / '.$chealthmax.'<br />
		<b>Experience:</b> '.$cexp.' / '.$cexpmax.'<br />
		<b>Gold:</b> '.$cgold.'<br />'.$commendations.'<br />
		<b>Attack Power:</b> '.$cdamage.'<br />
		<b>Defense Power:</b> '.$cdefense).'<br /><b>Inventory:</b><br />
		<center><table width=90%>
		<tr><td>'.$inven[1].'</td><td>'.$use[1].'</td></tr>
		<tr><td>'.$inven[2].'</td><td>'.$use[2].'</td></tr>
		<tr><td>'.$inven[3].'</td><td>'.$use[3].'</td></tr>
		<tr><td>'.$inven[4].'</td><td>'.$use[4].'</td></tr>
		<tr><td>'.$inven[5].'</td><td>'.$use[5].'</td></tr></table>
		<br /><font size=2>
		<a href="#" onclick="displayNewScreen('."'myFrame','main.php?catalogue=1'".');">Item Catalogue</a><br />
		<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=1&logout=1'".');">Sign Out</a>
		</font></center></td></tr></table>';
	}
else {
	$inside = '<center>Please create a <b>character</b>.</center>';
}
echo $inside;
?>
</td></tr></table></div>
	</td><td>
	<div id="centerzone">
		<table width="100%"><tr><td>
		<center><font size=2>
		<a href="#" onclick="displayNewScreen('myFrame','main.php?type=1');">Newpaper</a> - <a href="#" onclick="displayNewScreen('myFrame','main.php?type=2');">View Map</a> - <a href="#" onclick="displayNewScreen('myFrame','main.php?type=3');">Enter World</a>
		</font></center><hr /></td></tr>
		
		<tr><td height="468px"><div id="midzone">
		<table height=400px width=100%><tr><td valign="center"><center>
<?php

if (isset($_GET['type'])){
	if ($_GET['type'] == '1') {  
		if (isset($_GET['cat'])){
			$cat = (int) $_GET['cat'];
			if (isset($_GET['full'])){
				$full = (int) $_GET['full'];
				$full0 = $full - 1;
				$result1 = mysql_query("SELECT * FROM entrylog WHERE type='$cat' ORDER BY id LIMIT $full0,$full") or die(mysql_error());
				$row1 = mysql_fetch_array( $result1 );
				$author = $row1['author'];
				$result = mysql_query("SELECT * FROM chars WHERE ownerid='$author'") or die(mysql_error());
				$row = mysql_fetch_array( $result );
				$title = '<font size=4><b>'.$row1['title'].'</b></font><br /><font size=2>by '.$row['name'].'</font>';
				$lines = $row1['content'];
			}
			else if (isset($_GET['add'])){
				switch($cat){
					case 1:
					$title = "Developpers' Journal";
					break;
					case 2:
					$title = "Storyline";
					break;
				}
				$lines = '<table><tr><td><b>Title: </b></td><td><input name="ntitle" type="text" value=""></td></tr>
				<tr><td valign="top"><b>Content: </b></td><td><textarea name="ncontent" rows="14" cols="45" style="font-family:calibri" ></textarea></td></tr>
				<tr><td colspan=2><center><button type="button" onclick="var t = myForm.elements['."'ntitle'".'].value;var c = myForm.elements['."'ncontent'".'].value;displayNewScreen('."'myDiv','main.php?insert=".$cat."&ntitle='+t+'&ncontent='+c".');">Post!</button></center></td></tr></table>';
			}
			else {
				$result1 = mysql_query("SELECT * FROM entrylog WHERE type='$cat' ORDER BY id") or die(mysql_error());
				switch($cat){
					case 1:
						$title = "Developpers' Journal";
						break;
					case 2:
						$title = "Storyline";
						break;
				}
				$lines = '<center><table width=40%><tr><td>';
				$counter = 0;
				while($row1 = mysql_fetch_array( $result1 )){
					$counter++;
					$author = $row1['author'];
					$result = mysql_query("SELECT * FROM chars WHERE ownerid='$author'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
					$lines = $lines.'<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=1&cat=".$cat."&full=".$counter."'".');">+ '.$row1['title'].'</a> by '.$row['name'].'<br />';
				}
				$lines = $lines.'</td></tr></table></center>';
			}
			if (isset($_GET['add'])) {
				echo'<center>'.$title.'</center><br />'.$lines;
			}
			else {
				echo'<center>'.$title.'</center><p align="left">'.fix_string($lines).'</p>';
			}
		}
		else {
			$result1 = mysql_query("SELECT * FROM entrylog WHERE type=1 ORDER BY id DESC LIMIT 0,1") or die(mysql_error());
			$row1 = mysql_fetch_array( $result1 );
			$author = $row1['author'];
			$result = mysql_query("SELECT * FROM chars WHERE ownerid='$author'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			$title = $row1['title'].' by '.$row['name'];
			$devjourn = substr(fix_string($row1['content']),0,338).'...';
			
			$result1 = mysql_query("SELECT * FROM entrylog WHERE type=2 ORDER BY id DESC LIMIT 0,1") or die(mysql_error());
			$row1 = mysql_fetch_array( $result1 );
			$author = $row1['author'];
			$result = mysql_query("SELECT * FROM chars WHERE ownerid='$author'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			$title2 = $row1['title'].' by '.$row['name'];
			$storyjourn = substr(fix_string($row1['content']),0,338).'...';
			
			$result1 = mysql_query("SELECT * FROM entrylog WHERE type=3 ORDER BY id DESC LIMIT 0,1") or die(mysql_error());
			$row1 = mysql_fetch_array( $result1 );
			$announcement = $row1['content'];
			
			$add1 = '';
			$add2 = '';
			if ($myrights > 1){
				$add1 = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=1&cat=1&add=1'".');">[ add ]</a> | ';
				$add2 = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=1&cat=2&add=1'".');">[ add ]</a> | ';
			}
			?>
			<center><font size=4><i><b>= Dark Dreams Paper =</b></i></font><br /><br />
			<table width="100%" cellspacing="5" cellpadding="2"><tr>
			<td width="246px" height="254px" valign="top" align="left"><br />
			
			<table width=100% cellpadding="8"><tr><td><center><b>Developpers' Journal</b><br /><?php echo $title; ?><center></td></tr>
			<tr><td height="234px"><?php echo $devjourn; ?></td></tr>
			<tr><td><center><?php echo $add1; ?><a href="#" onclick="displayNewScreen('myFrame','main.php?type=1&cat=1&full=1');">[ read ]</a> | <a href="#" onclick="displayNewScreen('myFrame','main.php?type=1&cat=1');">[ browse ]</a></center></td></tr></table>
			
			</td><td width="1px" bgcolor="#94A0BF"><br /></td>
			<td width="246px" valign="top" align="left"><br />
			
			<table width=100% cellpadding="8"><tr><td><center><b>Story</b><br /><?php echo $title2; ?><center></td></tr>
			<tr><td height="234px"><?php echo $storyjourn; ?></td></tr>
			<tr><td><center><?php echo $add2; ?><a href="#" onclick="displayNewScreen('myFrame','main.php?type=1&cat=2&full=1');">[ read ]</a> | <a href="#" onclick="displayNewScreen('myFrame','main.php?type=1&cat=2');">[ browse ]</a></center></td></tr></table>
			
			</td></tr><tr><td height="5px" colspan="3" bgcolor="#94A0BF"></td></tr>
			<tr><td colspan="3">
			<center>~World Announcements~<font size=1><br /><br /></font><font size=2><?php echo $announcement; ?></font></center>
			</td></tr>
			</table></center> 
			<?php
		}
	}
	else if ($_GET['type'] == '2') {
		echo'Map';
	}
	else if ($_GET['type'] == '3') { 
		if (isset($_GET['modimg'])){
			echo'<table width=80%><tr><td><font size=2>Enter in the URL of the desired avatar image. Accounts with inappropriate avatars will be banned indefinitely.</font></td></tr></table><br />
			<table><tr><td>
			<b>Image: </b></td><td><input name="nimage" type="text" value=""></td><td>
			<button type="button" onclick="var n = myForm.elements['."'nimage'".'].value;displayNewScreen('."'myDiv','main.php?nimage='".'+n);">Submit!</button>
			</td></tr></table>';
		}
		else if (isset($cid)){
			$width=104;
			if (isset($_GET['quest'])){
				if (isset($_GET['engage'])) {
					$view = (int) $_GET['engage'];
					$result = mysql_query("SELECT * FROM quests WHERE id='$view'") or die(mysql_error());
					$row = mysql_fetch_array($result);
					$image = $row['image'];
					if ($mhealth <= 0 and $chealth > 0 and $distance == 0){
						$image = 43;
						$view = $_GET['tquest'];
						$prize = $row5['prize'];
						$cgold = $cgold + $prize;
						mysql_query("UPDATE chars SET gold='$cgold' WHERE ownerid='$myid'") or die(mysql_error());
						$result = mysql_query("SELECT COUNT(*) FROM quests WHERE type='1'") or die(mysql_error());
						$validrows = mysql_fetch_array($result);
						$number = $validrows['COUNT(*)'];
						if ($number > $cprog AND $row5['type'] == 1){
							$cprog++;
							mysql_query("UPDATE chars SET questprogress='$cprog' WHERE ownerid='$myid'") or die(mysql_error());
						}
						$controlpan = '<center><b>Congratulations!</b><br /><br />You earned: '.$prize.' gold</center>';
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&quest=1'".');">Return to Quest Selection</a>';
					}
					else if ($chealth <= 0){
						$image = 4;
						$controlpan = '<center><b>Defeat!</b><br /><br />You were killed in action.</center>';
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&quest=1'".');">Return to Quest Selection</a>';
					}
					else {
						$width = 160;
						$more = -140;
						$bnum = 0;
						if (isset($row['boss']) AND $row['boss'] != 0){
							$bnum = 1;
						}
						$minions = $distance - $bnum;
						$bosses = $bnum;
						$move[1] = 'Slash <font size=2>(Wild slash)</font>';
						$move[2] = 'Maul <font size=2>(Brutal blow)</font>';
						$move[3] = 'Impale <font size=2>(Precision hit)</font>';
						$checked = 1;
						if (isset($_GET['choice'])){
							$checked = (int) $_GET['choice'];
						}
						$number = 1;
						$next = '<center><font size=2>Minions left: '.$minions.' / Bosses left: '.$bosses.'</font><table>';
						while ($number < 4){
							$checkit = '';
							if ($number == $checked){
								$checkit = 'CHECKED';
							}
							$next = $next.'<tr><td><input type="radio" name="attackchoice" onClick="displayNewScreen('."'myDiv','main.php?type=3&quest=1&engage=".$view."&next=1&monster=".$monster."&mhealth=".$mhealth."&next=1&choice=".$number."&distance=".$distance."&tquest=".$view."'".');" '.$checkit.' /></td><td>'.$move[$number].'</td></tr>';
							$number++;
						}	
						$next = $next.'</table></center>';
						
						$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
						$mrow = mysql_fetch_array($result);
						
						$controlpan = '<font size=2><center><b>'.$mrow['name'].'<br /><font size=1>( Class '.$mrow['mclass'].' ; Level '.$clevel.' )</b></font><br/>
									<div id="ProfileBox" style="height:100px;width:100px;" ><img src="'.$mrow['image'].'" width="100%"/></div></center></font><br />
									'.indent('<font size=2><b>Health:</b> '.$mhealth.' / '.intval($mrow['health'] * ($clevel / $mrow['level'])).'<br /><b>Attack Power:</b> '.$mrow['damage'].'<br /><b>Defense Power: </b>'.$mrow['defense'].'</font>').'</center><br />';
					}
				}
				else if (isset($_GET['view'])){
					$view = (int) $_GET['view'];
					$result = mysql_query("SELECT * FROM quests WHERE id='$view'") or die(mysql_error());
					$row = mysql_fetch_array($result);
					$begin = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&quest=1&engage=".$view."&setup=1'".');">Begin Mission!</a>';
					$width = 200;
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&quest=1'".');">Return to Quest Selection</a>';
					$result = mysql_query("SELECT COUNT(*) FROM quests WHERE id<='$view' AND type=1") or die(mysql_error());
					$validrows = mysql_fetch_array($result);
					$number = $validrows['COUNT(*)'];
					if ($row['type'] == 1 AND $number > $cprog){
						$image = 21;
						$controlpan = "<center>Ohaidere.</center>";
					}
					else {
						$image = $row['image'];
						$controlpan = '<center><b>'.$row['title'].'</b><br /><br />'.indent('<font size=2>'.$row['descript'].'</font>').'<br />
						<table width=80%><tr><td><center><font size=2>Monsters:</font><br />'.$row['length'].'</center></td><td>
						<center><font size=2>Prize Money:</font><br />'.$row['prize'].' gold</center></td></tr></table><br />'.$begin.'</center>';
					}
				}
				else {
					$image = 21;
					$width = 200;
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a>';
					$quests = '';
					$squests = '';
					$number = 0;
					$result = mysql_query("SELECT * FROM quests WHERE type='1' ORDER BY id") or die(mysql_error());
					while ($row = mysql_fetch_array($result)){
						$number++;
						$id = $row['id'];
						if ($number == $cprog){
							$quests = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&quest=y&view=".$row['id']."'".');">'.$row['title'].'</a><br />';
						}
					}
					if ($quests == ''){
						$result = mysql_query("SELECT * FROM quests WHERE id='$id'") or die(mysql_error());
						$row = mysql_fetch_array($result);
						$quests = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&quest=y&view=".$row['id']."'".');">'.$row['title'].'</a><br />';
					}
					$result = mysql_query("SELECT * FROM quests WHERE type='3' ORDER BY id") or die(mysql_error());
					while ($row = mysql_fetch_array($result)){
						$squests = $squests.'<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&quest=y&view=".$row['id']."'".');">'.$row['title'].'</a><br />';
					}
					$controlpan = '<div align="left"><font size=1>Oi, there, my friend, have a sit, grab a drink! Let this old soldier tell ye a story... eh? Oh, before we begin, '."I'll".' only tell ye five stories a day, and I '."won't be repeatin'".' any of '."'em.".' So listen up!</font><br /><br /><b>Main Storyline:</b><br />'.indent('<font size=2>'.$quests.'</font>').'<b>Side Stories:</b><br />'.indent('<font size=2>'.$squests.'</font>').'</div>';
				}
			}
			else if (isset($_GET['hunt'])){
				if ($mhealth <= 0 and $chealth > 0){
					$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
					$mrow = mysql_fetch_array($result);
					$expgain = rand(10,20) + $mrow['mclass']*5;
					$profit = rand((($mrow['mclass']) * 20),(100 * intval($mrow['mclass'] + 1)));
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a>';
					$image = 3;
					$cgold = $cgold + $profit;
					$cexp = $cexp + $expgain;
					mysql_query("UPDATE chars SET gold='$cgold' WHERE id='$cid'") or die(mysql_error());
					mysql_query("UPDATE chars SET experience='$cexp' WHERE id='$cid'") or die(mysql_error());
					$controlpan = "Victory!<br />You gained ".$profit." gold and ".$expgain." experience!";			
				}
				else if ($chealth <= 0){
					$image = 4;
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a></center>';
					$controlpan = "<font size=2>You lost the battle!</font>";
				}
				else {
					$image = 2;
					$width = 160;
					$more = -140;
					$move[1] = '<font size=2>Slash <font size=1>(Wild slash)</font></font>';
					$move[2] = '<font size=2>Maul <font size=1>(Brutal blow)</font></font>';
					$move[3] = '<font size=2>Impale <font size=1>(Precision hit)</font></font>';
					$checked = 1;
					if (isset($_GET['choice'])){
						$checked = (int) $_GET['choice'];
					}
					$number = 1;
					$next = '<center><table>';
					while ($number < 4){
						$checkit = '';
						if ($number == $checked){
							$checkit = 'CHECKED';
						}
						$next = $next.'<tr><td><input type="radio" name="attackchoice" onClick="displayNewScreen('."'myDiv','main.php?type=3&hunt=1&monster=".$monster."&mlevel=".$mlevel."&mhealth=".$mhealth."&next=1&choice='+".$number.');"  '.$checkit.' /></td><td>'.$move[$number].'</td></tr>';
						$number++;
					}	
					$next = $next.'</table></center>';
					
					$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
					$mrow = mysql_fetch_array($result);
					
					$controlpan = '<font size=2><center><b>'.$mrow['name'].'<br /><font size=1>( Class '.$mrow['mclass'].' ; Level '.$mlevel.' )</b></font><br/>
								<div id="ProfileBox" style="height:100px;width:100px;" ><img src="'.$mrow['image'].'" width="100%"/></div></center></font><br />
								'.indent('<font size=2><b>Health:</b> '.$mhealth.' / '.intval($mrow['health'] * ($mlevel / $mrow['level'])).'<br /><b>Attack Power:</b> '.$mrow['damage'].'<br /><b>Defense Power: </b>'.$mrow['defense'].'</font>').'</center><br />';
				}
			}
			else if (isset($_GET['arena'])){
				$image = 5;
				if (isset($_GET['explain'])){
					$width = 200;
					$controlpan = "<font size=1>Welcome to the arena, kid, the place to smash skulls if you've got nothin' better to do. If you end up beating a hired Defender, we'll pay you a handsome sum of gold, which should cover the damages. O' course, you can always be a Defender yourself... they get paid for their services, and we cover the damage for 'em. Just a note: they lose money if they lose us battles! Amount of money earned and lost depends on your level.</font><br /><br />".indent('<font size=2>Prize money: '.(100 + $clevel*10).'<br />Defender earnings: '.($clevel*20).' per hour<br />Defender penalty: '.($clevel * 2).' per loss</font>');
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&arena=1'".');">Return to Arena</a>';
				}
				else if (isset($_GET['records'])){
					$image = 8;
					$result = mysql_query("SELECT * FROM arena WHERE ownerid='$myid'") or die(mysql_error());
					$arenarow = mysql_fetch_array( $result );
					$wins = $arenarow['wins'];
					$losses = $arenarow['losses'];
					$total = $wins + $losses;
					$controlpan = "<font size=2>Ah, want to have a look at your personal records, eh? Here, number of matches, wins, and defeats. We include results from defending matches.</font><br /><br />".indent('<font size=2>Wins: '.($wins).'<br />Defeats: '.($losses).'<br />Total Matches: '.($total).'</font>');
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&arena=1'".');">Return to Arena</a>';
				}
				else if ($impossible){
					$image = 7;
					$controlpan = indent("WRAAARG!!!<br /><font size=2>Something's wrong! We don't have any defenders strong enough to fight you!</font>");
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&arena=1'".');">Return to Arena</a>';
				}
				else if (isset($_GET['fight'])){
					if ($mhealth <= 0 or $chealth > 0){
						$result = mysql_query("SELECT * FROM chars WHERE id='$defender'") or die(mysql_error());
						$mrow = mysql_fetch_array($result);
						$rivid = $mrow['ownerid'];
						$result = mysql_query("SELECT * FROM arena WHERE ownerid='$rivid'") or die(mysql_error());
						$erow = mysql_fetch_array($result);
						$result = mysql_query("SELECT * FROM arena WHERE ownerid='$myid'") or die(mysql_error());
						$crow = mysql_fetch_array($result);
					}
					if ($mhealth <= 0 and $chealth > 0){
						$image = 3;
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a></center>';
						
						$expgain = rand( 2 * $clevel, 4 * $clevel);
						$profit = (100 + $clevel*10);
						$mgold = $mrow['gold'] - ($mrow['level'] * 2);
						mysql_query("UPDATE chars SET gold='$mgold' WHERE id='$defender'") or die(mysql_error());
						
						$cgold = $cgold + $profit;
						$cexp = $cexp + $expgain;
						mysql_query("UPDATE chars SET gold='$cgold' WHERE id='$cid'") or die(mysql_error());
						mysql_query("UPDATE chars SET experience='$cexp' WHERE id='$cid'") or die(mysql_error());
						$controlpan = "Victory!<br />You gained ".$profit." gold and ".$expgain." experience!";	
						
						$wins = $crow['wins'] + 1;
						mysql_query("UPDATE arena SET wins='$wins' WHERE ownerid='$myid'") or die(mysql_error());
						$losses = $erow['losses'] + 1;
						mysql_query("UPDATE arena SET losses='$losses' WHERE ownerid='$rivid'") or die(mysql_error());						
					}
					else if ($chealth <= 0){
						$image = 4;
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a></center>';
						$controlpan = "<font size=2>You lost the battle!</font>";
						$losses = $crow['losses'] + 1;
						mysql_query("UPDATE arena SET losses='$losses' WHERE ownerid='$myid'") or die(mysql_error());
						$wins = $erow['wins'] + 1;
						mysql_query("UPDATE arena SET wins='$wins' WHERE ownerid='$rivid'") or die(mysql_error());
					}
					else {
						$image = 6;
						$width = 160;
						$more = -140;
						$move[1] = '<font size=2>Slash <font size=1>(Wild slash)</font></font>';
						$move[2] = '<font size=2>Maul <font size=1>(Brutal blow)</font></font>';
						$move[3] = '<font size=2>Impale <font size=1>(Precision hit)</font></font>';
						$checked = 1;
						if (isset($_GET['choice'])){
							$checked = (int) $_GET['choice'];
						}
						$number = 1;
						$next = '<center><table>';
						while ($number < 4){
							$checkit = '';
							if ($number == $checked){
								$checkit = 'CHECKED';
							}
							$next = $next.'<tr><td><input type="radio" name="attackchoice" onClick="displayNewScreen('."'myDiv','main.php?type=3&arena=1&fight=1&defender=".$defender."&mhealth=".$mhealth."&edamage=".$edamage."&edefense=".$edefense."&next=1&choice=".$number."'".');"  '.$checkit.' /></td><td>'.$move[$number].'</td></tr>';
							$number++;
						}	
						$next = $next.'</table></center>';
						
						$result = mysql_query("SELECT * FROM chars WHERE id='$defender'") or die(mysql_error());
						$mrow = mysql_fetch_array($result);
						
						$controlpan = '<font size=2><center><b>'.$mrow['name'].'<br /><font size=1>( Level '.$mrow['level'].' )</b></font><br/>
									<div id="ProfileBox" style="height:100px;width:100px;" ><img src="'.$mrow['image'].'" width="100%"/></div></center></font><br />
									'.indent('<font size=2><b>Health:</b> '.$mhealth.' / '.(100 + ($mrow['level']) * 10).'<br /><b>Attack Power:</b> '.$edamage.'<br /><b>Defense Power: </b>'.$edefense.'</font>').'</center><br />';
					}
				}
				else {
					$width = 150;
					$about = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&arena=1&explain=1'".');">What is this?</a>';
					$combat = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&arena=1&setup=1&fight=1'".');">Challenge a Defender!</a>';
					$enlist = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&arena=1&join=1'".');">Register as a Defender</a>';
					if ($cenlist != 0){
						$enlist = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&arena=1&join=1'".');">Abandon Position</a>';
					}
					$records = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&arena=1&records=1'".');">View Personal Record</a>';
					
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a>';
					$controlpan = '<b>Battle Arena:</b><br />'.indent('<font size=2>'.$about.'<br/>'.$combat.'<br/>'.$enlist.'</br>'.$records.'<br /></font>');
				}
			}
			else if (isset($_GET['market'])){
				$width = 200;
				if (isset($_GET['manage'])){
					$image = 13;
					$about = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&market=1&explain=1'".');">What is this?</a>';
					$profits = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&market=1&profits=1'".');">Profits</a>';
					$destroy= '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&market=1&destroy=1'".');">Close Down</a>';
					
					$result1 = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
					$row1 = mysql_fetch_array( $result1 );
					$product = $row1['product'];
					$result2 = mysql_query("SELECT * FROM items WHERE id='$product'") or die(mysql_error());
					$row2 = mysql_fetch_array( $result2 );
					$product = $row2['name'];
					
					$funds = 'Funds: '.$row1['funds'].'<br /><select name="etype">
								<option value="1">Deposit</option>
								<option value="-1">Withdraw</option>
								</select>
								<input method=text name="exchange" value=0 size="8" />';
					$trades = '<select name="quantic">
								<option value="-1">Import</option>
								<option value="1">Export</option>
								</select>
								<input method=text name="quanticeq" value=0 size="8" />';
					$production = 'Product: <a href="#" onclick="displayNewScreen('."'myFrame','main.php?products=1'".');"><font size=2>'.$product.'</a></font>';
					$stock = 'Stock: '.$row1['quantity'];
					$price = '<input method=text name="price" value='.$row1['mprice'].' size="8" />';
					$wages = '<input method=text name="pay" value='.$row1['pay'].' size="8" />';
					
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a>';
					$controlpan = '<center>'.$row1['brand'].'</center><font size=2><b>Secretary:</b><br />'.indent($about.'<br />'.$profits.'<br />'.$destroy).
									'<b>Corporate Fund:</b><br />'.indent($funds).
									'<b>Production:</b><br />'.indent($production.'<br />'.$stock.'<br />'.$trades).
									'<b>Product Price:</b><br />'.indent($price).
									'<b>Worker Wages:</b><br />'.indent($wages).
									'</font><center><a href="#" onclick="var e = myForm.elements['."'etype'".'].value;var f = myForm.elements['."'exchange'".'].value;var g = myForm.elements['."'quantic'".'].value;var h = myForm.elements['."'quanticeq'".'].value;var i = myForm.elements['."'price'".'].value;var j = myForm.elements['."'pay'".'].value;displayNewScreen('."'myFrame','main.php?type=3&market=1&manage=1&etype='+e+'&exchange='+f+'&quantic='+g+'&quanticeq='+h+'&price='+i+'&pay='+j".');">Execute Changes</a></center>';
				}
				else if (isset($_GET['explain'])){
					$image = 15;
					$controlpan = "<font size=1>Why, Hello, and welcome to the Company Management screen! It's all real easy to use, so I'm sure my explanation will be more than adequate. First, the Corporate Funds. That's what the company owns. When you sell something, or when someone gets paid, the money leaves and enters in there. Make sure it's plenty full, because people who work for you will need to be paid! If that thing hits zero, you won't be able to hire anyone! Next, Wages, that's how much someone is paid for making the desired item. The Price is what the selling rate is of your product, and finally, in the Production area, we can purchase new licenses and see how many of the desired product we have in stock. Be careful though, switching licenses destroys the previous one, along with all produced products!</font>";
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&market=1&manage=1'".');">Return to Management Screen</a>';
					$width = 200;
				}
				else if (isset($_GET['profits'])){
					$image = 15;
					$width = '300';
					$result1 = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
					$row1 = mysql_fetch_array( $result1 );
					$controlpan = '<center>Profits: '.$row1['profit'].'</center>'.
					indent('<b>Note:<font size=2> The above number shows the sum of all of your sales, minus the worker salaries and licensing fees. Up front fee of 1000 gold and startup funds of 1000 gold are not included. If the number is negative, it is strongly advised that you change your marketing strategy.</font></b>');
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&market=1&manage=1'".');">Return to Management Screen</a>';
				}
				else if (isset($_GET['work'])){
					$image = 11;
					$width=350;
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a>';
					if (isset($_GET['prod'])){
						$prod = (int) $_GET['prod'];
						$result = mysql_query("SELECT * FROM companies WHERE product='$prod' AND hired<4 AND funds>pay ORDER BY pay DESC LIMIT 0,1") or die(mysql_error());
						$row = mysql_fetch_array( $result );
						$id = $row['id'];
						$pay = $row['pay'];
						$company = $row['brand'];
						$result1 = mysql_query("SELECT * FROM items WHERE id='$prod'") or die(mysql_error());
						$row1 = mysql_fetch_array( $result1 );
						if ($row1['mtype'] == 1){
							$pay = $pay * 10;
						}
						else {
							$pay = $pay * 2;
						}
						if ($company == ''){
							$id = '';
							$pay = '0';
							$company = 'No one';
						}
					}
					else {
						$id = '';
						$pay = '0';
						$company = 'No one';
						$work = '';
					}
					if ($id != '' AND $cjob != 6){
						$work = ' <a href="#" onclick="var e = myForm.elements['."'select'".'].value;displayNewScreen('."'myFrame','main.php?type=3&market=1&work=1&worked=".$id."&prod='+e".');">Work</a>';
					}
					else if ($cjob >= 6){
						$work = ' <font size=2>(You already worked)</font>';
					}
					$options = 	'<option value="0">-Select a Product-</option>';
					$class = intval( 1 + $clevel / 5 );
					$result = mysql_query("SELECT * FROM items WHERE makeable='1' AND mclass<='$class' ORDER BY mclass") or die(mysql_error());
					while ($row = mysql_fetch_array( $result )){
						if ($prod == $row['id']){
							$selected =  'selected="yes"';
						}
						else {
							$selected = '';
						}
						$item = $row['id'];
						$result3 = mysql_query("SELECT * FROM companies WHERE product='$item' AND hired<>7") or die(mysql_error());
						if (mysql_num_rows($result3)){
							$options = $options.'<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
						}
					}
					
					$controlpan = '<font size=2>Come to get a job, eh? Note that you can only work <b>6</b> jobs an hour. Also note that you will only be able to fabricate products that your level allows you to use.</font><br /><br />'
								.'Product:<form name="findprod" method="post">
								<select name="select" onChange="displayNewScreen('."'myFrame','main.php?type=3&market=1&work=1&prod='+this.value".');">'.$options.'</select></form>
								<br /><br />
								<center>'.$work.'<br />Best Pay: '.$pay.'</form><br /><br /><font size=2>Offered to you by</font><br />'.$company.'</center>';
				}
				else {
					$image = 10;
					$width='350';
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a>';
					if (isset($_GET['prod'])){
						$prod = (int) $_GET['prod'];
						$result = mysql_query("SELECT * FROM companies WHERE product='$prod' AND quantity>0 ORDER BY mprice LIMIT 0,1") or die(mysql_error());
						$row = mysql_fetch_array( $result );
						$id = $row['id'];
						$price = $row['mprice'];
						$company = $row['brand'];
						if ($company == ''){
							$id = '';
							$price = '0';
							$company = 'No one';
						}
					}
					else {
						$id = '';
						$price = '0';
						$company = 'No one';
						$buy = '';
					}
					$useonspot = '';
					if ($company != 'No one'){
						$result2 = mysql_query("SELECT * FROM items WHERE id='$prod'") or die(mysql_error());
						$row2 = mysql_fetch_array( $result2 );
						if ($row2['mtype'] == 1){
							$useonspot = ' <a href="#" onclick="var e = myForm.elements['."'select'".'].value;displayNewScreen('."'myFrame','main.php?type=3&market=1&buy=1&bought=".$id."&prod='+e".');">Use Now</a>';
						}
						else {
							$useonspot = ' <a href="#" onclick="var e = myForm.elements['."'select'".'].value;displayNewScreen('."'myFrame','main.php?type=3&market=1&buy=1&bought=".$id."&prod='+e".');">Purchase</a>';
						}
					}
					if ($cgold - $price < 0){
						$useonspot = ' <b><font size=2>(Too Expensive)</font></b>';
					}
					$options = 	'<option value="0">-Select a Product-</option>';
					$class = intval( 1 + $clevel / 5 );
					$result = mysql_query("SELECT * FROM items WHERE makeable='1' AND mclass<='$class' ORDER BY mclass") or die(mysql_error());
					while ($row = mysql_fetch_array( $result )){
						if ($prod == $row['id']){
							$selected =  'selected="yes"';
						}
						else {
							$selected = '';
						}
						$item = $row['id'];
						$result3 = mysql_query("SELECT * FROM companies WHERE product='$item' AND quantity<>0") or die(mysql_error());
						if (mysql_num_rows($result3)){
							$options = $options.'<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
						}
					}
								
					$controlpan = '<font size=2>Welcome, one and all, to the great market! Select the product you want to buy, and see if we have it stocked in one of the many player-owned markets! We give you lowest price guaranteed, my friend.</font><br /><br />'
								.'Product:<form name="findprod" method="post">
								<select name="select" onChange="displayNewScreen('."'myFrame','main.php?type=3&market=1&buy=1&prod='+this.value".');">'.$options.'</select></form>
								<br /><br />
								<center>'.$useonspot.'<br />Best Price: '.$price.'</form><br /><br /><font size=2>Offered to you by</font><br />'.$company.'</center>';
				}
			}
			else if (isset($_GET['ccreate'])){
				$maincon = '<center><font size=2>Please make sure that you choose a name that does not violate any of the rules, and is not already taken.	Crudely named companies will be shutdown and all profits, claimed by the admins.</font></center><br /><center>
				<table><tr><td valign="top">
				<label for="ncompname">Company Name</label></td>
				<td valign="top"><input  type="text" name="ncompname" maxlength="20" size="40"></td></tr>
				<tr><td colspan="2" style="text-align:center">
				<button type="button" onclick="var e = myForm.elements['."'ncompname'".'].value;displayNewScreen('."'myDiv','main.php?type=3&manage=1&market=1&ncompname='+e".');">Create Company</button></td></tr>
				</table></center><b>Note:<font size=2> Founding and maintaining a company costs a lot of gold. To start a company, one must pay 1000 gold up front, deposit an additional 1000 gold as startup funds, and purchase for the license necessary for the production of the desired goods. Also note that you will need to hire other characters as employees, and that they will need salaries.</font></b>';
			}
			else if (isset($_GET['factions'])){
				if (isset($_GET['explain'])){
					$image = 29;
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1'".');">Return to the Entity Council</a></center>';	
					$controlpan = "<font size=2>Why hello there! If you're looking to join a faction, you must be at least level <b>20</b>, and willing to pay the entry fee of <b>1000 gold</b>. Try talking to some of the players about factions. They know more than I do.</font>";
				}
				else if (isset($_GET['warsnow'])){
					$image = 29;
					$width = 180;
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1'".');">Return to the Entity Council</a></center>';	
					$counter = 0;
					$result = mysql_query("SELECT * FROM wars WHERE active=1 ORDER BY id") or die(mysql_error());
					while ($row = mysql_fetch_array( $result )){
						$counter++;
						if ($counter == 1){
							$winner1 = $row['winner'];
							$attacker1 = $row['attacker'];
							$result333 = mysql_query("SELECT * FROM factions WHERE id='$attacker1'") or die(mysql_error());
							$row333 = mysql_fetch_array( $result333 );
							$attacker1 = $row333['name'];
							$defender1 = $row['defender'];
							$result333 = mysql_query("SELECT * FROM factions WHERE id='$defender1'") or die(mysql_error());
							$row333 = mysql_fetch_array( $result333 );
							$defender1 = $row333['name'];
							$casualties1 = $row['casualties'];
						}
						else {
							$winner2 = $row['winner'];
							$attacker2 = $row['attacker'];
							$result333 = mysql_query("SELECT * FROM factions WHERE id='$attacker2'") or die(mysql_error());
							$row333 = mysql_fetch_array( $result333 );
							$attacker2 = $row333['name'];
							$defender2 = $row['defender'];
							$result333 = mysql_query("SELECT * FROM factions WHERE id='$defender2'") or die(mysql_error());
							$row333 = mysql_fetch_array( $result333 );
							$defender2 = $row333['name'];
							$casualties2 = $row['casualties'];
						}
					}
					$controlpan = '<center><b>Current Wars</b></center>
									  <b>First War:</b><br />'.indent('<font size=2>Attacker: '.$attacker1.'<br/>Defender: '.$defender1.'</br>Casualties: '.$casualties1.'</font>')
									  .'<br />
									  <b>Second War:</b><br />'.indent('<font size=2>Attacker: '.$attacker2.'<br/>Defender: '.$defender2.'</br>Casualties: '.$casualties2.'</font>');
				}
				else if (isset($_GET['awars'])){
					$image = 29;
					$width = 200;
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1'".');">Return to the Entity Council</a></center>';	
					$counter = 0;
					$result = mysql_query("SELECT * FROM wars ORDER BY id DESC LIMIT 0,2") or die(mysql_error());
					while ($row = mysql_fetch_array( $result )){
						$counter++;
						if ($counter == 1){
							$winner1 = $row['winner'];
							$attacker1 = $row['attacker'];
							$result333 = mysql_query("SELECT * FROM factions WHERE id='$attacker1'") or die(mysql_error());
							$row333 = mysql_fetch_array( $result333 );
							$attacker1 = $row333['name'];
							$defender1 = $row['defender'];
							$result333 = mysql_query("SELECT * FROM factions WHERE id='$defender1'") or die(mysql_error());
							$row333 = mysql_fetch_array( $result333 );
							$defender1 = $row333['name'];
							$casualties1 = $row['casualties'];
							if ($winner1 == 1){
								$winner1 = $attacker1;
							}
							else {
								$winner1 = $defender1;
							}
						}
						else {
							$winner2 = $row['winner'];
							$attacker2 = $row['attacker'];
							$result333 = mysql_query("SELECT * FROM factions WHERE id='$attacker2'") or die(mysql_error());
							$row333 = mysql_fetch_array( $result333 );
							$attacker2 = $row333['name'];
							$defender2 = $row['defender'];
							$result333 = mysql_query("SELECT * FROM factions WHERE id='$defender2'") or die(mysql_error());
							$row333 = mysql_fetch_array( $result333 );
							$defender2 = $row333['name'];
							$casualties2 = $row['casualties'];
							if ($winner2 == 1){
								$winner2 = $attacker2;
							}
							else {
								$winner2 = $defender2;
							}
						}
					}
					$controlpan = '<center><b>Previous Wars</b></center>
									  <b>First War:</b><br />'.indent('<font size=2>Attacker: '.$attacker1.'<br/>Defender: '.$defender1.'<br/>Winner: '.$winner1.'</br>Casualties: '.$casualties1.'</font>')
									  .'<br />
									  <b>Second War:</b><br />'.indent('<font size=2>Attacker: '.$attacker2.'<br/>Defender: '.$defender2.'<br/>Winner: '.$winner2.'</br>Casualties: '.$casualties2.'</font>');
				}
				else if (isset($_GET['party'])){
					$width = 120;
					$faction = (int) $_GET['party'];
					$result1 = mysql_query("SELECT * FROM factions WHERE id='$faction'") or die(mysql_error());
					$row1 = mysql_fetch_array( $result1 );
					if (isset($_GET['palace'])){
						$image = 33 + $faction;
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."'".');">Return to the Outskirts</a>';	
						$legacy = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&legacy=1'".');">The Legacy</a>';	
						if ($cfaction == $faction){
							$result = mysql_query("SELECT * FROM wars WHERE active=1 and ( attacker='$faction' or defender='$faction' )") or die(mysql_error());
							$attack = '';
							if (mysql_num_rows($result)){	
								$attack = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&attack=1'".');"><b>Launch Attack!</b></a><br />';
							}
							$teamquests = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1'".');">Team Quests</a>';
							$contribute = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&contribute=1'".');">Contribute</a>';
							$armory = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&armory=1'".');">Faction Armory</a>';
							$barracks = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&barracks=1'".');">Faction Barracks</a>';
							$workshop = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&workshop=1'".');">Faction Workshop</a>';
							$quit = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&leave=1'".');">Leave Faction</a>';
							$controlpan = '<center><b>'.$row1['name'].'</b></center><br />'
							.'<b>Palace Options:</b>'.indent('<font size=2>'.$legacy.'<br />'.$attack.$teamquests.'<br />'.$contribute.'<br />'.$armory.'<br />'.$barracks.'<br />'.$workshop.'<br />'.$quit.'</font>');
						}
						else if ($cfaction == 0){
							$join = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&join=1'".');">Join Faction</a>';	
							$controlpan = '<center><b>'.$row1['name'].'</b></center><br />'
										.'<b>Palace Options:</b>'.indent('<font size=2>'.$legacy.'<br />'.$join.'</font>');
						}
					}
					else if (isset($_GET['attack'])){
						$result = mysql_query("SELECT * FROM wars WHERE active=1") or die(mysql_error());
						while($row = mysql_fetch_array( $result )){
							if ($row['attacker'] == $cfaction){
								$enemy = $row['defender'];
							}
							else if ($row['defender'] == $cfaction){
								$enemy = $row['attacker'];
							}
						}
						$result = mysql_query("SELECT * FROM factions WHERE id='$enemy'") or die(mysql_error());
						$row = mysql_fetch_array( $result );
						$image = $row['imageid'];
						$enemy = $row['name'];
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
						$controlpan = '<center><font size=2><i>Launch an Attack against the '.$enemy.'!</i></font></center>';
					}
					else if (isset($_GET['quests'])){
						if (isset($_GET['fquest'])){
							$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1'".');">Return to Mission Selection</a>';							
							$quest = (int) $_GET['fquest'];
							$result = mysql_query("SELECT * FROM quests WHERE id='$quest'") or die(mysql_error());
							$row = mysql_fetch_array( $result );
							$image = $row['image'];
							$begin = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1&mission=".$quest."'".');">Begin Mission!</a>';
							$controlpan = '<font size=2><center>'.$row['title'].'</center><br /><br />'.$row['descript'].'<br /><br /><b>Party Size:</b> '.$row['partysize'].'<br /><b>Duration:</b> '.$row['length'].' battles</font><br /><br /><center>'.$begin.'</center>';
						}
						else if ($chealth <= 0 or (isset($_GET['mission']) and $cfacques > 10)){
							$image = 4;
							$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a></center>';
							$controlpan = '<font size=2>You are too weak to go on, or you have exceeded your limit of quests per hour. Mission failed.</font>';
						}
						else if (isset($_GET['distance']) and $_GET['distance'] == 0){
							$mission = (int) $_GET['mission'];
							$result = mysql_query("SELECT * FROM quests WHERE id='$mission'") or die(mysql_error());
							$row = mysql_fetch_array( $result );
							$prize = $row['prize'];
							$controlpan = '<center><b>We have victory!</b><br /><br /><table width=90%><tr><td><font size=2>Commendations Earned:</font></td><td>'.$prize.'</td></tr>
							<tr><td><font size=2>Experience Earned:</font></td><td>10</td></tr></table></center>';
							$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to the Palace</a>';
							$image = 43;
							$width = 160;
							$cfacrep = $cfacrep + $prize;
							mysql_query("UPDATE chars SET factionrep='$cfacrep' WHERE ownerid='$myid'") or die(mysql_error());
							$cexp = $cexp + 10;
							mysql_query("UPDATE chars SET experience='$cexp' WHERE ownerid='$myid'") or die(mysql_error());
						}
						else if (isset($_GET['mission'])){

							if (isset($a1health) and $a1health > 0){
								$location = '&ally1type='.$ally1type.'&ally1who='.$ally1who.'&a1health='.$a1health;
								if (isset($a2health) and $a2health > 0){
									$location = $location.'&ally2type='.$ally2type.'&ally2who='.$ally2who.'&a2health='.$a2health; 
								}
							}
							if (isset($mhealth) and $mhealth <= 0){
								$distance--;
							}
							
								
							$result = mysql_query("SELECT * FROM quests WHERE id='$quest'") or die(mysql_error());
							$row = mysql_fetch_array( $result );
							$image = $row['image'];
							$bnum = 0;
							if (isset($row['boss']) AND $row['boss'] != 0){
								$bnum = 1;
							}
							$minions = $distance - $bnum;
							$bosses = $bnum;
							$progress = '<center>Minions left: '.$minions.' / Bosses left: '.$bosses.'</center>';
							
							$width = 160;
							$more = -140;
							$move[1] = '<font size=2>Slash <font size=1>(Wild slash)</font></font>';
							$move[2] = '<font size=2>Maul <font size=1>(Brutal blow)</font></font>';
							$move[3] = '<font size=2>Impale <font size=1>(Precision hit)</font></font>';
							$checked = 1;
							if (isset($_GET['choice'])){
								$checked = (int) $_GET['choice'];
							}
							$number = 1;
							$next = '<center><table>';
							while ($number < 4){
								$checkit = '';
								if ($number == $checked){
									$checkit = 'CHECKED';
								}
								$next = $next.'<tr><td><input type="radio" name="attackchoice" onClick="displayNewScreen('."'myDiv','main.php?type=3&factions=y&party=".$faction.'&quests=y&mission='.$quest.'&distance='.$distance.'&monster='.$monster.'&mhealth='.$mhealth.$location."&choice=".$number."'".');"  '.$checkit.' /></td><td>'.$move[$number].'</td></tr>';
								$number++;
							}	
							$next = $next.'</table></center>';
					 
							$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
							$mrow = mysql_fetch_array($result);
							
							$controlpan = '<font size=2><center><b>'.$mrow['name'].'<br /><font size=1>( Class '.$mrow['mclass'].' ; Level '.$mrow['level'].' )</b></font><br/>
										<div id="ProfileBox" style="height:100px;width:100px;" ><img src="'.$mrow['image'].'" width="100%"/></div></center></font><br />
										'.indent('<font size=2><b>Health:</b> '.$mhealth.' / '.$mrow['health'].'<br /><b>Attack Power:</b> '.$mrow['damage'].'<br /><b>Defense Power: </b>'.$mrow['defense'].'</font>').'</center><br />';
							
							if (isset($a1health) and $a1health > 0){
								if ($ally1type == 1){
									$result = mysql_query("SELECT * FROM chars WHERE id='$ally1who'") or die(mysql_error());
									$mrow = mysql_fetch_array($result);
									$a1maxhealth = 100 + ($mrow['level']) * 10;
								}
								else {
									$result = mysql_query("SELECT * FROM monsters WHERE id='$ally1who'") or die(mysql_error());
									$mrow = mysql_fetch_array($result);
									$a1maxhealth = $mrow['health'];
									$a1damage = $mrow['damage'];
									$a1defense = $mrow['defense'];
								}
								$allies = '<center><b>'.$mrow['name'].'</b><br/>
										<div id="ProfileBox" style="height:60px;width:80px;"><img src="'.$mrow['image'].'"/></div></center><br /><font size=2>
										<b>Health:</b> '.$a1health.' / '.$a1maxhealth.'<br />
										<b>Attack Power:</b> '.$a1damage.'<br />
										<b>Defense Power:</b> '.$a1defense.'</font>';
								if (isset($a2health) and $a2health > 0){
									if ($ally2type == 1){
										$result = mysql_query("SELECT * FROM chars WHERE id='$ally2who'") or die(mysql_error());
										$mrow = mysql_fetch_array($result);
										$a2maxhealth = 100 + ($mrow['level']) * 10;
									}
									else {
										$result = mysql_query("SELECT * FROM monsters WHERE id='$ally2who'") or die(mysql_error());
										$mrow = mysql_fetch_array($result);
										$a1maxhealth = $mrow['health'];
										$a1damage = $mrow['damage'];
										$a1defense = $mrow['defense'];
									}
									$allies = $allies.'<hr><center><b>'.$mrow['name'].'</b><br/>
										<div id="ProfileBox" style="height:60px;width:80px;"><img src="'.$mrow['image'].'"/></div></center><br /><font size=2>
										<b>Health:</b> '.$a2health.' / '.$a2maxhealth.'<br />
										<b>Attack Power:</b> '.$a2damage.'<br />
										<b>Defense Power:</b> '.$a2defense.'</font>';
								}
							}
						}
						else{
							$image = 25;
							$width = 260;
							$faction = $cfaction;
							$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to the Palace</a>';	
							$rescue = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1&fquest=1'".');">Rescue Mission</a>';
							$seize = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1&fquest=2'".');">Seize a Temple</a>';
							$boss = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1&fquest=3'".');">Kill a Warbeast</a>';
							$controlpan = '<font size=2>Would you believe it if I told you that the local blasphemers were once more causing us trouble? This must stop!<br /><br /><b>(Note: quests reward Faction Commendations. You can only attempt up to 10 missions per hour.)</b></font>'.'<br /><br />'.indent('<font size=2>'.$rescue.'<br />'.$seize.'<br />'.$boss.'</font>').'<br /><center>'.(10 - $cfacques).' Attempts Remaining</center>';
						}
					}
					else if (isset($_GET['armory'])){
						$image = 42;
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
						$controlpan = '<center><b>'.$row1['name'].' Armory</b></center><br /><table width=90%><tr><td>
									<table width=80%><tr><td><center><table width=340px><tr><th>Item</th><th>Details</th><th>Cost</th></tr>';
						
						$checked = '';
						$result3 = mysql_query("SELECT * FROM invents WHERE ownerid='$myid'") or die(mysql_error());
						$row3 = mysql_fetch_array( $result3 );
						$counter = 0;
						while ($counter <= 5){
							$counter++;
							switch($counter){
								case 1:
								$item = $row3['item1'];
								break;
								case 2:
								$item = $row3['item2'];
								break;
								case 3:
								$item = $row3['item3'];
								break;
								case 4:
								$item = $row3['item4'];
								break;
								case 5:
								$item = $row3['item5'];
								break;
							}
							$result4 = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
							$row4 = mysql_fetch_array( $result4 );
							if ($row4['faction'] == $faction){
								$checked = $row4['id'];
								$counter = 6;
							}
						}
						
						$result = mysql_query("SELECT * FROM items WHERE faction='$faction' ORDER BY license") or die(mysql_error());
						while ($row = mysql_fetch_array( $result )){						
							$details = '';
							$sign = '+';	$sign2 = '+';
							if ($row['effect'] < 0){
								$sign = '';
							}
							if ($row['effect2'] < 0){
								$sign2 = '';
							}
							if ($row['mtype'] == 1){
								$details = '<font size=2>(Health Points + '.$row['effect'].')</font>';
							}
							else if ($row['mtype'] != 1){
								$details = '<font size=2>(AP '.$sign.' '.$row['effect'].' ; DP '.$sign2.' '.$row['effect2'].')</font>';
							}
							if ($row['id'] == $checked){
								$check = 'checked';
							}
							else {
								$check = '';
							}
							$controlpan = $controlpan.'<tr><td width=55%>
							<input type="radio" name="factiongear" onClick="displayNewScreen('."'myDiv','main.php?type=3&factions=y&party=".$faction."&armory=y&factiongear='+".$row['id'].');" '.$check.' />'.$row['name'].'<br /></td>
							<td width=35%>'.$details.'</td>
							<td width=10%>'.$row['license'].'</td></tr>';
						}
						$controlpan = $controlpan.'</table></center></td></tr></table></td></tr></table></center>';
						if ($cfaction != $faction){
							$controlpan = '<center><b>'.$row1['name'].' Armory</b><center><br /><center>Dude, GTFO.</center>';
						}
					}
					else if (isset($_GET['barracks'])){
						$image = 20;
						$width = 160;
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
						if ($cfaction == $faction){
							$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction='$faction'") or die(mysql_error());
							$rows = mysql_fetch_array($result);
							$number = $rows['COUNT(*)'];
							$controlpan = '<center><b>'.$row1['name'].'</b><center><br />'
									.'<b>Faction Barracks:</b>'.indent('Soldiers: '.$row1['army'].' / '.(400 + $number*10).'<br />
									<input method=text name="recruit" size="8" /><button type="button" onclick="var e = myForm.elements['."'recruit'".'].value;displayNewScreen('."'myDiv','main.php?type=3&factions=1&party=".$faction."&barracks=y&recruit='+e".');">Recruit</button>
									<br /><br /><font size=1>Note: 1 soldiers costs 10 faction commendations and 200 gold out of the Faction Funds</font>');
						}
					}
					else if (isset($_GET['workshop'])){
						$image = 20;
						$width = 160;
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
						if ($cfaction == $faction){
							$controlpan = '<center><b>'.$row1['name'].'</b><center><br /><b>Faction Workshop:</b><br />Influence: '.$row1['influence'].' / 1000<br />
									<input method=text name="recover" size="8" /><button type="button" onclick="var e = myForm.elements['."'recover'".'].value;displayNewScreen('."'myDiv','main.php?type=3&factions=1&party=".$faction."&workshop=y&recover='+e".');">Repair</button>
									<br /><br /><font size=1>Note: each point of reparation costs 15 faction commendations and 100 gold out of the Faction Funds</font>';
						}
					}
					else if (isset($_GET['contribute'])){
						$image = 33 + $faction;
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
						$controlpan = '<center><b>'.$row1['name'].'</b><center><br /><b>Faction Funds:</b> '.$row1['funds'].'<br /><br />
									<input method=text name="donate" size="8" /><button type="button" onclick="var e = myForm.elements['."'donate'".'].value;displayNewScreen('."'myDiv','main.php?type=3&factions=1&party=".$faction."&contribute=y&donate='+e".');">Donate</button>';
					}
					else if (isset($_GET['legacy'])){
						$image = 33 + $faction;
						$width = 200;
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
						$controlpan = indent('<font size=2>'.$row1['legacy'].'</font>');
					}
					else if (isset($_GET['leave'])){
						$image = 41;
						$width = 120;
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
						$controlpan = "<font size=2>He who abandons his friends and his faith, abandons himself. Sign your character's name below to confirm your decision.<br /><br /><b>(Note: leaving a faction will result in the loss of all faction items)</b></font>".'<br />
						<center>
						<input method=text name="signature2" size="20" />
						<button type="button" onclick="var e = myForm.elements['."'signature2'".'].value;displayNewScreen('."'myDiv','main.php?type=3&factions=1&signature2='+e".');">Abandon</button>
						</center>';
					}
					else if (isset($_GET['join'])){
						$image = 33 + $faction;
						$width = 200;
						$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction='$faction'") or die(mysql_error());
						$rows = mysql_fetch_array($result);
						$members = $rows['COUNT(*)'];
						$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction<>0") or die(mysql_error());
						$rows = mysql_fetch_array($result);
						$total = $rows['COUNT(*)'];
						
						$join = '<input method=text name="signature" size="20" /><button type="button" onclick="var e = myForm.elements['."'signature'".'].value;displayNewScreen('."'myDiv','main.php?type=3&factions=1&party=".$faction."&palace=y&signature='+e".');">Register</button>';
						if ($members >= ($total/3) AND $members >= 3){
							$join = '<b>This Faction is Overpopulated!</b>';
						}
						
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
						$controlpan = "<font size=2>You make a wise decision joining us, for the other factions are far below your standards and expertise! Sign your character's name below to confirm your decision.<br /><br /><b>(Note: you must be level 20+ and pay 1000 gold to join a faction.)</b></font>".'<br /><br />
						<center>'.$join.'</center>';
					}
					else {
						$image = 15 + $faction;
						$width = 140;
						$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1".');">Return to the Entity Council</a>';	
						$id = $row1['id'];
						$enter = '<font size=2><i>(You belong to a rival faction!)</i></font>';
						if ($cfaction == $faction OR $cfaction == 0){
							$enter = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Enter Palace</a>'; 
						}
						$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction='$id'") or die(mysql_error());
						$rows = mysql_fetch_array($result);
						$number = $rows['COUNT(*)'];
						$result22 = mysql_query("SELECT COUNT(*) FROM wars WHERE ((attacker='$id' AND winner=1) OR (defender='$id' AND winner=0)) AND active=0 ") or die(mysql_error());
						$rows22 = mysql_fetch_array($result22);
						$number2 = $rows22['COUNT(*)'];
						$result33 = mysql_query("SELECT COUNT(*) FROM wars WHERE ((attacker='$id' AND winner=0) OR (defender='$id' AND winner=1)) AND active=0 ") or die(mysql_error());
						$rows33 = mysql_fetch_array($result33);
						$number3 = $rows33['COUNT(*)'];
						$controlpan = '<center><b>'.$row1['name'].'</b></center><br />'
										.'<b>War Record:</b>'.indent('<font size=2><center>Wins: '.$number2.'</center></td><td><center>Losses: '.$number3.'</center></font>').'<br />'
										.'<b>Statistics:</b>'.indent('<font size=2>Capital: '.(intval($row1['funds'] / 100) * 100)
										.'+<br />Influence: '.$row1['influence'].'<br/>'.'Total Heros: '.$number.'</font>')
										.'<br /><center>'.$enter.'</center>';
					}
				}
				else{
					$image = 26;
					$width = 200;
					$next = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3'".');">Return to Tavern</a></center>';	
					$explain = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&explain=1'".');">What is this?</a>';	
					$result = mysql_query("SELECT * FROM wars WHERE active=1") or die(mysql_error());
					if (mysql_num_rows($result)){					
						$cwars = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=y&warsnow=y'".');"><b>Current War!</b></a><br />';
					}
					$wars = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=y&awars=y'".');">Latest Wars?</a>';
					$number = 0;
					$result = mysql_query("SELECT * FROM factions") or die(mysql_error());
					while ($row = mysql_fetch_array( $result )){
						$number++;
						$faction[$number] = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=y&party=".$number."'".');">'.$row['name'].'</a>';
					}
					$controlpan = 	"<font size=2>Hey there, welcome to the Entity Council! We try to use it to maintain peace between the civilizations, but you know how things are... Old enemies remain enemies, right?</font><br />".
									indent('<b>Questions:</b>'.indent('<font size=2>'.$explain.'<br/>'.$wars.'</br>'.$cwars.'</font>').
										  '<b>Factions:</b><br />'.indent('<font size=2>'.$faction[1].'<br/>'.$faction[2].'<br/>'.$faction[3].'<br/>'.$faction[4].'</font>'));
				}
			}
			else {
				$image = 1;
				$quest = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&quest=1'".');">Story Quests</a>';
				$hunt = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&hunt=1&setup=1'".');">The Hunt</a>';
				$arena = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&arena=1'".');">PvP Arena</a>';
				$market = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&market=1'".');">Open Market</a>';
				$company = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&ccreate=1'".');">Company</a>';
				if ($ccompany != 0){
					$company = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&market=1&manage=1'".');">Company</a>';
				}
				$job = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&market=y&work=y'".');">Find Work</a>';
				$faction = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1'".');">Entity Council</a>';
				$controlpan = '<div align="left"><b>Adventure:</b><br />'.indent('<font size=2>'.$quest.'<br/>'.$hunt.'<br/>'.$arena.'</font>').'
							  <b>Business:</b><br />'.indent('<font size=2>'.$market.'<br/>'.$company.'<br/>'.$job.'</font>').'
							  <b>Allegiance:</b><br />'.indent('<font size=2>'.$faction.'</font>').'</div>';
			}
			
			$result = mysql_query("SELECT * FROM imagebank WHERE id='$image'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			$image = $row['location'];
			
			$back1 = '';	$back2 = '';	$back3 = '';
			$centwidth = 344-$width;
			$centwidth = 'width='.$centwidth;
			$width = 'width='.$width;
			
			if (isset($allies)){
				$back1 = 'bgcolor="#E0DECF"';
			}
			if (isset($controlpan)){
				$back2 = 'bgcolor="#E0DECF"';
			}
			if (isset($next)){
				$back3 = 'bgcolor="#E0DECF"';
				$next = '<font size=2>'.$next.'</font>';
			}

			echo'
			<div style="height:450px;overflow:hidden;">'.$maincon.'
			<div id="imageframe" style="z-index:1;height:400px;overflow:hidden;"><img src="'.$image.'" width="100%" height="100%"></div>
			<div id="interface"><table height=400px cellpadding=10><tr>
				<td height=10px width=104 '.$back1.'>'.$allies.'</div></td>
				<td '.$centwidth.' ></td>
				<td '.$width.' '.$back2.' >'.$controlpan.'</td>
			</tr><tr></tr></table>
				<div id="control" style="top:'.$more.'px;"><table cellpadding=10><tr><td '.$back3.'><b>'.$next.'</b></td></tr></table></div>
			</div>';
		}
		else if (isset($_GET['test'])){
			echo'<table width=80%><tr><td><font size=2>In registering for an account, you agree not to cause damage to the website or its community in any way. You also agree to not hold more than 1 account. Additionally, accounts with inappropriate credentials will be banned indefinitely.</font></td></tr></table><br />
			<table><tr><td>
			<b>Username: </b></td><td><input name="euser" type="text" value=""></td></tr><tr><td>
			<b>Email: </b></td><td><input name="email" type="text" value=""></td></tr><tr><td>
			<b>Password: </b></td><td><input name="epass" type="password" value=""></td></tr><tr><td colspan=2>
			<center>
			<button type="button" onclick="var e = myForm.elements['."'euser'".'].value;var n = myForm.elements['."'email'".'].value;var p = myForm.elements['."'epass'".'].value;displayNewScreen('."'myDiv','main.php?type=3&euser='+e+'&email='+n+'&epass='+p".');">Register</button><br />
			</center></td></tr></table>';
		}
		else if (isset($_GET['help'])){
			echo'<table width=80%><tr><td><font size=2>Enter the email address used during registration in the field below. You will be emailed your current password.</font></td></tr></table><br />
			<table><tr><td>
			<b>Email: </b></td><td><input name="email" type="text" value=""></td></tr><tr><td colspan=2>
			<center>';
			//<button type="button" onclick="var n = myForm.elements['."'email'".'].value;displayNewScreen('."'myDiv','main.php?type=1&sendhelp=1&email='+n);">Email me!</button>
			echo'We are sorry, our host does not permit emailing.<br />Please contact an administrator.
			<br /></center></td></tr></table>';
		}
		else {
			?>
			<table><tr><td>
			<b>Username: </b></td><td><input name="euser" type="text" value=""></td></tr><tr><td>
			<b>Password: </b></td><td><input name="epass" type="password" value=""></td></tr><tr><td colspan=2>
			<center>
			<button type="button" onclick="var e = myForm.elements['euser'].value;var p = myForm.elements['epass'].value;displayNewScreen('myDiv','main.php?euser='+e+'&epass='+p);">Login</button><br /><font size=2>
			<a href="#" onclick="displayNewScreen('myDiv','main.php?type=3&test=1');">Make an Account!</a> | <a href="#" onclick="displayNewScreen('myDiv','main.php?type=3&help=1');">Forgot Password?</a></font>
			</center>
			</td></tr></table>
			<?
		}
	}
}
else if (isset($_GET['catalogue'])){
	echo'<center>'.indent('<font size=2>View the list of game items below. Note that working for or buying from a company that produces items beyond what your class allows is not possible. Items that cannot be produced by player companies are indicated by an asterix (*).</font></center>').'<br /><center><table width=100%><tr><td>';
	$class0 = 1;
	$class = intval( 1 + $clevel / 5 );
	$result = mysql_query("SELECT * FROM items ORDER BY mclass") or die(mysql_error());
	echo 'Class '.$class0.'<hr /><center><table width=500px><tr><th>Item</th><th>Details</th><th>License</th></tr>';
	while ($row = mysql_fetch_array( $result )){						
		if ($row['mclass'] > $class0){
			$class0++;
			echo'</table></center>';
			echo'<br />Class '.$class0.'<hr />';
			echo'<center><table width=100%><tr><th>Item</th><th>Details</th><th>License</th></tr>';
		}
		$details = '';
		$sign = '+';	$sign2 = '+';
		if ($row['effect'] < 0){
			$sign = '';
		}
		if ($row['effect2'] < 0){
			$sign2 = '';
		}
		if ($row['mtype'] == 1){
			$details = '<font size=2>(Health Points + '.$row['effect'].')</font>';
		}
		else if ($row['mtype'] > 1){
			$details = '<font size=2>(AP '.$sign.' '.$row['effect'].' ; DP '.$sign2.' '.$row['effect2'].')</font>';
		}
		if ($row['id'] != 0)
		echo '<tr><td width=200px>'.$row['name'].'<br /></td>
		<td width=250px>'.$details.'</td>
		<td width=50px>'.$row['license'].'</td></tr>';
	}
	echo'</table></center>';
}
else if (isset($_GET['products'])){
	echo indent('<font size=2>View the list of available licenses below. Note that your company will only be able to make items that your level allows you to use, just as you will only be able to employ players capable of using the produced items. Higher leveled gear will be unlocked as you level up. Also, remember that changing altering which product the company produces will <b>reset</b> the number of items you have in stock.</font>').'<br /><center><table width=450px><tr><td>';
	$class0 = 1;
	$class = intval( 1 + $clevel / 5 );
	$result = mysql_query("SELECT * FROM items WHERE makeable='1' AND mclass<='$class' ORDER BY mclass") or die(mysql_error());
	echo'Class '.$class0.'<hr /><center><table width=400px><tr><th>Item</th><th>Details</th><th>License</th></tr>';
	while ($row = mysql_fetch_array( $result )){						
		if ($row['mclass'] > $class0){
			$class0++;
			echo'</table></center>';
			echo'<br />Class '.$class0.'<hr />';
			echo'<center><table width=400px><tr><th>Item</th><th>Details</th><th>License</th></tr>';
		}
		$checked = '';
		$result1 = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
		$row1 = mysql_fetch_array( $result1 );
		if ($row1['product'] == $row['id']){
			$checked = 'CHECKED';
		}
		$details = '';
		$sign = '+';	$sign2 = '+';
		if ($row['effect'] < 0){
			$sign = '';
		}
		if ($row['effect2'] < 0){
			$sign2 = '';
		}
		if ($row['mtype'] == 1){
			$details = '<font size=2>(Health Points + '.$row['effect'].')</font>';
		}
		else if ($row['mtype'] > 1){
			$details = '<font size=2>(AP '.$sign.' '.$row['effect'].' ; DP '.$sign2.' '.$row['effect2'].')</font>';
		}
		echo'<tr><td width=200px><input type="radio" name="productchoice" onClick="displayNewScreen('."'myDiv','main.php?type=3&market=1&manage=1&productchoice='+".$row['id'].');" '.$checked.' />'.$row['name'].'<br /></td>
		<td width=150px>'.$details.'</td>
		<td width=50px>'.$row['license'].'</td></tr>';
	}
	?>
	</table></center></td></tr></table><br />
	<button type="button" onclick="alert(getValue(productchoice));displayNewScreen('myDiv','main.php?productchoice='+e);">Purchase License</button></center>
	<?php
}
else {
	echo'<b><center>Welcome to Dark Dreams!<br /><font size=2>Enjoy the game.</font></center></b>';
}

?>
		</center></td></tr></table>
		</div></td></tr></table>
	</div></td></tr></table>
	</form></div>