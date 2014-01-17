<?php 
// This file and all of its containing script is property of James Hamet.

// Generate Session
session_start();
if (!isset($_SESSION['initiated'])){
    session_regenerate_id();
    $_SESSION['initiated'] = true;
	}
if (isset($_SESSION['HTTP_USER_AGENT'])){
    if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
		{
		session_destroy();
        exit;
		}
	}
else{
    $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
	}

// Connect to Database
try{
	mysql_connect("localhost", "504810_site5", "paSsword2334") or die(mysql_error());
	mysql_select_db("darkdreams_zzl_mydatabase") or die(mysql_error());
	}
catch (Exception $e){
    die('Error : ' . $e->getMessage());
	}
	
// Functions, Tools and Regex
function clean_string($string) {
					$bad = array("content-type","bcc:","to:","cc:","href");
					return str_replace($bad,"",$string);
					}
function fix_string($string) {
					$string = nl2br($string);
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

// ***** Account System *****
	// For Log out
	if (isset($_GET['logout'])){
		session_destroy();
		header( 'Location: http://darkdreams.zzl.org/' );
		}
	// Run Login Function
	if (isset($_POST['euser']) AND isset($_POST['epass'])){
		$euser = htmlspecialchars($_POST['euser']);
		$epass = htmlspecialchars($_POST['epass']);
		$result = mysql_query("SELECT * FROM users WHERE username='$euser'") or die(mysql_error());  
		$row = mysql_fetch_array( $result );
		if (isset($row['username']) AND $epass == $row['password'])
			{
			session_regenerate_id();
			$_SESSION['username'] = $euser;
			header( 'Location: http://darkdreams.zzl.org/' );
			}
		else
			{
			?><script type="text/javascript">
			alert("Incorrect account credentials.");
			window.location = "http://darkdreams.zzl.org/index.php?asys=y&login=y";	
			</script><?php
			}
		}
	// Run Registration
	if (isset($_POST['nuser']) AND isset($_POST['npass']) AND isset($_POST['nemail'])){
		$nuser = htmlspecialchars($_POST['nuser']);
		$nemail = htmlspecialchars($_POST['nemail']);
		$npass = htmlspecialchars($_POST['npass']);
		$ipadd = $_SERVER['REMOTE_ADDR'];
		$query1 = "SELECT * FROM users WHERE username='$nuser' ";
		$nresult1 = mysql_query($query1) or die(mysql_error());
		$query2 = "SELECT * FROM users WHERE email='$nemail' ";
		$nresult2 = mysql_query($query2) or die(mysql_error());
		$query3 = "SELECT * FROM users WHERE ipadd='$ipadd' ";
		$nresult3 = mysql_query($query3) or die(mysql_error());
		if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $nemail))
			{
			if (mysql_num_rows($nresult1))
				{
				?><script type="text/javascript">
				alert("The username -<?php echo $nuser; ?>- is already registered.");
				history.back();			
				</script><?php
				}
			else if (mysql_num_rows($nresult2))
				{
				?><script type="text/javascript">
				alert("The email -<?php echo $nemail; ?>- is already registered.");
				history.back();			
				</script><?php
				}
			else if (mysql_num_rows($nresult3))
				{
				?><script type="text/javascript">
				alert("The ip address -<?php echo $ipadd; ?>- is already registered.");
				history.back();			
				</script><?php
				}
			else
				{
				mysql_query("INSERT INTO users (creation, username, email, password, ipadd) VALUES(NOW(), '$nuser', '$nemail', '$npass', '$ipadd') ") or die(mysql_error());  
				$_SESSION['username'] = $nuser;
				?><script type="text/javascript">
				alert("Account created.");	
				window.location = "http://darkdreams.zzl.org/";	
				</script><?php
				}
			}
		else
			{
			?><script type="text/javascript">
			alert("Invalid email address.");
			history.back();			
			</script><?php
			}
		}
	// For Signed in
	if (isset($_SESSION['username'])){
		$myuser = $_SESSION['username'];
		$myresult = mysql_query("SELECT * FROM users WHERE username='$myuser'") or die(mysql_error());  
    	$myrow = mysql_fetch_array( $myresult );
		$myid = $myrow['id'];
		}

// ***** Additional Features *****
	// Contact System
	if (isset($_POST['sender_name']) AND isset($_POST['message'])){
		require_once('recaptchalib.php');
		$privatekey = "6LeU8cASAAAAAGq86eq4bt_nfsJ-LgG2O6G5Jl7b";
		$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
		if (!$resp->is_valid) 
			{
			?><script type="text/javascript">
			alert("The reCAPTCHA wasn't entered correctly.");
			history.back();			
			</script><?php
			} 
		else 
			{
			$fromwhere = htmlspecialchars(clean_string($_POST['email']));
			$fromwho = htmlspecialchars($_POST['sender_name']);
			$final_message = htmlspecialchars($_POST['message']);
			$email_to = "info@darkdreams.herobo.com";
			$email_to_vault = "vault@darkdreams.herobo.com";
			$email_subject = "Dark Dreams";
			$headers = 'From: '.$fromwhere."\r\n".
			'Reply-To: '.$fromwhere."\r\n" .
			'X-Mailer: PHP/' . phpversion();
			mail($email_to, $email_subject, $fromwhere, $final_message); 
			mail($email_to_vault, $email_subject, $fromwhere, $final_message); 
			?><script type="text/javascript">
			alert("Message sent successfully!");	
			</script><?php
			}
		}
	// Donation System
	
// ***** Char System *****
	// Load Character
	if (isset($myid)){
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
			
			if (isset($myid) AND isset($_POST['which'])){
				$which = (int) $_POST['which'];
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
			$cquestlimit = $charrow['questnum'];
			
			$result = mysql_query("SELECT * FROM arena WHERE ownerid='$myid'") or die(mysql_error());
			$arenarow = mysql_fetch_array( $result );
			$cenlist = $arenarow['validreg'];
		}
	}
	// Create Character
	if (isset($_GET['create']) AND isset($_GET['check'])){
		if (!isset($myuser) OR $myrow['rights'] < 0)
			{
			?><script type="text/javascript">
			alert("You do not have sufficient priviledges.");
			history.back();			
			</script><?php
			}
		$result = mysql_query("SELECT * FROM chars WHERE ownerid='$myid'") or die(mysql_error());
		if (mysql_num_rows($result) AND $myrow['rights'] < 100 )
			{
			?><script type="text/javascript">
			alert("You already have an account! No cheating, or you will be banned without remorse.");
			window.location = "http://darkdreams.zzl.org/index.php";		
			</script><?php
			}
		else if (isset($_POST['ncharname']))
			{
			$cname = htmlspecialchars($_POST['ncharname']);
			$result = mysql_query("SELECT * FROM chars WHERE name='$cname'") or die(mysql_error());
			if (mysql_num_rows($result) OR strlen($cname) < 5 OR strlen($cname) > 30)
				{
				?><script type="text/javascript">
				alert("The char name -<?php echo $cname; ?>- is already in use or is too short (or even too long).");
				history.back();			
				</script><?php
				}
			else {
				$ownerid = $myid;
				mysql_query("INSERT INTO chars (ownerid, name, creation) VALUES('$ownerid','$cname', NOW()) ") or die(mysql_error()); 
				mysql_query("INSERT INTO invents (ownerid) VALUES('$ownerid') ") or die(mysql_error());  
				mysql_query("INSERT INTO arena (ownerid) VALUES('$ownerid') ") or die(mysql_error());  
				?><script type="text/javascript">
				alert("Char successfully created.");
				window.location = "http://darkdreams.zzl.org/index.php?play=y";
				</script><?php
				}
			}
		}
	// Modify Character
	if (isset($_GET['charmod']) AND isset($_GET['check'])){	
		if (isset($cid) AND $myrow['rights'] > 0){
			if (isset($_POST['ncharimage'])) {
			$cimage = htmlspecialchars($_POST['ncharimage']);
			mysql_query("UPDATE chars SET image='$cimage' WHERE id='$cid'") or die(mysql_error());
			?><script type="text/javascript">
			alert("Char successfully modified.");
			window.location = "http://darkdreams.zzl.org/index.php?play=y";
			</script><?php
			}
		}
	}
	
// ***** Game System *****
if (isset($myuser) AND isset($cid) AND $myrow['rights'] > 0){
	// Correction Service
	if (isset($_SESSION['party']) AND !isset($_GET['mission'])){
		unset($_SESSION['distance']);
		unset($_SESSION['party']);
		unset($_SESSION['quest']);
		unset($_SESSION['zone']);
		unset($_SESSION['eid']);
		unset($_SESSION['ehealth']);
		if (isset($_SESSION['ally1who'])){
			unset($_SESSION['ally1type']);
			unset($_SESSION['ally1who']);
			unset($_SESSION['ally1health']);
		}
		if (isset($_SESSION['ally2who'])){
			unset($_SESSION['ally2type']);
			unset($_SESSION['ally2who']);
			unset($_SESSION['ally2health']);
		}
	}
	if (isset($_SESSION['tquest']) AND !isset($_GET['engage']) AND !isset($_GET['finish'])){
		unset($_SESSION['distance']);
		unset($_SESSION['tquest']);
		unset($_SESSION['monster']);
		unset($_SESSION['mhealth']);
	}
	// is Online?
	$result = mysql_query("SELECT * FROM aaa WHERE id='1'") or die(mysql_error());
	$row = mysql_fetch_array( $result );
	if ($row['isonline'] == 0 AND isset($_GET['play'])){
		?><script type="text/javascript">
		alert("Game Server is Closed\nI hope you all enjoyed the small testing session!");
		window.location = "http://darkdreams.zzl.org";	
		</script><?php
	}
	// Quest Aspect
	else if (isset($_GET['quest'])){
	if (isset($_GET['engage'])){
		$quest = (int) $_GET['engage'];
		$result = mysql_query("SELECT * FROM quests WHERE id='$quest'") or die(mysql_error());
		$row5 = mysql_fetch_array( $result );
	}
		// Quest Setup
		if (isset($_GET['setup'])) {
			if ($chealth <= 0 OR ($cquestlimit > 5 AND $row5['type'] == 1)){
				header( 'Location: http://darkdreams.zzl.org/index.php?play=y&quest=y&death=y' );
			}
			if (!isset($_SESSION['distance'])){
				$_SESSION['distance'] = $row5['length'];
				$_SESSION['tquest'] = $row5['id'];
				if ($row5['type'] == 1){
					$cquestlimit++;
					mysql_query("UPDATE chars SET questnum='$cquestlimit' WHERE id='$cid'") or die(mysql_error());
				}
			}
			if (isset($_SESSION['monster'])){
				unset($_SESSION['monster']);
			}
			if ($_SESSION['distance'] == 1 AND $row5['boss'] != 0){
				$id = $row5['boss'];
				$result = mysql_query("SELECT * FROM monsters WHERE id='$id'") or die(mysql_error());
				$row = mysql_fetch_array($result);
			}
			else {
				$id = $row5['minion'];
				$result = mysql_query("SELECT * FROM monsters WHERE id='$id'") or die(mysql_error());
				$row = mysql_fetch_array($result);
			}
			$_SESSION['monster'] = $row['id'];
			$_SESSION['mhealth'] = intval($row['health'] * ($clevel / $row['level']));			
		}
		// Quest Execute
		else if (isset($_GET['next'])){
			if ($chealth <= 0){
				header( 'Location: http://darkdreams.zzl.org/index.php?play=y&quest=y&death=y' );
			}
			else if (isset($_POST['attackchoice'])){
				$monster = $_SESSION['monster'];
				
				$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
				$mrow = mysql_fetch_array($result);
				$extra = ($mrow['damage'] / rand(1,3));
				if ($cdefense < 0){
					$defense = rand ($cdefense, 0);
				}
				else {
					$defense = rand (0, $cdefense);
				}
				$mdamage = $mrow['damage'] + rand (-$extra, $extra) - $defense;
				if ($mdamage < 0){
					$mdamage = 0;
				}
				
				$_SESSION['choice'] = (int) $_POST['attackchoice'];
				$extra = ($cdamage / $_SESSION['choice']);
				if ($mrow['defense'] < 0){
					$defense = rand ($mrow['defense'], 0);
				}
				else {
					$defense = rand (0, $mrow['defense']);
				}
				$pdamage = $cdamage + rand (-$extra, $extra) - $defense;
				if ($pdamage < 0){
					$pdamage = 0;
				}
				
				$_SESSION['mhealth'] = $_SESSION['mhealth'] - $pdamage;
				$chealth = $chealth - $mdamage;
				mysql_query("UPDATE chars SET health='$chealth' WHERE id='$cid'") or die(mysql_error());
				

				if ($_SESSION['mhealth'] <= 0){
							$_SESSION['distance']--;
							if ($_SESSION['distance'] == 0){
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
								header( 'Location: http://darkdreams.zzl.org/index.php?play=y&quest=y&finish=y' );
							}
							else {
								header( 'Location: http://darkdreams.zzl.org/index.php?play=y&quest=y&engage='.$quest.'&setup=y' );
							}
						}
				else {
					header( 'Location: http://darkdreams.zzl.org/index.php?play=y&quest=y&engage='.$quest.'&next=y' );
				}
				
			}
		}
	}
	// Hunt Aspect
	else if (isset($_GET['hunt'])){
		// Hunt Setup
		if (isset($_GET['setup'])){
			if ($chealth <= 0){
				header( 'Location: http://darkdreams.zzl.org/index.php?play=y' );
			}
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
			
			$prop = ( $mlevel / $monsterrow['level'] );
			$_SESSION['monster'] = $monsterrow['id'];
			$_SESSION['mlevel'] = $mlevel;
			$_SESSION['mhealth'] = intval($monsterrow['health'] * $prop);
			
			header( 'Location: http://darkdreams.zzl.org/index.php?play=y&hunt=y' );
		}
		// Hunt Execute
		else if (isset($_GET['next'])){
			if ($chealth <= 0){
				header( 'Location: http://darkdreams.zzl.org/index.php?play=y' );
			}
			else if (isset($_POST['attackchoice'])){
				$monster = $_SESSION['monster'];
				
				$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
				$mrow = mysql_fetch_array($result);
				$extra = ($mrow['damage'] / rand(1,3));
				if ($cdefense < 0){
					$defense = rand ($cdefense, 0);
				}
				else {
					$defense = rand (0, $cdefense);
				}
				$mdamage = $mrow['damage'] + rand (-$extra, $extra) - $defense;
				if ($mdamage < 0){
					$mdamage = 0;
				}
				
				$_SESSION['choice'] = (int) $_POST['attackchoice'];
				$extra = ($cdamage / $_SESSION['choice']);
				if ($mrow['defense'] < 0){
					$defense = rand ($mrow['defense'], 0);
				}
				else {
					$defense = rand (0, $mrow['defense']);
				}
				$pdamage = $cdamage + rand (-$extra, $extra) - $defense;
				if ($pdamage < 0){
					$pdamage = 0;
				}
				
				$_SESSION['mhealth'] = $_SESSION['mhealth'] - $pdamage;
				$chealth = $chealth - $mdamage;
				mysql_query("UPDATE chars SET health='$chealth' WHERE id='$cid'") or die(mysql_error());
				
				if ($chealth > 0 AND $_SESSION['mhealth'] > 0){
					header( 'Location: http://darkdreams.zzl.org/index.php?play=y&hunt=y' );
				}
				else {
					header( 'Location: http://darkdreams.zzl.org/index.php?play=y&hunt=y&end=y' );
				}
			}
		}
	}
	// Arena Aspect
	else if (isset($_GET['arena'])){
		// Arena Setup
		if (isset($_GET['setup'])){
			$Bigresult = mysql_query("SELECT * FROM arena WHERE validreg='1' AND ownerid<>'$myid'") or die(mysql_error());
			if ($chealth <= 0 OR !mysql_num_rows($result)){
				header( 'Location: http://darkdreams.zzl.org/index.php?play=y&arena=y&imp=y' );
			}
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
			
			if ($number == 0){
				header( 'Location: http://darkdreams.zzl.org/index.php?play=y&arena=y&imp=y' );
			}
			
			$which = rand(1, $number);	
			$number = 0;
			
			$Bigresult2 = mysql_query("SELECT * FROM arena WHERE validreg='1' AND ownerid<>'$myid'") or die(mysql_error());
			while($rows = mysql_fetch_array($Bigresult2)){
				$id = $rows['ownerid'];
				$result = mysql_query("SELECT * FROM chars WHERE ownerid='$id'") or die(mysql_error());
				$validrows = mysql_fetch_array($result);
				if ($validrows['level'] <= $rangehigh AND $validrows['level'] >= $rangelow){
					$number++;
					if ($number == $which){
						$id = $rows['ownerid'];
						$result = mysql_query("SELECT * FROM chars WHERE ownerid='$id'") or die(mysql_error());
						$validrows = mysql_fetch_array($result);
						$_SESSION['defender'] = $validrows['id'];
						$_SESSION['dhealth'] = 100 + ($validrows['level']) * 10;
						$_SESSION['dhealthmax'] = $_SESSION['dhealth'];
						?><script type="text/javascript">window.location = "http://darkdreams.zzl.org/index.php?play=y&arena=y&fight=y";</script><?php
					}
				}
			}
		}
		// Combat Setup
		if (isset($_GET['fight'])){
			$id = $_SESSION['defender'];
			$result = mysql_query("SELECT * FROM chars WHERE id='$id'") or die(mysql_error());
			$row = mysql_fetch_array($result);
			
			$eid = $row['id'];
			$eowner = $row['ownerid'];
			$ename = $row['name'];
			$eimage = $row['image'];
			$elevel = $row['level'];
			
			$eattbonus = 0;
			$edefbonus = 0;
			$result = mysql_query("SELECT * FROM invents WHERE ownerid='$eowner'") or die(mysql_error());
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
		// Arena Execute
		if (isset($_GET['next']) AND isset($eid)){
			if ($chealth <= 0){
				header( 'Location: http://darkdreams.zzl.org/index.php?arena=y' );
			}
			else{
				$rival = $_SESSION['defender'];
				
				$_SESSION['choice'] = (int) $_POST['attackchoice'];
				$extra = ($cdamage / $_SESSION['choice']);
				if ($edefense < 0){
					$defense = rand ($edefense, 0);
				}
				else {
					$defense = rand (0, $edefense);
				}
				$pdamage = $cdamage + rand (-$extra, $extra) - $defense;
				if ($pdamage < 0){
					$pdamage = 0;
				}
				
				$extra = ($edamage / rand(1,3));
				if ($cdefense < 0){
					$defense = rand ($cdefense, 0);
				}
				else {
					$defense = rand (0, $cdefense);
				}
				$mdamage = $edamage + rand (-$extra, $extra) - $defense;
				if ($mdamage < 0){
					$mdamage = 0;
				}
				
				$_SESSION['dhealth'] = $_SESSION['dhealth'] - $pdamage;
				$chealth = $chealth - $mdamage;
				mysql_query("UPDATE chars SET health='$chealth' WHERE id='$cid'") or die(mysql_error());
				
				if ($chealth > 0 AND $_SESSION['dhealth'] > 0){
					header( 'Location: http://darkdreams.zzl.org/index.php?play=y&arena=y&fight=y' );
				}
				else {
					header( 'Location: http://darkdreams.zzl.org/index.php?play=y&arena=y&fight=y&end=y' );
				}
			}
		}
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
	}
	// Market Aspect
	else if (isset($_GET['market'])){
		// Create Company
		if (isset($_GET['ccreate']) AND isset($_GET['check']) AND isset($_POST['ncompname'])){
			if ($cgold < 2000){
				?><script type="text/javascript">
				alert("You do not have enough funding!");
				window.location = "http://darkdreams.zzl.org/index.php?play=y";				
				</script><?php
				}
			else {
				$compname = htmlspecialchars($_POST['ncompname']);
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
				header( 'Location: http://darkdreams.zzl.org/index.php?play=y' );
			}
		}
		// Manage Company
		else if (isset($_POST['type']) AND isset($_POST['quantic']) AND isset($_POST['exchange'])  AND isset($_POST['quanticeq']) AND isset($_POST['price']) AND isset($_POST['pay'])){
			$type = (int) $_POST['type'];
			$exchange = abs((int) $_POST['exchange']);
			$type2 = (int) $_POST['quantic'];
			$impexp = abs((int) $_POST['quanticeq']);
			$price = abs((int) $_POST['price']);
			$pay = abs((int) $_POST['pay']);
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
		else if (isset($_POST['productchoice'])){
			$item = (int) $_POST['productchoice'];
			$result = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			if ($item != $row['prod']){
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
				else {
					?><script type="text/javascript">
					alert("Not enough funding!");
					history.back();			
					</script><?php
				}
			}
		}
		// Go to Work
		else if ($cjob != 5 AND isset($_POST['work'])){
			$where = (int) $_POST['work'];
			$result = mysql_query("SELECT * FROM companies WHERE id='$where'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			if ($row['funds'] >= 0 AND $row['hired'] <= 6){
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
				
				$_SESSION['item'] = $prod;
			}
		}
		// Buy Product
		else if (isset($_POST['buy'])){
			$where = (int) $_POST['buy'];
			$result = mysql_query("SELECT * FROM companies WHERE id='$where'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			
			$price = $row['mprice'];
			$funds = $row['funds'] + $price;
			$profit = $row['profit'] + $price;
			$quantity = $row['quantity'] - 1;
			$newgold = $cgold - $price;
			
			if ($newgold >= 0 AND $quantity >= 0){
				$item = $row['product'];	$_SESSION['item'] = $item;
				if (isset($_POST['usenow']) AND !isset($_GET['fight']) AND !isset($_GET['hunt']) AND !isset($_GET['engage'])){
					$result = mysql_query("SELECT * FROM items WHERE id='$item'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
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
			if (isset($_POST['signature'])){
				if ($cgold >= 1000 AND $cfaction == 0 AND $clevel >= 20){
					$sig = htmlspecialchars($_POST['signature']);
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
			else if (isset($_POST['factiongear'])){
				$gear = (int) $_POST['factiongear'];
				$result0 = mysql_query("SELECT * FROM items WHERE id='$gear'") or die(mysql_error());
				$row0 = mysql_fetch_array( $result0 );
				if ($cfacrep >= $row0['license']){
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
			else if (isset($_POST['recruit'])){
				$recruits = abs((int) $_POST['recruit']);
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
			else if (isset($_POST['recover'])){
				$recover = abs((int) $_POST['recover']);
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
			else if (isset($_POST['donate'])){
				$donate = abs((int) $_POST['donate']);
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
			else if (isset($_GET['mission'])){
				$quest = (int) $_GET['mission'];
				$result5 = mysql_query("SELECT * FROM quests WHERE id='$quest'") or die(mysql_error());
				$row5 = mysql_fetch_array( $result5 );
				// Mission Fail
				if ($chealth <= 0 OR $cfacques > 10){
					header( 'Location: http://darkdreams.zzl.org/index.php?play=y&factions=y&party='.$cfaction.'&quests=y&missfail=y' );
				}
				// Mission Setup
				else if (!isset($_SESSION['distance']) AND $cfacques <= 10){
					$cfacques++;
					mysql_query("UPDATE chars SET factionquest='$cfacques' WHERE ownerid='$myid'") or die(mysql_error());
					$_SESSION['zone'] = 0;
					$_SESSION['quest'] = $quest;
					$_SESSION['distance'] = $row5['length'];
					$_SESSION['party'] = $row5['partysize'];
					
					$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction='$cfaction' AND ownerid<>'$myid'") or die(mysql_error());
					$validrows = mysql_fetch_array($result);
					$number = $validrows['COUNT(*)'];
					
					// Hero Fighters
					if ($number > 0 AND $_SESSION['party'] > 1){
						$first = rand(1,$number);
						if ($number > 1 AND $_SESSION['party'] > 2){
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
							if (isset($_SESSION['ally1type']) AND (!isset($second) OR isset($_SESSION['ally2type']))){
								$breaker = 1;
							}
							else if ($counter == $first){
								$_SESSION['ally1type'] = 1;
								$_SESSION['ally1who'] = $rows['id'];
								$_SESSION['ally1health'] = 100 + ($rows['level']) * 10;
							}
							else if (isset($second) AND $counter == $second){
								$_SESSION['ally2type'] = 1;
								$_SESSION['ally2who'] = $rows['id'];
								$_SESSION['ally2health'] = 100 + ($rows['level']) * 10;
							}
						}
					}
					// NPC Fighters
					$NPC = $row1['typicalwarrior'];
					$result = mysql_query("SELECT * FROM monsters WHERE id='$NPC'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
					if (!isset($first) AND $_SESSION['party'] > 1){
						$_SESSION['ally1type'] = 2;
						$_SESSION['ally1who'] = $NPC;
						$_SESSION['ally1health'] = $row['health'];
					}
					if (!isset($second) AND $_SESSION['party'] > 2){
						$_SESSION['ally2type'] = 2;
						$_SESSION['ally2who'] = $NPC;
						$_SESSION['ally2health'] = $row['health'];
					}
					
					header( 'Location: http://darkdreams.zzl.org/index.php?play=y&factions=y&party='.$cfaction.'&quests=y&mission='.$quest );
				}
				// Combat Setup
				else {
					if ($_SESSION['party'] > 1){
						if ($_SESSION['ally1type'] == 1){
							$id = $_SESSION['ally1who'];
							$result = mysql_query("SELECT * FROM chars WHERE id='$id'") or die(mysql_error());
							$row = mysql_fetch_array($result);
							
							$a1owner = $row['ownerid'];
							$a1name = $row['name'];
							$a1level = $row['level'];
							$a1image = $row['image'];
							$a1health = $_SESSION['ally1health'];
							$a1maxhealth = 100 + ($row['level']) * 10;
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
							
							$a1damage = 5 + 2*($a1level - 1) + $attbonus;
							$a1defense = ($a1level - 1) + $defbonus;
						}
						else {
							$id = $_SESSION['ally1who'];
							$result = mysql_query("SELECT * FROM monsters WHERE id='$id'") or die(mysql_error());
							$row = mysql_fetch_array($result);
							
							$a1name = $row['name'];
							$a1level = $row['level'];
							$a1image = $row['image'];
							$a1health = $_SESSION['ally1health'];
							$a1maxhealth = $row['health'];
							$a1damage = $row['damage'];
							$a1defense = $row['defense'];
						}
						if ($_SESSION['party'] > 2){
							if ($_SESSION['ally2type'] == 1){
								$id = $_SESSION['ally2who'];
								$result = mysql_query("SELECT * FROM chars WHERE id='$id'") or die(mysql_error());
								$row = mysql_fetch_array($result);
								
								$a2owner = $row['ownerid'];
								$a2name = $row['name'];
								$a2level = $row['level'];
								$a2image = $row['image'];
								$a2health = $_SESSION['ally2health'];
								$a2maxhealth = 100 + ($row['level']) * 10;
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
								$a2damage = 5 + 2*($a2level - 1) + $attbonus;
								$a2defense = ($a2level - 1) + $defbonus;
							}
							else {
								$id = $_SESSION['ally2who'];
								$result = mysql_query("SELECT * FROM monsters WHERE id='$id'") or die(mysql_error());
								$row = mysql_fetch_array($result);
								
								$a2name = $row['name'];
								$a2level = $row['level'];
								$a2image = $row['image'];
								$a2health = $_SESSION['ally2health'];
								$a2maxhealth = $row['health'];
								$a2damage = $row['damage'];
								$a2defense = $row['defense'];
							}
						}
					}
					if ($_SESSION['distance'] == 1 AND $row5['boss'] != 0){
						$id = $row5['boss'];
						$_SESSION['eid'] = $id;
						$result = mysql_query("SELECT * FROM monsters WHERE id='$id'") or die(mysql_error());
						$row = mysql_fetch_array($result);
						
						$ename = $row['name'];
						$elevel = $row['level'];
						$eimage = $row['image'];
						if (!isset($_SESSION['ehealth'])){
							$_SESSION['ehealth'] = $row['health'];
						}
						$ehealth = $_SESSION['ehealth'];
						$emaxhealth = $row['health'];
						$edamage = $row['damage'];
						$edefense = $row['defense'];
					}
					else {
						$id = $row5['minion'];
						$result = mysql_query("SELECT * FROM monsters WHERE id='$id'") or die(mysql_error());
						$row = mysql_fetch_array($result);
						
						$ename = $row['name'];
						$elevel = $row['level'];
						$eimage = $row['image'];
						if (!isset($_SESSION['ehealth'])){
							$_SESSION['ehealth'] = $row['health'];
						}
						$ehealth = $_SESSION['ehealth'];
						$emaxhealth = $row['health'];
						$edamage = $row['damage'];
						$edefense = $row['defense'];
					}
				}
				// Combat Execute
				if (isset($_POST['attackchoice'])){	
					if ($chealth <= 0){
						header( 'Location: http://darkdreams.zzl.org/index.php?play=y&factions=y&party='.$cfaction.'&quests=y&missfail=y' );
					}
					else {
						$_SESSION['choice'] = (int) $_POST['attackchoice'];
						$extra = ($cdamage / $_SESSION['choice']);
						if ($edefense < 0){
							$defense = rand ($edefense, 0);
						}
						else {
							$defense = rand (0, $edefense);
						}
						$pdamage = $cdamage + rand (-$extra, $extra) - $defense;
						if ($pdamage < 0){
							$pdamage = 0;
						}
						if ($_SESSION['party'] > 1){
							$extra = ($a1damage / rand(1,3));
							if ($edefense < 0){
								$defense = rand ($edefense, 0);
							}
							else {
								$defense = rand (0, $edefense);
							}
							$p2damage = $a1damage + rand (-$extra, $extra) - $defense;
							if ($p2damage < 0){
								$p2damage = 0;
							}
							$pdamage = $pdamage + $p2damage;
							if ($_SESSION['party'] > 2){
								$extra = ($a2damage / rand(1,3));
								if ($edefense < 0){
									$defense = rand ($edefense, 0);
								}
								else {
									$defense = rand (0, $edefense);
								}
								$p3damage = $a2damage + rand (-$extra, $extra) - $defense;
								if ($p3damage < 0){
									$p3damage = 0;
								}
								$pdamage = $pdamage + $p3damage;
							}
						}
						
						$target = rand(1,$_SESSION['party']);
						if ($target == 1){
							$targetdefense = $cdefense;
						}
						else if ($target == 2){
							$targetdefense = $a1defense;
						}
						else if ($target == 3){
							$targetdefense = $a2defense;
						}
						$extra = ($edamage / rand(1,3));
						if ($targetdefense < 0){
							$defense = rand ($targetdefense, 0);
						}
						else {
							$defense = rand (0, $targetdefense);
						}
						$mdamage = $edamage + rand (-$extra, $extra) - $defense;
						if ($mdamage < 0){
							$mdamage = 0;
						}
						
						if ($target == 1){
							$chealth = $chealth - $mdamage;
							mysql_query("UPDATE chars SET health='$chealth' WHERE id='$cid'") or die(mysql_error());
						}
						else if ($target == 2){
							$newhealth = $a1health - $mdamage;
							$_SESSION['ally1health'] = $newhealth;
							if ($newhealth <= 0){
								$_SESSION['party']--;
								if (!isset($_SESSION['ally2who'])){
									unset($_SESSION['ally1type']);
									unset($_SESSION['ally1who']);
									unset($_SESSION['ally1health']);
								}
								else {
									$_SESSION['ally1type'] = $_SESSION['ally2type'];
									$_SESSION['ally1who'] = $_SESSION['ally2who'];
									$_SESSION['ally1health'] = $_SESSION['ally2health'];
									unset($_SESSION['ally2type']);
									unset($_SESSION['ally2who']);
									unset($_SESSION['ally2health']);
								}
							}
						}
						else if ($target == 3){
							$newhealth = $a2health - $mdamage;
							$_SESSION['ally2health'] = $newhealth;
							if ($newhealth <= 0){
								unset($_SESSION['ally2type']);
								unset($_SESSION['ally2who']);
								unset($_SESSION['ally2health']);
								$_SESSION['party']--;
							}
						}
						$_SESSION['ehealth'] = $_SESSION['ehealth'] - $pdamage;

						if ($_SESSION['ehealth'] <= 0){
							$_SESSION['zone'] = 1;
							$_SESSION['distance']--;
							if ($_SESSION['distance'] == 0){
								$prize = $row5['prize'];
								$cfacrep = $cfacrep + $prize;
								mysql_query("UPDATE chars SET factionrep='$cfacrep' WHERE ownerid='$myid'") or die(mysql_error());
								$cexp = $cexp + 10;
								mysql_query("UPDATE chars SET experience='$cexp' WHERE ownerid='$myid'") or die(mysql_error());
								header( 'Location: http://darkdreams.zzl.org/index.php?play=y&factions=y&party='.$cfaction.'&quests=y&misssuccess='.$quest.'' );
							}
						}
						if ($_SESSION['distance'] > 0){
						header( 'Location: http://darkdreams.zzl.org/index.php?play=y&factions=y&party='.$faction.'&quests=y&mission='.$quest.'' );
					}
					}
				}
			}
		}
		else if (isset($_POST['signature2'])){
			if ($cfaction != 0){
				$sig = htmlspecialchars($_POST['signature2']);
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
}

// ************************ START ************************
echo'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-ca"><head><title>Dark Dreams</title>
<meta name="description" content="Dark Dreams: forge an interactive adventure!" />
<meta name="keywords" content="" lang="en" />
<meta name="language" content="en" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="http://www.pikkipi.com/quill_red.gif" />
<link rel="stylesheet" href="/DarkDreams.css" type="text/css" />	
</head>';

include("chatbox.php");
echo"<script>
<!--
    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }
//-->
</script>";

// Global Opening
echo'<body><div id="logo" /><div id="pagebody"><center><table class="forumline" width="100%"><tr><td><div class="module main">';
echo'<center>
<a class="mainmenu" href="/index.php">Homepage</a> - ';
if (isset($myuser) AND $myrow['rights'] > 0){
	echo '<a class="mainmenu" href="#" onclick="toggle_visibility('."'chat'".');">Chat</a> - 
		  <a class="mainmenu" href="/index.php?logout=y">Logout</a> - ';
	}
else{
	echo '<a class="mainmenu" href="/index.php?asys=y&login=y">Login</a> - ';
	}
echo '<a class="mainmenu" href="/index.php?contact=y">Contact</a> - 
	  <a class="mainmenu" href="/index.php?donate=y">Donate</a> - 
	  <a class="mainmenu" href="/index.php?help=y">Help</a>';
echo'</center><br /><hr /><br />';

// Homepage ( introduction / search / information )
	if (empty($_GET)){
		echo'		
		<center><table width="80%">
		<tr><td><b>Introduction:</b><br /><br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		Welcome to Dark Dreams, a free text based RPG.
		</td></tr><tr><td>
		<br /><center>';
		if (isset($myuser)){
			if (!isset($cname)){
				echo'<a href="/index.php?create=y">Create Char</a>';
			}
			else {
				echo'<a href="/index.php?play=y">Enter World</a><i> as ' . htmlspecialchars($cname) . '</i>';
			}
		}
		echo'</center></td></tr></table>';
		}
	
// Registration and Login
	else if (isset($_GET['asys'])){
		echo'<center>';
		if (isset($_GET['login']))
			{
			echo'<form action="/index.php" method="post"><table><tr><td colspan=2><center>';
				
			echo'<br /></center><tr><td width="20px">
			Username: </td><td><input type="text" name="euser" size="16" /></td></tr><tr><td>
			Password: </td><td><input type="password" name="epass" size="16" /></td></tr><tr><td colspan="2">
			<center><input type="submit" value="Login" /></center></td></tr></table>
			<a href="/index.php?asys=y"><font size="2">Make an Account!</font></a>';
			}
		else if (isset($_GET['regnew']))
			{
			echo'<form action="/index.php" method="post"><table><tr><td colspan=2><center>';
		
			echo'<br /></center><tr><td width="20px">
			Username: </td><td><input type="text" name="nuser" size="16" /></td></tr><tr><td>
			Email: </td><td><input type="text" name="nemail" size="16" /></td></tr><tr><td>
			Password: </td><td><input type="password" name="npass" size="16" /></td></tr><tr><td colspan="2">
			<center><input type="submit" value="Register" /></center></td></tr></table>';
			}
		else
			{
			echo'Terms of Service Agreements<br /><br /><b>
			Please note that the website administration reserves the right to modify this statement at any time without warning.
			</b><br /><br />';
			echo'<a href="/index.php?asys=y&regnew=y">I agree to these terms</a>';
			}
		echo'</center>';
		}
	
// Contact Form ( signed / anonymous )
	else if (isset($_GET['contact'])){
		echo'<center><form name="contactform" method="post" action="index.php"><table width="450px"><tr><td colspan=2><center>';
		echo'<br /></center></td></tr>';
		if (!isset($myuser))
			{
			$myuser = "Anonymous";
			}
		echo'<tr><td valign="top">
			 <label for="sender_name">Sender *</label></td>
			 <td valign="top"><input  type="text" name="sender_name" maxlength="50" size="30" value="'.$myuser.'" readonly></td></tr>
			 <tr><td valign="top"><label for="email">Email Address</label></td>
			 <td valign="top"><input  type="text" name="email" maxlength="80" size="30"></td>
			 <tr><td valign="top">
			 <label for="message">Comments *</label></td><td valign="top">
			 <textarea  name="message" maxlength="1000" cols="25" rows="6"></textarea>
			 </td></tr><tr><td colspan="2" style="text-align:center">
			 <input type="submit" value="Submit"></td></tr></table></form></center>';
		}
		
// Donations ( signed / anonymous )
	else if (isset($_GET['donate'])){
		echo'<center>Donation Center<br /><br />To be added once everything else is in place.</center>';
		}
		
// Help Center ( FAQ / other info )
	else if (isset($_GET['help'])){
		echo'<center>Help Center<br /><br />If you would like to help us set this up, please send us a message via the Contact form!</center>
		<br /><br />'
		.indent('<b>Daily Event:</b><br />'.indent('Arena Defendants<br />Daily 10% Tax'))
		.indent('<b>Hourly Event:</b><br />'.indent('Arena Pay<br />Corporate Employee Limit Reset'))
		.indent('<b>15 minute Event:</b><br />'.indent('Health Regeneration<br />Work Timer'));
		}
		
// *** Member Areas
	else if (isset($myuser)){
		// Char Creation	
			if (isset($_GET['create'])){
				echo'<center><i><font size=2>Please make sure that you choose a name that does not violate any of the rules, and is to your liking.<br /> 
				Name changing is not allowed, and accounts with inappropriate names will be banned indefinitely.</font></i></center><br />';
				echo'<center><form action="/index.php?create=y&check=y" method="post">
				<table><tr><td valign="top">
				<label for="ncharname">Char Name</label></td>
				<td valign="top"><input  type="text" name="ncharname" maxlength="20" size="40"></td></tr>
				<tr><td colspan="2" style="text-align:center">
				<input type="submit" value="Submit"></td></tr>
				</table></form></center>';
			}

		// Char Modification
			else if (isset($_GET['charmod'])){
				echo'<center><i><font size=2>Enter in the URL of the desired avatar image. Please make sure that the image that does not violate<br /> any of the rules, as accounts with inappropriate avatars will be banned indefinitely.</font></i></center><br />';
				echo'<center><form action="/index.php?charmod=y&check=y" method="post">
				<table><tr><td valign="top">
				<label for="ncharimage">Avatar</label></td>
				<td valign="top"><input type="text" name="ncharimage" size="40"></td></tr>
				<tr><td colspan="2" style="text-align:center">
				<input type="submit" value="Submit"></td></tr>
				</table></form></center>';
			}
		
		// Item Catalogue
			else if (isset($_GET['catalogue']) AND isset($cid)){
				echo'<center><i><font size=2>View the list of game items below. Note that working, or buying, for a company that produces items beyond your class is not possible.<br/>Also note that not all items here displayed can be purchased or produced. Items of this nature will be indicated by an asterix (*) in its name.</font></i></center><br />';
					
					echo '<center><table width=650px><tr><td>';
					$class0 = 1;
					$class = intval( 1 + $clevel / 5 );
					$result = mysql_query("SELECT * FROM items ORDER BY mclass") or die(mysql_error());
					echo 'Class '.$class0.'<hr /><center><table width=500px><tr><th>Item</th><th>Details</th><th>License</th></tr>';
					while ($row = mysql_fetch_array( $result )){						
						if ($row['mclass'] > $class0){
							$class0++;
							echo'</table></center>';
							echo'<br />Class '.$class0.'<hr />';
							echo'<center><table width=500px><tr><th>Item</th><th>Details</th><th>License</th></tr>';
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
					echo'</table></center></td></tr></table></center>';
			}
		
		// Company Creation
			else if (isset($_GET['ccreate'])){
					echo'<center><i><font size=2>Please make sure that you choose a name that does not violate any of the rules, and is not already taken.<br /> 
					Crudely named companies will be shutdown and all profits, claimed by the admins.</font></i></center><br />';
					echo'<center><form action="/index.php?play=y&market=y&ccreate=y&check=y" method="post">
					<table><tr><td valign="top">
					<label for="ncompname">Company Name</label></td>
					<td valign="top"><input  type="text" name="ncompname" maxlength="20" size="40"></td></tr>
					<tr><td colspan="2" style="text-align:center">
					<input type="submit" value="Submit"></td></tr>
					</table></form></center>';
					echo indent('<b>Note:<font size=2> Founding and maintaining a company costs a lot of gold. To start a company, one must pay 1000 gold up front, deposit an additional 1000 gold as startup funds, and purchase for the license necessary for the production of the desired goods. Also note that you will need to hire other characters as employees, and that they will need salaries. Failure to support the company with enough gold to pay for plyer employement will result in no items being produced!</font></b>');
				}
		
		// Product Management
			else if (isset($_GET['products'])){
					echo'<center><i><font size=2>View the list of available licenses below. Note that your company will only be able to make items that your level allows you to use, just as you will only be able to employ players capable of using the produced items. Higher leveled gear will be unlocked as you level up. Also, remember that changing altering which product the company produces will <b>reset</b> the number of items you have in stock.</font></i></center><br />';
					
					echo '<center><form action="/index.php?play=y&market=y&manage=y" method="post"><table width=650px><tr><td>';
					$class0 = 1;
					$class = intval( 1 + $clevel / 5 );
					$result = mysql_query("SELECT * FROM items WHERE makeable='1' AND mclass<='$class' ORDER BY mclass") or die(mysql_error());
					echo 'Class '.$class0.'<hr /><center><table width=500px><tr><th>Item</th><th>Details</th><th>License</th></tr>';
					while ($row = mysql_fetch_array( $result )){						
						if ($row['mclass'] > $class0){
							$class0++;
							echo'</table></center>';
							echo'<br />Class '.$class0.'<hr />';
							echo'<center><table width=500px><tr><th>Item</th><th>Details</th><th>License</th></tr>';
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
						echo '<tr><td width=200px><input type="radio" name="productchoice" value="'.$row['id'].'" '.$checked.' />'.$row['name'].'<br /></td>
						<td width=250px>'.$details.'</td>
						<td width=50px>'.$row['license'].'</td></tr>';
					}
					echo'</table></center></td></tr></table><br /><br /><input type="Submit" /></form></center>';
				}
		
		// Play Screen
			else if (isset($_GET['play']) AND isset($cid)){
			$changeava = '';
			$viewinven = '';
			$allies = '';
			$progress = '';
			$width = '200';
			$next = '';
			
			if (isset($_GET['end'])){
				if (isset($_GET['hunt']) AND isset($_SESSION['mhealth']) AND $_SESSION['mhealth'] <= 0){
					if ($chealth > 0){
						$monster = $_SESSION['monster'];
						$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
						$mrow = mysql_fetch_array($result);
						$expgain = rand(10,20) + $mrow['mclass']*5;
						$profit = rand((($mrow['mlevel']) * 5),(100 * intval($mrow['mclass'] + 1)));
					}
					unset($_SESSION['monster']);
					unset($_SESSION['mlevel']);
					unset($_SESSION['mhealth']);
				}
				else if (isset($_GET['arena']) AND isset($_SESSION['dhealth']) AND $_SESSION['dhealth'] < 0){
					$rival = $_SESSION['defender'];
					$result = mysql_query("SELECT * FROM chars WHERE id='$rival'") or die(mysql_error());
					$mrow = mysql_fetch_array($result);
					$rivid = $mrow['ownerid'];
					$result = mysql_query("SELECT * FROM arena WHERE ownerid='$rivid'") or die(mysql_error());
					$erow = mysql_fetch_array($result);
					$result = mysql_query("SELECT * FROM arena WHERE ownerid='$myid'") or die(mysql_error());
					$crow = mysql_fetch_array($result);
					if ($chealth > 0){
						$expgain = rand( 2 * $clevel, 4 * $clevel);
						$profit = (100 + $clevel*10);
						$mgold = $mrow['gold'] - ($mrow['level'] * 2);
						mysql_query("UPDATE chars SET gold='$mgold' WHERE id='$rival'") or die(mysql_error());
						
						$wins = $crow['wins'] + 1;
						mysql_query("UPDATE arena SET wins='$wins' WHERE ownerid='$myid'") or die(mysql_error());
						$losses = $erow['losses'] + 1;
						mysql_query("UPDATE arena SET losses='$losses' WHERE ownerid='$rivid'") or die(mysql_error());
					}
					else {
						$losses = $crow['losses'] + 1;
						mysql_query("UPDATE arena SET losses='$losses' WHERE ownerid='$myid'") or die(mysql_error());
						$wins = $erow['wins'] + 1;
						mysql_query("UPDATE arena SET wins='$wins' WHERE ownerid='$rivid'") or die(mysql_error());
					}
					unset($_SESSION['defender']);
					unset($_SESSION['dhealth']);
					unset($_SESSION['dhealthmax']);
				}
				if ($chealth <= 0){
					?><script type="text/javascript">
					alert("You lost the battle!");			
					</script><?php
					$image = 4;
					$next = '<a href="/index.php?play=y">Return to Tavern</a></center>';
				}
				else {
					$image = 3;
					$next = '<a href="/index.php?play=y">Return to Tavern</a></center>';
					$cgold = $cgold + $profit;
					$cexp = $cexp + $expgain;
					mysql_query("UPDATE chars SET gold='$cgold' WHERE id='$cid'") or die(mysql_error());
					mysql_query("UPDATE chars SET experience='$cexp' WHERE id='$cid'") or die(mysql_error());
					?><script type="text/javascript">
					alert("Victory!\nYou gained <?php echo $profit ?> gold and <?php echo $expgain ?> experience!");			
					</script><?php
				}
			}
			else if (isset($_GET['quest'])){
				if (isset($_GET['engage'])) {
					$view = (int) $_GET['engage'];
					$result = mysql_query("SELECT * FROM quests WHERE id='$view'") or die(mysql_error());
					$row = mysql_fetch_array($result);
					$image = $row['image'];
					$bnum = 0;
					if (isset($row['boss']) AND $row['boss'] != 0){
						$bnum = 1;
					}
					$minions = $_SESSION['distance'] - $bnum;
					$bosses = $bnum;
					$width='280';
					$move[1] = 'Slash <font size=2>(Wild slash)</font>';
					$move[2] = 'Maul <font size=2>(Brutal blow)</font>';
					$move[3] = 'Impale <font size=2>(Precision hit)</font>';
					$checked = 1;
					if (isset($_SESSION['choice'])){
						$checked = $_SESSION['choice'];
					}
					$number = 1;
					$next = '<form action="/index.php?play=y&quest=y&engage='.$view.'&next=y" name="questing" method="post"><center><table width=50%>';
					while ($number < 4){
						$checkit = '';
						if ($number == $checked){
							$checkit = 'CHECKED';
						}
						$next = $next.'<tr><td><input type="radio" name="attackchoice" value="'.$number.'" '.$checkit.' /></td><td>'.$move[$number].'</td></tr>';
						$number++;
					}	
					$next = $next.'</table></cemter><input type=submit value="Submit" /></form>';
					
					$monster = $_SESSION['monster'];
					$mhealth = $_SESSION['mhealth'];
					$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
					$mrow = mysql_fetch_array($result);
					
					$controlpan = '<center><b>'.$mrow['name'].' ( Class '.$mrow['mclass'].' ; Level '.$clevel.' )</b><br/>
								<div id="ProfileBox"><img src="'.$mrow['image'].'" width="100%"/></div></center><br />
								'.indent('<b>Health:</b> '.$mhealth.' / '.intval($mrow['health']*($clevel / $mrow['level'])).'<br /><b>Attack Power:</b> '.$mrow['damage'].'<br /><b>Defense Power: </b>'.$mrow['defense']).'</center><br />';
					$progress = '<center>Minions left: '.$minions.' / Bosses left: '.$bosses.'</center>';
				}
				else if (isset($_GET['finish'])){
					$image = 43;
					$view = $_SESSION['tquest'];
					$result = mysql_query("SELECT * FROM quests WHERE id='$view'") or die(mysql_error());
					$row = mysql_fetch_array($result);
					$controlpan = '<center><b>Congratulations!</b><br /><br />You earned: '.$row['prize'].' gold</center>';
					$next = '<a href="/index.php?play=y&quest=y">Return to Quest Selection</a>';
					unset($_SESSION['monster']);
					unset($_SESSION['mhealth']);
					unset($_SESSION['distance']);
					unset($_SESSION['tquest']);
				}
				else if (isset($_GET['death'])){
					$image = 4;
					$controlpan = '<center><b>Defeat!</b><br /><br />You were killed in action.</center>';
					$next = '<a href="/index.php?play=y&quest=y">Return to Quest Selection</a>';
					unset($_SESSION['monster']);
					unset($_SESSION['mhealth']);
					unset($_SESSION['distance']);
					unset($_SESSION['tquest']);
				}
				else if (isset($_GET['view'])){
					$view = (int) $_GET['view'];
					$result = mysql_query("SELECT * FROM quests WHERE id='$view'") or die(mysql_error());
					$row = mysql_fetch_array($result);
					$begin = '<a href="/index.php?play=y&quest=y&engage='.$view.'&setup=y">Begin Mission!</a>';
					$width = 300;
					$next = '<a href="/index.php?play=y&quest=y">Return to Quest Selection</a>';
					$result = mysql_query("SELECT COUNT(*) FROM quests WHERE id<='$view' AND type=1") or die(mysql_error());
					$validrows = mysql_fetch_array($result);
					$number = $validrows['COUNT(*)'];
					if ($row['type'] == 1 AND $number > $cprog){
						$image = 21;
						$controlpan = "<center>Ohaidere.</center>";
					}
					else {
						$image = $row['image'];
						$controlpan = '<center><b>'.$row['title'].'</b><br /><br />'.indent('<i>'.$row['descript'].'</i>').'<br />
						<table width=80%><tr><td><center><font size=2>Monsters:</font><br />'.$row['length'].'</center></td><td>
						<center><font size=2>Prize Money:</font><br />'.$row['prize'].' gold</center></td></tr></table><br />
						<br />'.$begin.'</center>';
					}
				}
				else {
					$image = 21;
					$width = 300;
					$next = '<a href="/index.php?play=y">Return to Tavern</a>';
					$quests = '';
					$squests = '';
					$number = 0;
					$result = mysql_query("SELECT * FROM quests WHERE type='1' ORDER BY id") or die(mysql_error());
					while ($row = mysql_fetch_array($result)){
						$number++;
						$id = $row['id'];
						if ($number == $cprog){
							$quests = '<a href="/index.php?play=y&quest=y&view='.$row['id'].'">'.$row['title'].'</a><br />';
						}
					}
					if ($quests == ''){
						$result = mysql_query("SELECT * FROM quests WHERE id='$id'") or die(mysql_error());
						$row = mysql_fetch_array($result);
						$quests = '<a href="/index.php?play=y&quest=y&view='.$row['id'].'">'.$row['title'].'</a><br />';
					}
					$result = mysql_query("SELECT * FROM quests WHERE type='3' ORDER BY id") or die(mysql_error());
					while ($row = mysql_fetch_array($result)){
						$squests = $squests.'<a href="/index.php?play=y&quest=y&view='.$row['id'].'">'.$row['title'].'</a><br />';
					}
					$controlpan = indent('<font size=2><i>Oi, there, my friend, have a sit, grab a drink! Let this old soldier tell ye a story... eh? Oh, before we begin, '."I'll".' only tell ye five stories a day, and I '."won't be repeatin'".' myself any of '."'em.".' So listen up!</i></font><br /><br /><b>Main Storyline:</b><br />'.indent($quests)).indent('<b>Side Stories:</b><br />'.indent('<font size=2>'.$squests.'</font>'));
				}
			}
			else if (isset($_GET['hunt'])){
				$image = 2;
				$width='280';
				$move[1] = 'Slash <font size=2>(Wild slash)</font>';
				$move[2] = 'Maul <font size=2>(Brutal blow)</font>';
				$move[3] = 'Impale <font size=2>(Precision hit)</font>';
				$checked = 1;
				if (isset($_SESSION['choice'])){
					$checked = $_SESSION['choice'];
				}
				$number = 1;
				$next = '<form action="/index.php?play=y&hunt=y&next=y" name="battle" method="post"><center><table width=50%>';
				while ($number < 4){
					$checkit = '';
					if ($number == $checked){
						$checkit = 'CHECKED';
					}
					$next = $next.'<tr><td><input type="radio" name="attackchoice" value="'.$number.'" '.$checkit.' /></td><td>'.$move[$number].'</td></tr>';
					$number++;
				}	
				$next = $next.'</table></cemter><input type=submit value="Submit" /></form>';
				
				$monster = $_SESSION['monster'];
				$mlevel = $_SESSION['mlevel'];
				$mhealth = $_SESSION['mhealth'];
				$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
				$mrow = mysql_fetch_array($result);
				
				$controlpan = '<center><b>'.$mrow['name'].' ( Class '.$mrow['mclass'].' ; Level '.$mlevel.' )</b><br/>
							<div id="ProfileBox"><img src="'.$mrow['image'].'" width="100%"/></div></center><br />
							'.indent('<b>Health:</b> '.$mhealth.' / '.intval($mrow['health'] * ($mlevel / $mrow['level'])).'<br /><b>Attack Power:</b> '.$mrow['damage'].'<br /><b>Defense Power: </b>'.$mrow['defense']).'</center><br />';
			}
			else if (isset($_GET['arena'])){
				$image = 5;
				$width = '250';
				$about = '<a href="/index.php?play=y&arena=y&explain=y">What is this?</a>';
				$combat = '<a href="/index.php?play=y&arena=y&setup=y">Challenge a Defender!</a>';
				$enlist = '<a href="/index.php?play=y&arena=y&join=y">Register as a Defender</a>';
				if ($cenlist != 0){
					$enlist = '<a href="/index.php?play=y&arena=y&join=n">Abandon Position</a>';
				}
				$records = '<a href="/index.php?play=y&arena=y&records=y">View Personal Record</a>';
				
				$next = '<a href="/index.php?play=y">Return to Tavern</a>';
				$controlpan = indent('<b>Battle Arena:</b><br />'.indent($about.'<br/>'.$combat.'<br/>'.$enlist.'</br>'.$records.'<br />'));
				if (isset($_GET['explain'])){
					$controlpan = indent("<font size=2><i>Welcome to the arena, kid, the place to smash skulls if you've got nothin' better to do. Here, by Challengin' a Defender, you can pick a fight with any of the registered defenders. If you end up beating them, we'll pay you a handsome sum of gold, which should cover the damages. O' course, you can always be a Defender yourself... they get paid for their services, and we cover the damage for 'em. Just a note: they lose money if they lose us battles! Amount of money earned and lost depends on your level.</i></font><br /><br />").indent('<font size=2>Prize money: '.(100 + $clevel*10).'<br />Defender earnings: '.($clevel*20).' per hour<br />Defender penalty: '.($clevel * 2).' per loss</font>');
					$next = '<a href="/index.php?play=y&arena=y">Return to Arena</a>';
				}
				else if (isset($_GET['records'])){
					$image = 8;
					$result = mysql_query("SELECT * FROM arena WHERE ownerid='$myid'") or die(mysql_error());
					$arenarow = mysql_fetch_array( $result );
					$wins = $arenarow['wins'];
					$losses = $arenarow['losses'];
					$total = $wins + $losses;
					$controlpan = indent("<font size=2><i>Ah, want to have a look at your personal records, eh? Here, number of matches, wins, and defeats. We include results from defending matches.</i></font><br /><br />").indent('<font size=2>Wins: '.($wins).'<br />Defeats: '.($losses).'<br />Total Matches: '.($total).'</font>');
					$next = '<a href="/index.php?play=y&arena=y">Return to Arena</a>';
				}
				else if (isset($_GET['fight'])){
					$image = 6;
					$controlpan = '<center><b>'.$ename.' ( Level '.$elevel.' )</b><br/>
									<div id="ProfileBox"><img src="'.$eimage.'" width="100%"/></div></center><br />
									'.indent('
									<b>Health:</b> '.$_SESSION['dhealth'].' / '.$_SESSION['dhealthmax'].'<br /><br />
									<b>Attack Power:</b> '.$edamage.'<br />
									<b>Defense Power:</b> '.$edefense).'<br />';

					$move[1] = 'Slash <font size=2>(Wild slash)</font>';
					$move[2] = 'Maul <font size=2>(Brutal blow)</font>';
					$move[3] = 'Impale <font size=2>(Precision hit)</font>';
					if (isset($_SESSION['choice'])){
						$checked = $_SESSION['choice'];
					}
					else {
						$checked = 1;
					}
					$next = '<form action="/index.php?play=y&arena=y&fight=y&next=y" name="battle" method="post"><center><table width=50%>';

					$number = 0;
					while ($number < 3){
						$number++;
						
						$next = $next.'<tr><td><input type="radio" name="attackchoice" value="'.$number.'" ';
						if ($number == $checked){
							$next = $next.'checked';
						}
						$next = $next.' ></td><td>'.$move[$number].'</td></tr>';
					}	
					
					$next = $next.'</table></cemter><input type=submit value="Submit" /></form>';
					
				}
				else if (isset($_GET['imp'])){
					$image = 7;
					$controlpan = indent("<i>WRAAARG!!! Something's wrong! We don't have any defenders strong enough to fight you!</i>");
					$next = '<a href="/index.php?play=y&arena=y">Return to Arena</a>';
				}
			}
			else if (isset($_GET['inven'])){
				$image = 9;
				$width = '400';
				$next = '<a href="/index.php?play=y">Return to Tavern</a>';
				$catalogue = '<a href="/index.php?catalogue=y">View All Items</a>';
				
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
						$use[$counter] = '<form name="inventory'.$counter.'" method="post" action="#"><input type="hidden" name="which" value="'.$counter.'" />';
						if ($row1['mtype'] == 1){
							$inven[$counter] = '<font size=2>'.$row1['name'].' (HP + '.$row1['effect'].')</font>';
							$use[$counter] = $use[$counter].'<font size=2><a href="javascript:document.inventory'.$counter.'.submit()">Use</a></font></form>';
						}
						else if ($row1['mtype'] == 2){
							$sign = '+';	$sign2 = '+';
							if ($row1['effect'] < 0){
								$sign = '';
							}
							if ($row1['effect2'] < 0){
								$sign2 = '';
							}
							$inven[$counter] = '<font size=2>'.$row1['name'].' (AP '.$sign.' '.$row1['effect'].' ; DP '.$sign2.' '.$row1['effect2'].')</font>';
							$use[$counter] = $use[$counter].'<font size=2><a href="javascript:document.inventory'.$counter.'.submit()">Remove</a></font></form>';
						}
					}
					else {
						$inven[$counter] = '<font size=2>(empty slot)</font>';
					}
				}
				
				$controlpan = indent('<b>Inventory:</b><br /><center><table width=90%>
							<tr><td width=90% >1: '.$inven[1].'</td><td>'.$use[1].'</td></tr>
							<tr><td>2: '.$inven[2].'</td><td>'.$use[2].'</td></tr>
							<tr><td>3: '.$inven[3].'</td><td>'.$use[3].'</td></tr>
							<tr><td>4: '.$inven[4].'</td><td>'.$use[4].'</td></tr>
							<tr><td>5: '.$inven[5].'</td><td>'.$use[5].'</td></tr>
							</table><br />'.$catalogue.'</center>');
			}
			else if (isset($_GET['market'])){
				if (isset($_GET['manage'])){
					$image = 13;
					$width = '250';
					$about = '<a href="/index.php?play=y&market=y&explain=y">What is this?</a>';
					$profits = '<a href="/index.php?play=y&market=y&profits=y">Profits</a>';
					$destroy= '<a href="/index.php?play=y&market=y&destroy=y">Close Down</a>';
					
					$form = '<form action="/index.php?play=y&market=y&manage=y" name="company" method="post">';
					
					$result1 = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
					$row1 = mysql_fetch_array( $result1 );
					$product = $row1['product'];
					$result2 = mysql_query("SELECT * FROM items WHERE id='$product'") or die(mysql_error());
					$row2 = mysql_fetch_array( $result2 );
					$product = $row2['name'];
					
					$funds = 'Funds: '.$row1['funds'].'<br /><select name="type">
								<option value="1">Deposit</option>
								<option value="-1">Withdraw</option>
								</select>
								<input method=text name="exchange" value=0 size="8" />';
					$trades = '<select name="quantic">
								<option value="-1">Import</option>
								<option value="1">Export</option>
								</select>
								<input method=text name="quanticeq" value=0 size="8" />';
					$production = 'Product: <font size=2><a href="/index.php?play=y&market=y&products=y">'.$product.'</a></font>';
					$stock = 'Number in stock: '.$row1['quantity'];
					$price = '<input method=text name="price" value='.$row1['mprice'].' size="8" />';
					$wages = '<input method=text name="pay" value='.$row1['pay'].' size="8" />';
					
					$next = '<a href="/index.php?play=y">Return to Tavern</a>';
					$controlpan = '<center><b><i>'.$row1['brand'].'</i></b></center><br />'.indent($form.'<b>Secretary:</b><br />'.indent($about.' / '.$profits.'<br />'.$destroy).
									'<b>Corporate Fund:</b><br />'.indent($funds).
									'<b>Production:</b><br />'.indent($production.'<br />'.$stock.'<br />'.$trades).
									'<b>Product Price:</b><br />'.indent($price).
									'<b>Worker Wages:</b><br />'.indent($wages)).
									'<center><a href="javascript:document.company.submit()">Execute Changes</a></center></form>';
				}
				else if (isset($_GET['explain'])){
					$image = 15;
					$controlpan = indent("<font size=2><i>Why, Hello, and welcome to the Company Management screen! It's all real easy to use, so I'm sure my explanation will be more than adequate. First, the Corporate Funds. That's what the company owns. When you sell something, or when someone gets paid, the money leaves and enters in there. Make sure it's plenty full, because people who work for you will need to be paid! If that thing hits zero, you won't be able to hire anyone! Next, Wages, that's how much someone is paid for making the desired item. The Price is what the selling rate is of your product, and finally, in the Production area, we can purchase new licenses and see how many of the desired product we have in stock. Be careful though, switching licenses destroys the previous one, along with all produced products!</i></font>");
					$next = '<a href="/index.php?play=y&market=y&manage=y">Return to Management Screen</a>';
					$width = '300';
				}
				else if (isset($_GET['profits'])){
					$image = 15;
					$width = '300';
					$result1 = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
					$row1 = mysql_fetch_array( $result1 );
					$controlpan = '<center>Profits: '.$row1['profit'].'</center>'.
					indent('<b>Note:<font size=2> The above number shows the sum of all of your sales, minus the worker salaries and licensing fees. Up front fee of 1000 gold and startup funds of 1000 gold are not included. If the number is negative, it is strongly advised that you change your marketing strategy.</font></b>');
					$next = '<a href="/index.php?play=y&market=y&manage=y">Return to Management Screen</a>';
				}
				else if (isset($_GET['work'])){
					$image = 11;
					$width='350';
					$next = '<a href="/index.php?play=y">Return to Tavern</a>';
					if (isset($_POST['prod']) OR isset($prod) OR isset($_SESSION['item'])){
						if (isset($_POST['prod'])){
							$prod = (int) $_POST['prod'];
						}
						else if (isset($_SESSION['item'])){
							$prod = $_SESSION['item'];
						}
						$result = mysql_query("SELECT * FROM companies WHERE product='$prod' AND hired<7 AND funds>pay ORDER BY pay DESC LIMIT 0,1") or die(mysql_error());
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
					if ($company != 'No one' AND $cjob != 5){
						$work = ' <a href="javascript:document.work.submit()">Work</a>';
					}
					else if ($cjob >= 5){
						$work = ' <font size=2><i>(You already worked)</i></font>';
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
								
					$controlpan = indent('<i><font size=2>Come to get a job, eh? Note that you can only work <b>5</b> jobs an hour. Also note that you will only be able to fabricate products that your level allows you to use.</font></i><br /><br />')
								.indent('<form action="#" name="findwork" method="post">Product: <select name="prod">'.$options.'</select>
								<a href="javascript:document.findwork.submit()">Find Work</a></form>
								<br />
								<form action="#" name="work" method="post">
								<input type="hidden" name="work" value='.$id.' />
								<center>'.$work.'<br />Best Pay: '.$pay.'</form><br /><br /><font size=2>Offered to you by</font><br /><i>'.$company.'</i></center>');
				}
				else {
					$image = 10;
					$width='350';
					$next = '<a href="/index.php?play=y">Return to Tavern</a>';
					if (isset($_POST['prod']) OR isset($prod) OR isset($_SESSION['item'])){
						if (isset($_POST['prod'])){
							$prod = (int) $_POST['prod'];
						}
						else if (isset($_SESSION['item'])){
							$prod = $_SESSION['item'];
						}
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
							$useonspot = ' <input type="hidden" name="usenow" /><a href="javascript:document.buy.submit()">Use Now</a>';
						}
						else {
							$useonspot = ' <a href="javascript:document.buy.submit()">Buy</a>';
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
								
					$controlpan = indent('<i><font size=2>Welcome, one and all, to the great market! Select the product you want to buy, and see if we have it stocked in one of the many player-owned markets! We give you lowest price guaranteed, my friend.</font></i><br /><br />')
								.indent('<form action="#" name="findprod" method="post">Product: <select name="prod">'.$options.'</select>
								<a href="javascript:document.findprod.submit()">Find Item</a></form><br />
								<form action="#" name="buy" method="post">
								<input type="hidden" name="buy" value='.$id.' />
								<center>'.$useonspot.'</form><br />Best Price: '.$price.'<br /><br /><font size=2>Offered to you by</font><br /><i>'.$company.'</i></center>');
				}
			}
			else if (isset($_GET['factions'])){
				if (isset($_GET['explain'])){
					$image = 29;
					$next = '<a href="/index.php?play=y&factions=y">Return to the Entity Council</a></center>';	
					$controlpan = indent("<font size=2><i>Why hello there, you must be new to these lands, for the wars between the factions have gone on for ages! Now, though the factions have their differences, one thing remains common between them all: a level requirement of <b>20</b> is required to join any of them, as well as a fee of <b>1000 gold</b>.</i></font>");
				}
				else if (isset($_GET['warsnow'])){
					$image = 29;
					$width = 300;
					$next = '<a href="/index.php?play=y&factions=y">Return to the Entity Council</a></center>';	
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
					$controlpan = '<center><b>Latest Wars</b></center>'.indent('
									  <b>First War:</b><br />'.indent('<font size=2>Attacker: '.$attacker1.'<br/>Defender: '.$defender1.'</br>Casualties: '.$casualties1.'</font>')
									  .'<br />
									  <b>Second War:</b><br />'.indent('<font size=2>Attacker: '.$attacker2.'<br/>Defender: '.$defender2.'</br>Casualties: '.$casualties2.'</font>'));
				}
				else if (isset($_GET['awars'])){
					$image = 29;
					$width = 300;
					$next = '<a href="/index.php?play=y&factions=y">Return to the Entity Council</a></center>';	
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
					$controlpan = '<center><b>Latest Wars</b></center>'.indent('
									  <b>First War:</b><br />'.indent('<font size=2>Attacker: '.$attacker1.'<br/>Defender: '.$defender1.'<br/>Winner: '.$winner1.'</br>Casualties: '.$casualties1.'</font>')
									  .'<br />
									  <b>Second War:</b><br />'.indent('<font size=2>Attacker: '.$attacker2.'<br/>Defender: '.$defender2.'<br/>Winner: '.$winner2.'</br>Casualties: '.$casualties2.'</font>'));
				}
				else if (isset($_GET['map'])){
				$width = "100";
				$image = 1;
				$next = '<a href="/index.php?play=y&factions=y">Return to the Entity Council</a></center>';	
				}
				else if (isset($_GET['party'])){
					$faction = (int) $_GET['party'];
					$result1 = mysql_query("SELECT * FROM factions WHERE id='$faction'") or die(mysql_error());
					$row1 = mysql_fetch_array( $result1 );
					if (isset($_GET['palace'])){
						$image = 33 + $faction;
						$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'">Return to the Outskirts</a>';	
						$legacy = '<a href="/index.php?play=y&factions=y&party='.$faction.'&legacy=y">The Legacy</a>';	
						if ($cfaction == $faction){
							$result = mysql_query("SELECT * FROM wars WHERE active=1") or die(mysql_error());
							$attack = '';
							if (mysql_num_rows($result)){	
								$attack = '<a href="/index.php?play=y&factions=y&party='.$faction.'&attack=y"><b>Launch Attack!</b></a><br />';
							}
							$teamquests = '<a href="/index.php?play=y&factions=y&party='.$faction.'&quests=y">Team Quests</a>';
							$contribute = '<a href="/index.php?play=y&factions=y&party='.$faction.'&contribute=y">Contribute</a>';
							$armory = '<a href="/index.php?play=y&factions=y&party='.$faction.'&armory=y">Faction Armory</a>';
							$barracks = '<a href="/index.php?play=y&factions=y&party='.$faction.'&barracks=y">Faction Barracks</a>';
							$workshop = '<a href="/index.php?play=y&factions=y&party='.$faction.'&workshop=y">Faction Workshop</a>';
							$quit = '<a href="/index.php?play=y&factions=y&party='.$faction.'&leave=y">Leave Faction</a>';
							$controlpan = '<center><b>'.$row1['name'].'</b></center><br />'
							.indent('<b>Palace Options:</b>'.indent($legacy.'<br />'.$attack.$teamquests.'<br />'.$contribute.'<br />'.$armory.'<br />'.$barracks.'<br />'.$workshop.'<br />'.$quit));
						}
						else if ($cfaction == 0){
							$join = '<a href="/index.php?play=y&factions=y&party='.$faction.'&join=y">Join Faction</a>';	
							$controlpan = '<center><b>'.$row1['name'].'</b></center><br />'
										.indent('<b>Palace Options:</b>'.indent($legacy.'<br />'.$join));
						}
						else {
							$controlpan = indent('<font size=2><i>Begone! You belong to another faction, you are not welcome to the throne room! Leave now, or so help you, you shall die a fool.</i></font>');
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
						$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Return to Palace</a>';
						$controlpan = '<center><font size=2><i>Launch an Attack against the '.$enemy.'!</i></font></center>';
					}
					else if (isset($_GET['quests'])){
						if (isset($_GET['fquest'])){
							$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&quests=y">Return to Mission Selection</a>';							
							$quest = (int) $_GET['fquest'];
							$result = mysql_query("SELECT * FROM quests WHERE id='$quest'") or die(mysql_error());
							$row = mysql_fetch_array( $result );
							$image = $row['image'];
							$begin = '<a href="/index.php?play=y&factions=y&party='.$faction.'&quests=y&mission='.$quest.'">Begin Mission!</a>';
							$controlpan = indent('<font size=2><i><center>'.$row['title'].'</center><br /><br />'.$row['descript'].'<br /><br /><b>Party Size:</b> '.$row['partysize'].'<br /><b>Duration:</b> '.$row['length'].' battles</i></font><br /><br /><center>'.$begin.'</center>');
						}
						else if (isset($_GET['mission'])){	
							$result = mysql_query("SELECT * FROM quests WHERE id='$quest'") or die(mysql_error());
							$row = mysql_fetch_array( $result );
							$image = $row['image'];
							$bnum = 0;
							if (isset($row['boss']) AND $row['boss'] != 0){
								$bnum = 1;
							}
							$minions = $_SESSION['distance'] - $bnum;
							$bosses = $bnum;
							$progress = '<center>Minions left: '.$minions.' / Bosses left: '.$bosses.'</center>';
							
							if (isset($_SESSION['zone']) AND $_SESSION['zone'] == 1){
								unset($_SESSION['ehealth']);
								unset($_SESSION['eid']);
								$_SESSION['zone'] = 0;
							}
							else{
								if (isset($_SESSION['ally1who'])){
									$allies = '<hr /><center><b>'.$a1name.' ( Level '.$a1level.' )</b><br/>
											<div id="ProfileBox"><img src="'.$a1image.'" width="100%"/></div></center><br />'.indent('
											<b>Health:</b> '.$a1health.' / '.$a1maxhealth.'<br />
											<b>Attack Power:</b> '.$a1damage.'<br />
											<b>Defense Power:</b> '.$a1defense);
											if (isset($_SESSION['ally2who'])){
												$allies = $allies.'<hr /><center><b>'.$a2name.' ( Level '.$a2level.' )</b><br/>
																	<div id="ProfileBox"><img src="'.$a2image.'" width="100%"/></div></center><br />'.indent('
																	<b>Health:</b> '.$a2health.' / '.$a2maxhealth.'<br />
																	<b>Attack Power:</b> '.$a2damage.'<br />
																	<b>Defense Power:</b> '.$a2defense);
											}
								}
											
								$controlpan = '<center><b>'.$ename.' ( Level '.$elevel.' )</b><br/>
										<div id="ProfileBox"><img src="'.$eimage.'" width="100%"/></div></center><br />
										'.indent('
										<b>Health:</b> '.$ehealth.' / '.$emaxhealth.'<br /><br />
										<b>Attack Power:</b> '.$edamage.'<br />
										<b>Defense Power:</b> '.$edefense).'<br />';

								$move[1] = 'Slash <font size=2>(Wild slash)</font>';
								$move[2] = 'Maul <font size=2>(Brutal blow)</font>';
								$move[3] = 'Impale <font size=2>(Precision hit)</font>';
								if (isset($_SESSION['choice'])){
									$checked = $_SESSION['choice'];
								}
								else {
									$checked = 1;
								}
								$next = '<form action="/index.php?play=y&factions=y&party='.$faction.'&quests=y&mission='.$quest.'" name="battle" method="post"><center><table width=50%>';
								$number = 0;
								while ($number < 3){
									$number++;
									
									$next = $next.'<tr><td><input type="radio" name="attackchoice" value="'.$number.'" ';
									if ($number == $checked){
										$next = $next.'checked';
									}
									$next = $next.' ></td><td>'.$move[$number].'</td></tr>';
								}	
								
							$next = $next.'</table></cemter><input type=submit value="Submit" /></form>';
							}
						}
						else if (isset($_GET['misssuccess'])){
						$mission = (int) $_GET['misssuccess'];
						$result = mysql_query("SELECT * FROM quests WHERE id='$mission'") or die(mysql_error());
						$row = mysql_fetch_array( $result );
						$prize = $row['prize'];
						$controlpan = '<center><b>We have victory!</b><br /><br /><table width=90%><tr><td><font size=2>Commendations Earned:</font></td><td>'.$prize.'</td></tr>
						<tr><td><font size=2>Experience Earned:</font></td><td>10</td></tr></table></center>';
						$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Return to the Palace</a>';
						$image = 43;
						}
						else if (isset($_GET['missfail'])){
						$image = 4;
						$next = '<a href="/index.php?play=y">Return to Tavern</a></center>';
						$controlpan = indent('<font size=2><i>You are too weak to go on, or you have exceeded your limit of quests per hour. Mission failed.</i></font>');
						}
						else{
							$image = 25;
							$width = '300';
							$faction = $cfaction;
							$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Return to the Palace</a>';	
							$rescue = '<a href="/index.php?play=y&factions=y&party='.$faction.'&quests=y&fquest=1">Rescue Mission</a>';
							$seize = '<a href="/index.php?play=y&factions=y&party='.$faction.'&quests=y&fquest=2">Seize a Temple</a>';
							$boss = '<a href="/index.php?play=y&factions=y&party='.$faction.'&quests=y&fquest=3">Kill a Warbeast</a>';
							$controlpan = indent('<font size=2><i>It is good that you choose to help your faction. We could use capable people like you out in the field. Would you believe it if I told you that the local blasphemers were once more causing us trouble? This must stop!<br /><br /><b>(Note: quests reward Faction Commendations, which can be spent in the barracks, or used to borrow Faction Gear. Be warned though, you can only attempt to complete up to 10 missions per hour.)</b></i></font>').'<br />'
							.indent('<b>Available Missions:</b>'.indent($rescue.'<br />'.$seize.'<br />'.$boss).'<br /><center>'.(10 - $cfacques).' Attempts Remaining</center>');
						}
					}
					else if (isset($_GET['armory'])){
						$image = 42;
						$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Return to Palace</a>';
						$controlpan = '<center><b>'.$row1['name'].' Armory</b></center><br /><br />'
									.'<table width=90%><tr><td><b>Available Gear:</b>
									<form action="/index.php?play=y&factions=y&party='.$faction.'&armory=y" name="armory" method="post"><table width=80%><tr><td>'
									.'<center><table width=340px><tr><th>Item</th><th>Details</th><th>Cost</th></tr>';
						
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
						
						$result = mysql_query("SELECT * FROM items WHERE faction='$faction'") or die(mysql_error());
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
							else if ($row['mtype'] > 1){
								$details = '<font size=2>(AP '.$sign.' '.$row['effect'].' ; DP '.$sign2.' '.$row['effect2'].')</font>';
							}
							if ($row['id'] == $checked){
								$check = 'checked';
							}
							else {
								$check = '';
							}
							$controlpan = $controlpan.'<tr><td width=55%><input type="radio" name="factiongear" value="'.$row['id'].'" '.$check.' />'.$row['name'].'<br /></td>
							<td width=35%>'.$details.'</td>
							<td width=10%>'.$row['license'].'</td></tr>';
						}
						$controlpan = $controlpan.'</table><br /><center><input type="submit" value="Equip"></center></td></tr></table></form></td></tr></table></center>';
						if ($cfaction != $faction){
							$controlpan = '<center><b>'.$row1['name'].' Armory</b><center><br /><center>Dude, GTFO.</center>';
						}
					}
					else if (isset($_GET['barracks'])){
						$image = 20;
						$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Return to Palace</a>';
						if ($cfaction == $faction){
							$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction='$faction'") or die(mysql_error());
							$rows = mysql_fetch_array($result);
							$number = $rows['COUNT(*)'];
							$controlpan = '<center><b>'.$row1['name'].'</b><center><br />'
									.indent('<b>Faction Barracks:</b>'.indent('Soldiers: '.$row1['army'].' / '.(400 + $number*10).'<br /><br />Recruit: 
									<form action="/index.php?play=y&factions=y&party='.$faction.'&barracks=y" method="post">
									<input method=text name="recruit" size="8" /><input type="submit" value="Go"></form><br /><font size=2><i>Note: 1 soldiers costs 10 faction commendations and 200 gold out of the Faction Funds</i></font>'));
						}
						else {
							$controlpan = '<center><b>'.$row1['name'].'</b><center><br /><center>Dude, GTFO.</center>';
						}
					}
					else if (isset($_GET['workshop'])){
						$image = 20;
						$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Return to Palace</a>';
						if ($cfaction == $faction){
							$controlpan = '<center><b>'.$row1['name'].'</b><center><br />'
									.indent('<b>Faction Workshop:</b>'.indent('Influence: '.$row1['influence'].' / 1000<br /><br />Repair: 
									<form action="/index.php?play=y&factions=y&party='.$faction.'&workshop=y" method="post">
									<input method=text name="recover" size="8" /><input type="submit" value="Go"></form><br /><font size=2><i>Note: each point of reparation costs 15 faction commendations and 100 gold out of the Faction Funds</i></font>'));
						}
						else {
							$controlpan = '<center><b>'.$row1['name'].'</b><center><br /><center>Dude, GTFO.</center>';
						}
					}
					else if (isset($_GET['contribute'])){
						$image = 33 + $faction;
						$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Return to Palace</a>';
						$controlpan = '<center><b>'.$row1['name'].'</b><center><br />'
									.indent('<b>Faction Funds:</b>'.indent('Total Funds: '.$row1['funds'].'<br /><br />Donate: 
									<form action="/index.php?play=y&factions=y&party='.$faction.'&contribute=y" method="post">
									<input method=text name="donate" size="8" /><input type="submit" value="Go"></form>'));
						if ($cfaction != $faction){
							$controlpan = '<center><b>'.$row1['name'].' Armory</b><center><br /><center>Dude, GTFO.</center>';
						}
					}
					else if (isset($_GET['legacy'])){
						$image = 33 + $faction;
						$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Return to Palace</a>';
						$controlpan = indent('<font size=2><i>'.$row1['legacy'].'</i></font>');
					}
					else if (isset($_GET['leave'])){
						$image = 41;
						$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Return to Palace</a>';
						$controlpan = indent("<font size=2><i>He who abandons his friends and his city seeks only self destruction... Retype your character's name below to confirm your decision!<br /><br /><b>(Note: leaving a faction will result in the loss of all faction items. One cannot leave a faction during Wartime.)</b></i></font>").'<br />
						<center><form action="/index.php?play=y&factions=y" method="post">
						<input method=text name="signature2" size="20" /><input type="submit" value="Abandon">
						</form></center>';
					}
					else if (isset($_GET['join'])){
						$image = 33 + $faction;
						
						$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction='$faction'") or die(mysql_error());
						$rows = mysql_fetch_array($result);
						$members = $rows['COUNT(*)'];
						$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction<>0") or die(mysql_error());
						$rows = mysql_fetch_array($result);
						$total = $rows['COUNT(*)'];
						
						$join = '<input method=text name="signature" size="20" /><input type="submit" value="Register">';
						if ($members >= ($total/3) AND $members >= 3){
							$join = '<b>This Faction is Overpopulated!</b>';
						}
						
						$next = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Return to Palace</a>';
						$controlpan = indent("<font size=2><i>You wish to join us, then? You make a wise decision, for the other factions were not suitable choices for someone of your caliber and expertise! Retype your character's name below to confirm your decision!<br /><br /><b>(Note: joining a faction costs 1000 gold and requires a player level of at least 20)</b></i></font>").'<br />
						<center><form action="/index.php?play=y&factions=y&party='.$faction.'&palace=y" method="post">'.$join.'</form></center>';
					}
					else {
						$image = 15 + $faction;
						$next = '<a href="/index.php?play=y&factions=y">Return to the Entity Council</a>';	
						$id = $row1['id'];
						$enter = '<font size=2><i>(You belong to a rival faction!)</i></font>';
						if ($cfaction == $faction OR $cfaction == 0){
							$enter = '<a href="/index.php?play=y&factions=y&party='.$faction.'&palace=y">Enter Palace</a>'; 
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
										.indent('<b>War Record:</b>'.indent('<center>Wins: '.$number2.'</center></td><td><center>Losses: '.$number3.'</center>')).'<br />'
										.indent('<b>Statistics:</b>'.indent('Capital: '.(intval($row1['funds'] / 100) * 100)
										.'+<br />Influence: '.$row1['influence'].'<br/>'.'Total Heros: '.$number)
										.'<br /><center>'.$enter.'</center>');
					}
				}
				else{
					$image = 26;
					$next = '<a href="/index.php?play=y">Return to Tavern</a></center>';	
					$explain = '<a href="/index.php?play=y&factions=y&explain=y">What is this?</a>';	
					$map = '<a href="/index.php?play=y&factions=y&map=y">'."What's on the Map</a>";	
					$result = mysql_query("SELECT * FROM wars WHERE active=1") or die(mysql_error());
					if (mysql_num_rows($result)){					
						$cwars = '<a href="/index.php?play=y&factions=y&warsnow=y"><b>Current War!</b></a><br />';
					}
					$wars = '<a href="/index.php?play=y&factions=y&awars=y">Latest Wars?</a>';
					$number = 0;
					$result = mysql_query("SELECT * FROM factions") or die(mysql_error());
					while ($row = mysql_fetch_array( $result )){
						$number++;
						$faction[$number] = '<a href="/index.php?play=y&factions=y&party='.$number.'">'.$row['name'].'</a>';
					}
					$controlpan = 	indent("<font size=2><i>Hey there, welcome to the Entity Council! This be the meetin' place o' the four factions. We try to use it to maintain peace between the civilizations, but you know how things are... Old enemies remain enemies, right?</i></font><br />").
									indent('<b>Questions:</b>'.indent($explain.'<br/>'.$map.'<br/>'.$wars.'</br>'.$cwars).'<br />
										  <b>Factions:</b><br />'.indent($faction[1].'<br/>'.$faction[2].'<br/>'.$faction[3].'<br/>'.$faction[4].'<br/>'));
				}
			}
			else {
				$image = 1;
				$changeava = '<i><a href="/index.php?charmod=y">Change Image</a></i>';
				$viewinven = '<center><i><a href="/index.php?play=y&inven=y">Inventory</a></i></center>';
				$quest = '<a href="/index.php?play=y&quest=y">Story Quests</a>';
				$hunt = '<a href="/index.php?play=y&hunt=y&setup=y">The Hunt</a>';
				$arena = '<a href="/index.php?play=y&arena=y">PvP Arena</a>';
				$market = '<a href="/index.php?play=y&market=y">Open Market</a>';
				$company = '<a href="/index.php?play=y&market=y&ccreate=y">Start a Company</a>';
				if ($ccompany != 0){
					$company = '<a href="/index.php?play=y&market=y&manage=y">Manage Company</a>';
				}
				$job = '<a href="/index.php?play=y&market=y&work=y">Find Some Work</a>';
				$faction = '<a href="/index.php?play=y&factions=y">Visit the Entity Council</a>';
				$controlpan = indent('<b>Adventure:</b><br />'.indent($quest.'<br/>'.$hunt.'<br/>'.$arena.'</br>').'<br />
									  <b>Business:</b><br />'.indent($market.'<br/>'.$company.'<br/>'.$job.'<br/>').'<br />
									  <b>Allegiance:</b><br />'.indent($faction.'<br/>'));
			}
			$result = mysql_query("SELECT * FROM imagebank WHERE id='$image'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			$image = $row['location'];

			if ($cfaction != 0){
				$commendations = '<b>Comm.:</b> '.$cfacrep.' / 2000<br />';
			}
			
			echo'<center><table width="80%" ><tr><td width="200px">
			
			<center><b>'.$cname.' ( Level '.$clevel.' )</b><br/>
			<div id="ProfileBox"><img src="'.$cimage.'" width="100%"/></div>'.$changeava.'</center><br />
			'.indent('
			<b>Health:</b> '.$chealth.' / '.$chealthmax.'<br />
			<b>Experience:</b> '.$cexp.' / '.$cexpmax.'<br />
			<b>Gold:</b> '.$cgold.'<br />'.$commendations.'<br /><b>Attack Power:</b> '.$cdamage.'<br />
			<b>Defense Power:</b> '.$cdefense).$viewinven.'<br />
			'.$allies.'
			</td><td>'.$progress.'
			<img src="'.$image.'" width="100%" />
			<center>'.$next.'</center>
			</td><td width="'.$width.'px">'.$controlpan.'</td></tr></table>';
		}
	}

//	Error Page
	else {
		echo'<center><b>Oops!</b><br />An error has occured! Please hold while we redirect you to the homepage.</center>
				<meta http-equiv="refresh" content="1; url=http://darkdreams.zzl.org">';		
	}
		
// Global Ending
echo'<br /><hr /><br /><div id="Footer"> Dark Dreams © 2011 </div></div></table></div></body><br />';

// Analytics
echo'<script type="text/javascript">'."
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-12913897-10']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</html>";

// Website Main Chatbox Setup
echo'<div id="chat" style="display:none"><div class="box" onmousedown="dragStart(event)">
<object width="200" height="400"><TABLE border=0 align="center" width="100%" height="100%" bgcolor=#000000><TR><TD>
<object width="200" height="400" id="obj_1268862558914"><param name="movie" value="http://aqwportal.chatango.com/group"/>
<param name="AllowScriptAccess" VALUE="always"/><param name="AllowNetworking" VALUE="all"/><param name="AllowFullScreen" VALUE="true"/>
<param name="flashvars" value="cid=1268862558914&a=000000&b=100&c=999999&d=848484&e=000000&g=CCCCCC&h=333333&i=29&j=CCCCCC&k=666666&l=333333&m=000000&n=CCCCCC&w=0"/>
<embed id="emb_1268862558914" src="http://aqwportal.chatango.com/group" width="200" height="400" allowScriptAccess="always" allowNetworking="all" type="application/x-shockwave-flash" allowFullScreen="true" flashvars="cid=1268862558914&a=000000&b=100&c=999999&d=848484&e=000000&g=CCCCCC&h=333333&i=29&j=CCCCCC&k=666666&l=333333&m=000000&n=CCCCCC&w=0">
</embed></object><a href="#" onclick="toggle_visibility('."'chat'".');" style="text-decoration:none"><div align="center"><font size="2">Close</a></font>
</center></TD></TR></TABLE></object></div></div></div>';
?>