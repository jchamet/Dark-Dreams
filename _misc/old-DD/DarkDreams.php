<?php 
// This file and all of its containing script is property of James Hamet.

// Generate Session
session_start();
if (!isset($_SESSION['initiated']))
	{
    session_regenerate_id();
    $_SESSION['initiated'] = true;
	}
if (isset($_SESSION['HTTP_USER_AGENT']))
	{
    if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
		{
		session_destroy();
        exit;
		}
	}
else
	{
    $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
	}
// Session Timeout
if ((time() - $_session['timeout']) > 1800)
	{
	session_destroy();
	}
else
	{
	$_session['timeout'] = time();
	}

// Connect to Database
try
	{
	mysql_connect("mysql16.000webhost.com", "a2314638_12", "1admindd1") or die(mysql_error());
	mysql_select_db("a2314638_12") or die(mysql_error());
	}
catch (Exception $e)
	{
    die('Error : ' . $e->getMessage());
	}
	
// Functions, Tools and Regex
function clean_string($string) 
					{
					$bad = array("content-type","bcc:","to:","cc:","href");
					return str_replace($bad,"",$string);
					}
function fix_string($string) 
					{
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
function indent($string)
					{
					$string = '<center><table width=90%><tr><td>'.$string.'</td></tr></table></center>';
					return $string;
					}

// ***** Account System *****
	// For Log out
	if (isset($_GET['logout']))
		{
		session_destroy();
		header( 'Location: http://darkdreams.herobo.com/' );
		}
	// Run Login Function
	if (isset($_POST['euser']) AND isset($_POST['epass']))
		{
		require_once('recaptchalib.php');
		$privatekey = "6LeU8cASAAAAAGq86eq4bt_nfsJ-LgG2O6G5Jl7b";
		$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
		if ($resp->is_valid) 
			{
			$euser = htmlspecialchars($_POST['euser']);
			$epass = htmlspecialchars($_POST['epass']);
			$result = mysql_query("SELECT * FROM users WHERE username='$euser'") or die(mysql_error());  
			$row = mysql_fetch_array( $result );
			if (isset($row['username']) AND $epass == $row['password'])
				{
				session_regenerate_id();
				$_SESSION['username'] = $euser;
				header( 'Location: http://darkdreams.herobo.com/' );
				}
			else
				{
				?><script type="text/javascript">
				alert("Incorrect account credentials.");
				window.location = "http://darkdreams.herobo.com/index.php?asys=y&login=y";	
				</script><?php
				}
			}
		}
	// Run Registration
	if (isset($_POST['nuser']) AND isset($_POST['npass']) AND isset($_POST['nemail']))
		{
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
					?><script type="text/javascript">
					alert("Account created.");	
					</script><?php
					mysql_query("INSERT INTO users (creation, username, email, password, ipadd) VALUES(NOW(), '$nuser', '$nemail', '$npass', '$ipadd') ") or die(mysql_error());  
					session_regenerate_id();
					$_SESSION['username'] = $nuser;
					header( 'Location: http://darkdreams.herobo.com/' );
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
		}
	// For Signed in
	if (isset($_SESSION['username']))
		{
		$myuser = $_SESSION['username'];
		$myresult = mysql_query("SELECT * FROM users WHERE username='$myuser'") or die(mysql_error());  
    	$myrow = mysql_fetch_array( $myresult );
		}

// ***** Additional Features *****
	// Contact System
	if (isset($_POST['sender_name']) AND isset($_POST['message']))
		{
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
	
	
// ***** Game System *****
	// Load Game
	if (isset($_GET['game']))
		{
		$gameid = (int) htmlspecialchars($_GET['game']);
		$thisgquery = "SELECT * FROM games WHERE id='$gameid' ";
		$thisgresult = mysql_query($thisgquery) or die(mysql_error());
		$thisgrow = mysql_fetch_array( $thisgresult );
		}
	// New Game
	if (isset($_GET['createg']))
		{
		if (!isset($myuser) OR $myrow['rights'] < 50)
			{
			?><script type="text/javascript">
			alert("You do not have sufficient priviledges.");
			history.back();			
			</script><?php
			}
		else if (isset($_POST['gametitle']))
			{
			$gametitle = htmlspecialchars($_POST['gametitle']);
			$query = "SELECT * FROM games WHERE gametitle='$gametitle' ";
			$result = mysql_query($query) or die(mysql_error());
			if (mysql_num_rows($result))
				{
				?><script type="text/javascript">
				alert("The game title -<?php echo $gametitle; ?>- is already in use.");
				history.back();			
				</script><?php
				}
			else if (isset($_POST['gameintro']) AND isset($_POST['gameplay']))
				{
				$ownerid = $myrow['id'];
				$gameintro = htmlspecialchars($_POST['gameintro']);
				$gameplay = htmlspecialchars($_POST['gameplay']);
				mysql_query("INSERT INTO games (gametitle, creation, lastchange, ownerid, introduction, gameplay) VALUES('$gametitle', NOW(), NOW(), '$ownerid', '$gameintro', '$gameplay' ) ") or die(mysql_error());  
				$xresult = mysql_query("SELECT * FROM games WHERE gametitle='$gametitle'") or die(mysql_error());  
				$xrow = mysql_fetch_array( $xresult );
				$newpageid = $xrow['id'];
				?><script type="text/javascript">
				alert("Game successfully created.");
				window.location = "http://darkdreams.herobo.com/index.php?game=<?php echo $newpageid; ?>";
				</script><?php
				}
			}
		}
	// Modify Game
	if (isset($_GET['gmode']) AND isset($_GET['genedit']))
		{
		if (!isset($myuser) OR $myrow['rights'] < 50)
			{
			?><script type="text/javascript">
			alert("You do not have sufficient priviledges.");
			history.back();			
			</script><?php
			}
		else if (isset($_POST['gametitle']) AND isset($_POST['gameintro']) AND isset($_POST['gameplay']))
			{
			$ownerid = $myrow['id'];
			$gametitle = htmlspecialchars($_POST['gametitle']);				
			$gameintro = htmlspecialchars($_POST['gameintro']);
			$gameplay = htmlspecialchars($_POST['gameplay']);
			mysql_query("UPDATE games SET lastchange=NOW() WHERE id='$gameid'") or die(mysql_error()); 
			mysql_query("UPDATE games SET introduction='$gameintro' WHERE gametitle='$gametitle'") or die(mysql_error()); 
			mysql_query("UPDATE games SET gameplay='$gameplay' WHERE gametitle='$gametitle'") or die(mysql_error()); 			
			$xresult = mysql_query("SELECT * FROM games WHERE gametitle='$gametitle'") or die(mysql_error());  
			$xrow = mysql_fetch_array( $xresult );
			$newpageid = $xrow['id'];
			?><script type="text/javascript">
			alert("Game successfully modified.");
			window.location = "http://darkdreams.herobo.com/index.php?game=<?php echo $newpageid; ?>";
			</script><?php
			}
		}
	// New Story
	if (isset($_GET['game']) AND isset($_GET['gmode']) AND isset($_GET['nchap']))
		{
		$gameid = (int) htmlspecialchars($_GET['game']);
		if (!isset($myuser) OR $myrow['rights'] < 0)
			{
			?><script type="text/javascript">
			alert("You do not have sufficient priviledges.");
			history.back();			
			</script><?php
			}
		else if (isset($_POST['nchapt']) AND isset($_POST['nchapc']) AND (($myrow['rights'] > 50 AND $myrow['id'] == $thisgrow['ownerid']) OR $myrow['rights'] > 100))
			{
			$ownerid = $myrow['id'];
			$nchapt = htmlspecialchars($_POST['nchapt']);
			$nchapc = htmlspecialchars($_POST['nchapc']);
			mysql_query("INSERT INTO stories (gameid, ownerid, creation, title, content) VALUES('$gameid', '$ownerid', NOW(), '$nchapt', '$nchapc') ") or die(mysql_error()); 
			mysql_query("UPDATE games SET lastchange=NOW() WHERE id='$gameid'") or die(mysql_error());
			?><script type="text/javascript">
			alert("Chapter submitted.");
			window.location = "http://darkdreams.herobo.com/index.php?game=<?php echo $gameid; ?>&story=y";
			</script><?php
			}
		}
	// Modify Story
	if (isset($_GET['gmode']) AND isset($_GET['sstory']) AND isset($_GET['game']))
		{
		$sstory = (int) htmlspecialchars($_GET['sstory']);
		if (!isset($myuser) OR $myrow['rights'] < 50)
			{
			?><script type="text/javascript">
			alert("You do not have sufficient priviledges.");
			history.back();			
			</script><?php
			}
		else if (isset($_POST['echapt']) AND isset($_POST['echapc']) AND (($myrow['rights'] > 50 AND $myrow['id'] == $thisgrow['ownerid']) OR $myrow['rights'] > 100))
			{
			$echapt = htmlspecialchars($_POST['ncharstats']);
			$echapc = htmlspecialchars($_POST['ncharinven']);
			mysql_query("UPDATE games SET lastchange=NOW() WHERE id='$gameid'") or die(mysql_error());
			mysql_query("UPDATE stories SET title='$echapt' WHERE id='$sstory'") or die(mysql_error()); 
			mysql_query("UPDATE stories SET content='$echapc' WHERE id='$sstory'") or die(mysql_error()); 			
			?><script type="text/javascript">
			alert("Chapter successfully modified.");
			window.location = "http://darkdreams.herobo.com/index.php?game=<?php echo $gameid; ?>&story=y";
			</script><?php
			}
		}
	// New Character
	if (isset($_GET['game']) AND isset($_GET['newchar']))
		{
		$gameid = (int) htmlspecialchars($_GET['game']);
		$result = mysql_query("SELECT * FROM games WHERE id='$gameid'") or die(mysql_error());  
		$gamerow = mysql_fetch_array( $result );
		$ownerid = $gamerow['ownerid'];
		if (!isset($myuser) OR $myrow['rights'] < 0)
			{
			?><script type="text/javascript">
			alert("You do not have sufficient priviledges.");
			history.back();			
			</script><?php
			}
		$myid = $myrow['id'];
		$query = "SELECT * FROM chars WHERE ownerid='$myid' AND gameid='$gameid'";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) AND $myrow['rights'] < 100 AND $myrow['id'] != $ownerid )
			{
			?><script type="text/javascript">
			alert("You already have an account! No cheating, or you will be banned without remorse.");
			window.location = "http://darkdreams.herobo.com/index.php?game=<?php echo $gameid; ?>";		
			</script><?php
			}
		else if (isset($_POST['ncharname']))
			{
			$ncharname = htmlspecialchars($_POST['ncharname']);
			$query = "SELECT * FROM chars WHERE name='$ncharname' AND gameid='$gameid'";
			$result = mysql_query($query) or die(mysql_error());
			if (mysql_num_rows($result))
				{
				?><script type="text/javascript">
				alert("The char name -<?php echo $ncharname; ?>- is already in use.");
				history.back();			
				</script><?php
				}
			else if (isset($_POST['ncharstats']) AND isset($_POST['ncharinven']) AND isset($_POST['ncharinfo']))
				{
				$ownerid = $myrow['id'];
				$ncharstats = htmlspecialchars($_POST['ncharstats']);
				$ncharinven = htmlspecialchars($_POST['ncharinven']);
				$ncharinfo = htmlspecialchars($_POST['ncharinfo']);
				mysql_query("INSERT INTO chars (gameid, ownerid, name, stats, inventory, info, creation) VALUES('$gameid', '$ownerid', '$ncharname', '$ncharstats', '$ncharinven', '$ncharinfo', NOW() ) ") or die(mysql_error());  
				mysql_query("UPDATE games SET lastchange=NOW() WHERE id='$gameid'") or die(mysql_error());
				?><script type="text/javascript">
				alert("Char successfully created.");
				window.location = "http://darkdreams.herobo.com/index.php?game=<?php echo $gameid; ?>&listchars=y";
				</script><?php
				}
			}
		}
	// Modify Character
	if (isset($_GET['gmode']) AND isset($_GET['schar']) AND isset($_GET['game']))
		{
		$gameid = (int) htmlspecialchars($_GET['game']);
		$schar = (int) htmlspecialchars($_GET['schar']);
		if (!isset($myuser) OR $myrow['rights'] < 50)
			{
			?><script type="text/javascript">
			alert("You do not have sufficient priviledges.");
			history.back();			
			</script><?php
			}
		else if (isset($_POST['ncharstats']) AND isset($_POST['ncharinven']) AND isset($_POST['ncharinfo']))
			{
			$ncstats = htmlspecialchars($_POST['ncharstats']);
			$ownerid = $myrow['id'];
			$ncinven = htmlspecialchars($_POST['ncharinven']);
			$ncinfo = htmlspecialchars($_POST['ncharinfo']);
			mysql_query("UPDATE games SET lastchange=NOW() WHERE id='$gameid'") or die(mysql_error());
			mysql_query("UPDATE chars SET stats='$ncstats' WHERE id='$schar'") or die(mysql_error()); 
			mysql_query("UPDATE chars SET inventory='$ncinven' WHERE id='$schar'") or die(mysql_error()); 
			mysql_query("UPDATE chars SET info='$ncinfo' WHERE id='$schar'") or die(mysql_error()); 			
			?><script type="text/javascript">
			alert("Char successfully modified.");
			window.location = "http://darkdreams.herobo.com/index.php?game=<?php echo $gameid; ?>";
			</script><?php
			}
		}
	// Delete Game
	if (isset($_GET['game']) AND isset($_GET['gmode']) AND isset($_GET['delete']) AND ($myrow['rights'] > 100 OR ($myrow['rights'] > 50 AND $myrow['id'] == $thisgrow['ownerid'])))
		{
		$deleteid = $thisgrow['id'];
		mysql_query("DELETE FROM games WHERE id='$deleteid'") or die(mysql_error()); 
		mysql_query("DELETE FROM chars WHERE gameid='$deleteid'") or die(mysql_error()); 
		mysql_query("DELETE FROM stories WHERE gameid='$deleteid'") or die(mysql_error()); 
		?><script type="text/javascript">
		alert("Game deleted.");
		window.location = "http://darkdreams.herobo.com/";
		</script><?php
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
if (isset($myuser) AND $myrow['rights'] > 0)
	{
	echo '<a class="mainmenu" href="#" onclick="toggle_visibility('."'chat'".');">Chat</a> - 
		  <a class="mainmenu" href="/index.php?logout=y">Logout</a> - ';
	}
else
	{
	echo '<a class="mainmenu" href="/index.php?asys=y&login=y">Login</a> - ';
	}
echo '<a class="mainmenu" href="/index.php?contact=y">Contact</a> - 
	  <a class="mainmenu" href="/index.php?donate=y">Donate</a> - 
	  <a class="mainmenu" href="/index.php?help=y">Help</a>';
echo'</center><br /><hr /><br />';

// Homepage ( introduction / search / information )
	if (empty($_GET))
		{
		echo'		
		<center><table width="80%">
		<tr><td><b>Introduction:</b><br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		Welcome to Dark Dreams, a free collection of chat RPGs.
		</td></tr></table>
		
		<br /><br /><br />
		
		<table><tr><td><form action="/index.php?search=y" method="post"><center>Search for a Game<br />
		<input type="text" name="entry" size=24><input type="submit" value="Go!">
		<br /><sup>leave blank to find our top games!</sup></center></form></td>
		
		<td width="50px"></td>
		
		<td><form action="/index.php?createg=y" method="post"><center>Create a Game<br />
		<input type="text" name="gametitle" size=24><input type="submit" value="Go!">
		<br /><sup>NOTE: Only GMs may create games</center></form></td></tr></table>
		
		<br /><br /><br />
		
		<table width="80%"><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		This will be about general information.</td></tr></table>';
		}
		
// Search ( games --> title / last run / small intro / player count )
	else if (isset($_GET['search']) AND (isset($_POST['entry']) OR isset($_SESSION['entry'])))
		{
		if (isset($_POST['entry']))
				{
				$_SESSION['entry'] = htmlspecialchars($_POST['entry']);
				}
		$entry = $_SESSION['entry'];
		echo'Games containing the entry: <i>"'.$entry.'"</i><br /><br />';
		if (isset($_GET['page']) AND $_GET['page'] > 0)
				{
				$pagenum = (int)$_GET['page'] - 1;
				}
			else
				{
				$pagenum = 0;
				}
		$limit1 = 0 + 10 * $pagenum;
		$limit2 = 10;
		$searchresult = mysql_query("SELECT * FROM games WHERE gametitle LIKE '%" . $entry . "%' OR gameplay LIKE '%" . $entry . "%' OR introduction LIKE '%" . $entry . "%' ORDER BY gametitle DESC LIMIT $limit1,$limit2") or die(mysql_error());  
		echo'<table cellspacing="10">';
		while($searow = mysql_fetch_array($searchresult))
			{
			$result = mysql_query("SELECT COUNT(id) FROM chars WHERE gameid=".$searow['id']."") or die(mysql_error());
			$row = mysql_fetch_array($result);
			$charcount = $row['COUNT(id)'];
			echo '<tr><td>>> <a href="/index.php?game=' . $searow['id'] . '">' . $searow['gametitle'] . '</a></td><td><font size="2">('.$charcount.' Chars)</font></td><td><font size="2">(Time Created: '.$searow['creation'].')</font></td></tr>';
			}
		echo'</table>';
		$searchresult = mysql_query("SELECT COUNT(id) FROM games WHERE gametitle LIKE '%" . $entry . "%' OR gameplay LIKE '%" . $entry . "%' OR introduction LIKE '%" . $entry . "%'") or die(mysql_error());
		$searow = mysql_fetch_array($searchresult);
		$sealeft = $searow['COUNT(id)'];
		$hpagenum = 0;
		if ($sealeft > 10)
			{
			echo '<br />Pages:&nbsp;&nbsp;';
			while ($sealeft > 0)
				{
				$hpagenum++;
				if ($hpagenum == $hpagenu + 1)
					{
					echo $hpagenum . '&nbsp;';
					}
				else
					{
					echo '<a href="/moboexample.php?search=y&page=' . $hpagenum .'">' . $hpagenum . '</a>&nbsp;';
					}
				$sealeft = $sealeft - 10;
				}
			}	
		}
		
// Game Create
	else if (isset($_GET['createg']) AND isset($myuser) AND $myrow['rights'] > 50)
		{
		echo'<center><form action="/index.php?createg=y" method="post"><table><tr><td valign="top">
			 <label for="gametitle">Game Title *</label></td>
			 <td valign="top"><input  type="text" name="gametitle" maxlength="50" size="40" value="'.$gametitle.'"></td></tr>
			 <tr><td valign="top">
			 <label for="gameintro">Introduction</label></td><td valign="top">
			 <textarea  name="gameintro" maxlength="1000" cols="60" rows="12"></textarea></td></tr>
			 <tr><td valign="top">
			 <label for="gameplay">Gameplay</label></td><td valign="top">
			 <textarea  name="gameplay" maxlength="5000" cols="60" rows="12"></textarea></td></tr>
			 <tr><td colspan="2" style="text-align:center">
			 <input type="submit" value="Submit"><br /><sup>Fields without (*) may be modified later.</sup></td></tr>
			 </table></form></center>';
		}
		
// Game Pages  ( intro / information / story / (newchar: only if not already) / chars / **GM** : Modify Data )
	else if (isset($_GET['game']))
		{
		if (!isset($myuser))
			{
			?><script type="text/javascript">
			alert("You are not logged in. Please login to view this page.");
			window.location = "http://darkdreams.herobo.com/index.php?asys=y&login=y";	
			</script><?php
			}
		else 
			{
			$gameid = (int) htmlspecialchars($_GET['game']);
			$result = mysql_query("SELECT * FROM games WHERE id='$gameid'") or die(mysql_error());  
			$gamerow = mysql_fetch_array( $result );
			$ownerid = $gamerow['ownerid'];
			$result = mysql_query("SELECT * FROM users WHERE id='$ownerid'") or die(mysql_error());  
			$ownerrow = mysql_fetch_array( $result );
			$myid = $myrow['id'];
			$queryex = "SELECT * FROM chars WHERE ownerid='$myid' AND gameid='$gameid'";
			$resultex = mysql_query($queryex) or die(mysql_error());
			$rowex = mysql_fetch_array( $resultex );
			echo'<center><font size=5>'.$gamerow['gametitle'].'</font><br />by '.$ownerrow['username'].'<br /><br />';
			echo'<table width="90%"><tr><td><center>';
			echo'<a class="mainmenu" href="/index.php?game='.$gameid.'"><img src="" border="0"  hspace="0" alt="Main" /></a> - 
				 <a class="mainmenu" href="/index.php?game='.$gameid.'&story=y"><img src="" border="0"  hspace="0" alt="Storyline" /></a> - 
				 <a class="mainmenu" href="/index.php?game='.$gameid.'&gameplay=y"><img src="" border="0"  hspace="0" alt="Gameplay" /></a> - ';
				if ((!mysql_num_rows($resultex)) OR ($myrow['rights'] > 50 AND $myrow['id'] == $gamerow['ownerid']))
					{
					echo'<a class="mainmenu" href="/index.php?game='.$gameid.'&newchar=y"><img src="" border="0"  hspace="0" alt="New Char" /></a> - ';
					}
				echo'<a class="mainmenu" href="/index.php?game='.$gameid.'&listchars=y"><img src="" border="0"  hspace="0" alt="List Chars" /></a>';
				if (($myrow['rights'] > 50 AND $myrow['id'] == $gamerow['ownerid']) OR $myrow['rights'] > 100 )
					{
					echo' - <a class="mainmenu" href="/index.php?game='.$gameid.'&gmode=y"><img src="" border="0"  hspace="0" alt="GM: Edit" /></a>';
					}
				echo'</center></td></tr>';
			echo'<tr><td><i>Last modified: '.$gamerow['lastchange'].'</i><br /><br /></td></tr><tr><td>';
			if (isset($_GET['gameplay']))
				{
				$gameplay = indent(fix_string($gamerow['gameplay']));
				echo'<font size=4><b>Gameplay:</b></font><br /><br /><center><table width=80%><tr><td>'.$gameplay.'</td></tr></table></center></td></tr></table>';
				}
			else if (isset($_GET['story']))
				{
				$storyresult = mysql_query("SELECT * FROM stories WHERE gameid='$gameid' ORDER BY creation DESC") or die(mysql_error());  
				echo'<font size=4><b>Storybook:</b></font><br /><br />';
				while($storyrow = mysql_fetch_array($storyresult))
					{
					$gmedit = '';
					if (($myrow['rights'] > 50 AND $myrow['id'] == $thisgrow['ownerid']) OR $myrow['rights'] > 100)
						{
						$gmedit = ' - <a href="/index.php?game='.$gameid.'&gmode=y&sstory='.$storyrow['id'].'">Edit</a>';
						}
					$gamestory = '<b> '.$storyrow['title'].'</b>'.$gmedit.'<br /><br />'.indent($storyrow['content']).'</td>';
					echo '<hr />'.indent(fix_string($gamestory)).'<hr />';
					}
				echo'</td></tr></table>';
				}
			else if (isset($_GET['listchars']))
				{
				$charresult = mysql_query("SELECT * FROM chars WHERE gameid='$gameid' ORDER BY name DESC") or die(mysql_error());  
				echo'<font size=4><b>List of Characters:</b></font><br /><br /><hr />';
				while($charrow = mysql_fetch_array($charresult))
					{
					if (($myrow['id'] == $gamerow['ownerid']) OR $myrow['rights'] > 100)
						{
						$gmedit = ' - <a href="/index.php?game='.$gameid.'&gmode=y&schar='.$charrow['id'].'">Edit</a>';
						}
					else
						{
						$gmedit = '';
						}
					$charprofile = '<b>Char '.$charrow['name'].'</b>'.$gmedit.'<br />Stats: '.indent($charrow['stats']).'Inventory: '.indent($charrow['inventory']).'</td><td valign="top" width="30%"><br />'.indent($charrow['info']).'</td>';
					echo indent(fix_string($charprofile)).'<hr />';
					}
				echo'</td></tr></table>';
				}
			else if (isset($_GET['newchar']))
				{
				echo'<center><i><font size=2>Please make sure that your sheet fits the format of the game as defined by the Game Master. If it does not, it may be removed.<br />
				All questionable content also risks being removed, and may lead to your account being banned.</font></i></center><br />';
				echo'<center><form action="/index.php?game='.$gameid.'&newchar=y" method="post"><table><tr><td valign="top">
				<label for="ncharname">Char Name *</label></td>
				<td valign="top"><input  type="text" name="ncharname" maxlength="50" size="40"></td></tr>
				<tr><td valign="top">
				<label for="ncharstats">Char Stats</label></td><td valign="top">
				<textarea  name="ncharstats" maxlength="500" cols="60" rows="12"></textarea></td></tr>
				<tr><td valign="top">
				<label for="ncharinven">Char Inventory</label></td><td valign="top">
				<textarea  name="ncharinven" maxlength="500" cols="60" rows="12"></textarea></td></tr>
				<tr><td valign="top">
				<label for="ncharinfo">Char Information</label></td><td valign="top">
				<textarea  name="ncharinfo" maxlength="500" cols="60" rows="12"></textarea></td></tr>
				<tr><td colspan="2" style="text-align:center">
				<input type="submit" value="Submit"><br /><sup>Fields without (*) may be modified later unless specified.</sup></td></tr>
				</table></form></center>';
				}
			else if (isset($_GET['gmode']) AND ($myrow['id'] == $gamerow['ownerid'] OR $myrow['rights'] > 100))
				{
				if (isset($_GET['genedit']))
					{
					echo'<center><form action="/index.php?game='.$gameid.'&gmode=y&genedit=y" method="post"><table><tr><td valign="top">
					<label for="gametitle">Game Title</label></td>
					<td valign="top"><input  type="text" name="gametitle" maxlength="50" size="40" value="'.$gamerow['gametitle'].'" readonly></td></tr>
					<tr><td valign="top">
					<label for="gameintro">Introduction</label></td><td valign="top">
					<textarea  name="gameintro" maxlength="1000" cols="60" rows="12">'.$gamerow['introduction'].'</textarea></td></tr>
					<tr><td valign="top">
					<label for="gameplay">Gameplay</label></td><td valign="top">
					<textarea  name="gameplay" maxlength="5000" cols="60" rows="12">'.$gamerow['gameplay'].'</textarea></td></tr>
					<tr><td colspan="2" style="text-align:center">
					<input type="submit" value="Submit"><br /></td></tr>
					</table></form></center>';
					}
				else if (isset($_GET['schar']))
					{
					$schar = (int) htmlspecialchars($_GET['schar']);
					$scharresult = mysql_query("SELECT * FROM chars WHERE id='$schar'") or die(mysql_error());  
					$scharrow = mysql_fetch_array( $scharresult );
					echo'<center><form action="/index.php?game='.$gameid.'&gmode=y&schar='.$schar.'" method="post"><table><tr><td valign="top">
					<label for="ncharname">Char Name *</label></td>
					<td valign="top"><input  type="text" name="ncharname" maxlength="50" size="40" value="'.$scharrow['name'].'" readonly></td></tr>
					<tr><td valign="top">
					<label for="ncharstats">Char Stats</label></td><td valign="top">
					<textarea  name="ncharstats" maxlength="500" cols="60" rows="12">'.$scharrow['stats'].'</textarea></td></tr>
					<tr><td valign="top">
					<label for="ncharinven">Char Inventory</label></td><td valign="top">
					<textarea  name="ncharinven" maxlength="500" cols="60" rows="12">'.$scharrow['inventory'].'</textarea></td></tr>
					<tr><td valign="top">
					<label for="ncharinfo">Char Information</label></td><td valign="top">
					<textarea  name="ncharinfo" maxlength="500" cols="60" rows="12">'.$scharrow['info'].'</textarea></td></tr>
					<tr><td colspan="2" style="text-align:center">
					<input type="submit" value="Submit"><br /></td></tr>
					</table></form></center>';
					}
				else if (isset($_GET['nchap']))
					{
					echo'<center><form action="/index.php?game='.$gameid.'&gmode=y&nchap=y" method="post"><table><tr><td valign="top">
					<label for="nchapt">Chapter Title </label></td>
					<td valign="top"><input  type="text" name="nchapt" maxlength="50" size="40" ></td></tr>
					<tr><td valign="top">
					<label for="nchapc">Chapter Content</label></td><td valign="top">
					<textarea  name="nchapc" maxlength="5000" cols="60" rows="12"></textarea></td></tr>
					<tr><td colspan="2" style="text-align:center">
					<input type="submit" value="Submit"><br /></td></tr>
					</table></form></center>';
					}
				else if (isset($_GET['sstory']))
					{
					$sstory = (int) htmlspecialchars($_GET['sstory']);
					$sstoryresult = mysql_query("SELECT * FROM stories WHERE gameid='$gameid'") or die(mysql_error());  
					$sstoryrow = mysql_fetch_array( $sstoryresult );
					echo'<center><form action="/index.php?game='.$gameid.'&gmode=y&sstory='.$sstory.'" method="post"><table><tr><td valign="top">
					<label for="echapt">Chapter Title </label></td>
					<td valign="top"><input  type="text" name="echapt" maxlength="50" size="40" value="'.$sstoryrow['title'].'"></td></tr>
					<tr><td valign="top">
					<label for="echapc">Chapter Content</label></td><td valign="top">
					<textarea  name="echapc" maxlength="5000" cols="60" rows="12">'.$sstoryrow['content'].'</textarea></td></tr>
					<tr><td colspan="2" style="text-align:center">
					<input type="submit" value="Submit"><br /></td></tr>
					</table></form></center>';
					}
				else
					{
					echo'<center><a href="/index.php?game='.$gameid.'&gmode=y&genedit=y">Modify Game Details</a><br /><a href="/index.php?game='.$gameid.'&gmode=y&nchap=y">Add new story chapter</a><br /><br /><a href="/index.php?game='.$gameid.'&gmode=y&delete=y">Delete Game (this cannot be undone)</a></center>';
					}
				}
			else
				{
				$gameintro = indent(fix_string($gamerow['introduction']));
				echo'<font size=4><b>Introduction:</b></font><br /><br />'.$gameintro.'</td></tr>';
				echo'<tr><td></table>';
				}		
			}
		}
// Session Pages ( two live chat boxes )

// Registration and Login
	else if (isset($_GET['asys']))
		{
		echo'<center>';
		if (isset($_GET['login']))
			{
			echo'<form action="/index.php?asys=y" method="post"><table><tr><td colspan=2><center>';
			
			// reCAPTCHA protection
			require_once('recaptchalib.php');
			$publickey = "6LeU8cASAAAAAO66tiM7exeWfdU-ooKdZM6sdhsO";
			echo recaptcha_get_html($publickey);
		
			echo'<br /></center><tr><td width="20px">
			Username: </td><td><input type="text" name="euser" size="16" /></td></tr><tr><td>
			Password: </td><td><input type="password" name="epass" size="16" /></td></tr><tr><td colspan="2">
			<center><input type="submit" value="Login" /></center></td></tr></table>
			<a href="/index.php?asys=y"><font size="2">Make an Account!</font></a>';
			}
		else if (isset($_GET['regnew']))
			{
			echo'<form action="/index.php" method="post"><table><tr><td colspan=2><center>';
			
			// reCAPTCHA protection
			require_once('recaptchalib.php');
			$publickey = "6LeU8cASAAAAAO66tiM7exeWfdU-ooKdZM6sdhsO ";
			echo recaptcha_get_html($publickey);
		
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
	else if (isset($_GET['contact']))
		{
		echo'<center><form name="contactform" method="post" action="index.php"><table width="450px"><tr><td colspan=2><center>';
		
		// reCAPTCHA protection
		require_once('recaptchalib.php');
		$publickey = "6LeU8cASAAAAAO66tiM7exeWfdU-ooKdZM6sdhsO  ";
		echo recaptcha_get_html($publickey);
		
		echo'<br /></center></td></tr>';
		if (isset($myuser))
			{
			$sender_name = $myuser;
			}
		else
			{
			$sender_name = "Anonymous";
			}
		echo'<tr><td valign="top">
			 <label for="sender_name">Sender *</label></td>
			 <td valign="top"><input  type="text" name="sender_name" maxlength="50" size="30" value="'.$sender_name.'" readonly></td></tr>
			 <tr><td valign="top"><label for="email">Email Address</label></td>
			 <td valign="top"><input  type="text" name="email" maxlength="80" size="30"></td>
			 <tr><td valign="top">
			 <label for="message">Comments *</label></td><td valign="top">
			 <textarea  name="message" maxlength="1000" cols="25" rows="6"></textarea>
			 </td></tr><tr><td colspan="2" style="text-align:center">
			 <input type="submit" value="Submit"></td></tr></table></form></center>';
		}
		
// Donations ( signed / anonymous )
	else if (isset($_GET['donate']))
		{
		echo'<center>Donation Center<br /><br />To be added once everything else is in place.</center>';
		}
		
// Help Center ( FAQ / other info )
	else if (isset($_GET['help']))
		{
		echo'<center>Help Center<br /><br />If you would like to help us set this up, please send us a message via the Contact form!</center>';
		}
		
// Global Ending
echo'<br /><hr /><br /><div id="Footer"> Dark Dreams © 2011 </div></div></table></div></body><br /></html>';

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