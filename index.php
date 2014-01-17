<?php
// Protection
if (!isset($_GET['user']) or !isset($_GET['date']) or htmlspecialchars($_GET['user']) == 'Guest'){
	die();
}

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
include('classes/company.php');
include('classes/faction.php');
include('includes/fight.php');

function getURL(){
	return "http://darkdreamshost.zzl.org/index.php?user=".$_GET['user']."&date=".$_GET['date'];
}

// Activate User
$ipadd = $_SERVER['REMOTE_ADDR'];
$unique_identifier = md5(htmlspecialchars($_GET['date']));
$result = mysql_query("SELECT * FROM chars WHERE uid='$unique_identifier' ") or die(mysql_error());
if (mysql_num_rows($result)){
	$myrow = mysql_fetch_array($result);
}
else{
	mysql_query("INSERT INTO chars (uid,ipadd) VALUES('$unique_identifier','$ipadd') ") or die(mysql_error());   
}

// Initialize User
$user = new char(1,$unique_identifier);
$user->name = htmlspecialchars($_GET['user']);
if ($user->checkExp()){
	$user->levelUp();
	$user->updateStats();
}

// Aspect Modifiers
if (isset($_GET['location'])){
	$newloc = htmlspecialchars($_GET['location']);
	if ($user->location == 'market' and ($newloc == 'hunt' or $newloc == 'battle' or $oldloc == 'attack' or substr($oldloc,0,7) == 'mission')){
		die();
	}
	else {
		$oldloc = $user->location;
		$user->location = $newloc;
		if(($oldloc == 'hunt' or $oldloc == 'battle' or $oldloc == 'attack' or substr($oldloc,0,7) == 'mission') and ($newloc != 'hunt' or $newloc != 'attack' or $newloc != 'battle' or $newloc != 'victory' or $newloc != 'commendation' or $newloc != 'defeat' and substr($oldloc,0,7) != 'mission')){
			$user->setSTAT('GOLD',round($user->getSTAT('GOLD')*0.99));
		}
	}
}
include('includes/modifiers.php');
$user->loadGear();
if ($user->getSTAT('HP') > $user->getSTAT('END')){
	$user->setSTAT('HP', $user->getSTAT('END'));
}
$user->update();
if (isset($company)){
	$company->update();
}
if (isset($faction)){
	$faction->update();
}

// Battle Method
if ((isset($_GET['monsterid']) or isset($_GET['enemyid'])) and !$user->checkDeath()){

	if (!isset($foe) and isset($_GET['mhealth'])){
		if (isset($_GET['monsterid'])){
			$monsterid = (int) $_GET['monsterid'];
			$foe = new char(2,$monsterid);
		}
		if (isset($_GET['enemyid'])){
			$enemyid = (int) $_GET['enemyid'];
			$foe = new char(3,$enemyid);
		}
	}
	$foe->loadGear();
	$foe->setSTAT('HP',(int)$_GET['mhealth']);
	if (isset($_GET['ally1type']) and isset($_GET['ally1who']) and isset($_GET['ally1health'])){
		$ally1type = (int) $_GET['ally1type'];
		$ally1who = (int) $_GET['ally1who'];
		$ally1 = new char($ally1type,$ally1who);
		$ally1->loadGear();
		$ally1->setSTAT('HP',(int)$_GET['ally1health']);
		if (isset($_GET['ally2type']) and isset($_GET['ally2who']) and isset($_GET['ally2health'])){
			$ally2type = (int) $_GET['ally2type'];
			$ally2who = (int) $_GET['ally2who'];
			$ally2 = new char($ally2type,$ally2who);
			$ally2->loadGear();
			$ally2->setSTAT('HP',(int)$_GET['ally2health']);	
		}	
	}
	
	
	$middleColumn = $middleColumn.Battle($user,$foe);
	if (isset($ally1)){
		$middleColumn = $middleColumn.'<br>'.$ally1->name.' charges!<br>'.Battle($ally1,$foe);
		if (isset($ally2)){
			$middleColumn = $middleColumn.'<br>'.$ally2->name.' charges!<br>'.Battle($ally2,$foe);
		}
	}
}

// Check Death
if($user->checkDeath() and $user->location != 'tavern'){
	if ($user->location = 'battle' and isset($foe)){
		$user->arena_losses += 1;
		$foe->arena_wins += 1;
		$foe->updateArena();
	}
	$user->location = 'defeat';
	$middleColumn = '<center><b>Argh!</b><br /><br />You are too weak to carry on.<br>You will need to recover.</center>';
}
$user->update();

// Display Handler
include('includes/handler.php');

// Conditional Display
$how = 'Return to Tavern';
if($user->location == 'hunt' or $user->location == 'battle' or $user->location == 'attack' or substr($oldloc,0,7) == 'mission'){
	$how = 'Flee from Battle (1% gold loss)';
}
$return_home = '<a href="'.getURL().'&location=tavern">'.$how.'</a>';
if(!$user->checkFaction()){
	$facrep = '<br>Faction Rep: '.$user->factionrep.'/2000';
}		
$leftColumn = $user->name.' ( Level '.$user->getSTAT('LVL').' )<br><font size=2>'.$user->getSoul().'<br><br>Health: '.$user->getSTAT('HP').' / '.$user->getSTAT('END').'<br>Experience: '.$user->getSTAT('EXP').' / '.$user->maxExp.'<br>Gold: '.$user->getSTAT('GOLD').$facrep.'</font><br><br><font size=1>'.$user->getStatSheet().'</font><br><br><center><a href="'.getURL().'&location=inventory">Inventory</a></center>';

$loc = $user->location;
$result = mysql_query("SELECT * FROM imagebank WHERE whatis='$loc'") or die(mysql_error());
$row = mysql_fetch_array( $result );
$image_box = '<img src="'.$row['location'].'" width="100%" />';
if (isset($foe) and $foe->img != null and $user->location != 'victory' and $user->location != 'defeat'){
	$monster_box = '<img src="'.$foe->img.'" height="100%" />';
}
if (isset($service)){
	$monster_box = '<img src="'.$serviceimg.'" height="100%" />';
}

// Interface
echo'<html>
	<head>
		<link rel="stylesheet" href="http://godaqw.com/131-ltr.css" type="text/css" />
		<link rel="stylesheet" href="style.css" type="text/css" />
	</head>
	
	<body>
		<form id="myform">
			
			<div id="return_home">
				'.$return_home.'
			</div>
		
			<div id="image_box">
				'.$image_box.'
				<div id="monster_box">
				'.$monster_box.'
				</div>
			</div>
			
			<div id="text_box">
			
				<div class="leftColumn">
					'.$leftColumn.'
				</div>
				<div class="middleColumn">
					'.$middleColumn.'
				</div>
				<div class="rightColumn">
					'.$rightColumn.'
				</div>
				
			</div>
			
		</form>
	</body>
</html>';

?>