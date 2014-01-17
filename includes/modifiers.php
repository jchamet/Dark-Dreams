<?php
function alertSys($string){
	echo'?><script type="text/javascript">alert("'.$string.'");	</script><?php';
}

// Tavern
if($user->location == 'tavern' and $user->checkDeath()){
	$user->setSTAT('HP',1);
}

// Inventory
if ($user->location == "inventory"){
	if (isset($_GET['remove'])){
		$removed = new item((int) $_GET['remove']);
		$user->removeItem($removed->id);
		$user->chgSTAT('GOLD',$removed->itemprice*0.75);
	}
}
// Soul
if ($user->location == "spirit"){
	if(isset($_GET['soul'])){
		$soul = (int) $_GET['soul'];
		$this_soul = new item($soul);
		if($user->checkSpend($this_soul->itemprice)){
			$user->chgSTAT('GOLD',-$this_soul->itemprice);
			$user->Soul = $soul;
		}
	}
}

// Market Buy/Work
if ($user->location == "market"){
	if (isset($_GET['worked'])){
		$company = new company((int) $_GET['worked']);
		if ($user->checkWork($company->id) and $company->checkSpend($company->getPay()*3) and $company->checkHire()){
			$user->addJob($company->id);
			$user->chgSTAT('GOLD',$company->getPay()*3);
			$user->chgSTAT('EXP',10);
			$company->chgFunds(-$company->getPay()*3);
			$company->chgQuantity(3);
			$company->aspects->hired += 1;
		}
	}
	else if (isset($_GET['bought'])){
		$company = new company((int) $_GET['bought']);
		$price = $company->getPrice();
		if ($user->checkSpend($price) and $company->getQuantity() > 0){
			$user->chgSTAT('GOLD',-$price);
			$company->chgFunds($price);
			$company->chgQuantity(-1);
			$item = new item($company->getProduct());
			if ($item->type == 3){
				$user->chgSTAT('HP',$item->stats->END);
			}
			else if ($user->checkInventory()){
				$user->addItem($company->getProduct());
			}
		}
	}
}
// Company Manage/Create
if (isset($_GET['ncompname'])){
	if (!$user->checkSpend(1000)){
		alertSys("You do not have enough funding!");
		$user->location = "market";
		}
	else {
		$compname = str_replace("'", '', htmlspecialchars($_GET['ncompname']));
		if ($user->checkCompany()){
			alertSys("You already own a company!");
			$user->location = "market";
			}
		else {
			$result = mysql_query("SELECT * FROM companies WHERE brand='$compname'") or die(mysql_error());
			if (mysql_num_rows($result) OR strlen($compname) < 8 OR strlen($compname) > 60){
				alertSys("Company name already in use, or is too short (or even too long).\nPlease pick another one.");		
				$user->location = "market";
				die();
			}
			else {
				$user->chgSTAT('GOLD',-1000);
				mysql_query("INSERT INTO companies (ownerid, brand) VALUES('$user->id', '$compname') ") or die(mysql_error());
				$user->location = "manage";
			}
		}
	}
}
// Manage Company
if ($user->checkCompany() and isset($_GET['type']) AND isset($_GET['exchange']) AND isset($_GET['quanticeq']) AND isset($_GET['price']) AND isset($_GET['pay']) AND isset($_GET['cname'])){
	$type = (int) $_GET['type'];
	$company = new company($user->getCompany());
	$exchange = abs((int) $_GET['exchange']);
	$exp = abs((int) $_GET['quanticeq']);
	$company->setPrice(abs((int) $_GET['price']));
	$company->setPay(abs((int) $_GET['pay']));
	$company->aspects->brand = htmlspecialchars($_GET['cname']);
	if (($user->checkSpend($exchange) AND $type > 0) OR ($company->checkSpend($exchange) AND $type < 0)){
		if ((int) $_GET['type'] < 0){
			$exchange = -$exchange;
		}
		$user->chgSTAT('GOLD',-$exchange);
		$company->addFunds($exchange);
		if ($exp != 0 and $exp <= $company->getQuantity()){
			$item = new item($company->getProduct());
			$company->chgQuantity(-$exp);
			$company->chgFunds($exp*$item->itemprice/2);
		}
	}
}
// Manage Product
else if (isset($_GET['productchoice'])){
	$item = new item((int) $_GET['productchoice']);
	$company = new company($user->getCompany());
	if ($item->id != $company->getProduct() and $company->checkSpend($item->licprice)){
		$company->chgFunds(-$item->licprice);
		$company->setProduct($item->id);	
		$company->aspects->quantity = 0;	
	}
}
// Faction Aspect
if(isset($_GET['factionid'])){
	$faction = new faction((int) $_GET['factionid']);
	$user->location = $faction->aspects->name;
}
if(isset($_GET['join']) and $user->checkFaction() and $user->checkSpend(1000)){
	$faction = new faction((int) $_GET['join']);
	$faction1 = new faction(1);
	$faction2 = new faction(2);
	$faction3 = new faction(3);
	$faction4 = new faction(4);
	if($faction->getMembers() <= ($faction1->getMembers() + $faction2->getMembers() + $faction3->getMembers() + $faction4->getMembers())/3){
		$user->faction = $faction->id;
		$faction->chgFunds(1000);
		$user->chgSTAT('GOLD',-1000);
		switch($faction->id){
			case 1:
				$user->location = 'throneroom 1';
				break;
			case 2:
				$user->location = 'throneroom 2';
				break;
			case 3:
				$user->location = 'throneroom 3';
				break;
			case 4:
				$user->location = 'throneroom 4';
				break;
		}
	}
}
if(isset($_GET['enter'])){
	$faction = new faction((int) $_GET['enter']);
	switch($faction->id){
		case 1:
			$user->location = 'throneroom 1';
			break;
		case 2:
			$user->location = 'throneroom 2';
			break;
		case 3:
			$user->location = 'throneroom 3';
			break;
		case 4:
			$user->location = 'throneroom 4';
			break;
	}
}
if(isset($_GET['funds']) and isset($_GET['army']) and isset($_GET['influence'])){
	$faction = new faction($user->faction);
	$funds = abs((int) $_GET['funds']);
	if($user->checkSpend($funds)){
		$user->chgSTAT('GOLD',-$funds);
		$faction->chgFunds($funds);
	}
	$army = abs((int) $_GET['army']);
	$priceg = $army*200;	$pricer = $army*10;
	if($user->checkSpendR($pricer) and $faction->checkSpend($priceg)){
		$user->chgFacRep(-$pricer);
		$faction->chgFunds(-$priceg);
		$faction->chgArmy($army);		
	}
	$influence = abs((int) $_GET['influence']);
	$priceg = $influence*500;	$pricer = $influence;
	if($user->checkSpendR($pricer) and $faction->checkSpend($priceg) and ($faction->getInfluence() + $influence) <= 1000){
		$user->chgFacRep(-$pricer);
		$faction->chgFunds(-$priceg);
		$faction->chgInfluence($influence);		
	}
}
if(isset($_GET['abandon'])){
	$user->location = 'abandon';
	$user->factionrep = 0;
	$user->faction = 0;
}

if (isset($_GET['buyloot'])){
	$item = new item((int) $_GET['buyloot']);
	if ($user->checkSpend($item->itemprice)){
		if($item->type == 2){
			$user->chgSTAT('GOLD',-$item->itemprice);
			$user->Soul = $item->id;
		}
		elseif($user->checkInventory()){
			$user->chgSTAT('GOLD',-$item->itemprice);
			$user->addItem($item->id);
		}
	}
}
if (isset($_GET['buyfacloot'])){
	$item = new item((int) $_GET['buyfacloot']);
	if ($user->checkSpend($item->itemprice) and $user->checkSpendR($item->licprice) and $user->checkInventory()){
		$user->chgSTAT('GOLD',-$item->itemprice);
		$user->chgFacRep(-$item->licprice);
		$user->addItem($item->id);
	}
}
	
// Hunt Aspect
else if ($user->location == 'hunt'){
	if (!isset($_GET['monsterid'])){

		$result = mysql_query("SELECT COUNT(*) FROM monsters") or die(mysql_error());
		$validrows = mysql_fetch_array($result);
		$number = $validrows['COUNT(*)'];
			
		$which = rand(1, $number);
		$which0 = $which - 1;	
		$result = mysql_query("SELECT * FROM monsters ORDER BY id LIMIT $which0,$which") or die(mysql_error());
		$monsterrow = mysql_fetch_array( $result );
		
		$foe = new char(2,$monsterrow['id']);
		$foe->setSTAT('HP',$foe->getSTAT('END'));
		$mhealth = $foe->getSTAT('END');
	}
}
// Arena Aspect
else if ($user->location == 'battle'){
	if (!isset($_GET['enemyid'])){

		$result = mysql_query("SELECT COUNT(*) FROM chars") or die(mysql_error());
		$validrows = mysql_fetch_array($result);
		$number = $validrows['COUNT(*)'];
			
		$which = rand(1, $number);
		$which0 = $which - 1;	
		$result = mysql_query("SELECT * FROM chars ORDER BY id LIMIT $which0,$which") or die(mysql_error());
		$charrow = mysql_fetch_array( $result );
		
		$foe = new char(3,$charrow['id']);
		$mhealth = $foe->getSTAT('END');
	}
}
// Attack Aspect
else if ($user->location == 'attack'){
	if (!isset($_GET['monsterid'])){

		$faction = new faction($user->faction);
		$factione = new faction($faction->enemy);
		$warrior = $factione->aspects->typicalwarrior;
			
		$result = mysql_query("SELECT * FROM monsters WHERE id='$warrior'") or die(mysql_error());
		$charrow = mysql_fetch_array( $result );
		
		$foe = new char(2,$charrow['id']);
		$mhealth = $foe->getSTAT('END');
	}
}
// Mission Aspect
else if (substr($user->location, 0, 7) == 'mission'){
	if ($user->checkFacNum()){
		$quest = substr($user->location, 7, 8);
		$result5 = mysql_query("SELECT * FROM quests WHERE id='$quest'") or die(mysql_error());
		$row5 = mysql_fetch_array( $result5 );
		if (!isset($_GET['length']) and $user->checkFacNum()){
			$user->factionquestnum += 1;
			$length = $row5['length'];
			$party = $row5['partysize'];
			$faction = new faction($user->faction);
			
			// Player Fighters
			if ($faction->getMembers() > 0 AND $party > 1){
				$first = rand(1,$faction->getMembers());
				if ($faction->getMembers() > 1 AND $party > 2){
					$second = rand(1,$faction->getMembers());
					while ($first == $second){
						$second = rand(1,$faction->getMembers());
					}
				}
				$breaker = 0;
				$counter = 0;
				$result = mysql_query("SELECT * FROM chars WHERE faction='$faction->id' AND id<>'$user->id'") or die(mysql_error());
				while($rows = mysql_fetch_array($result) AND $breaker == 0){
					$counter++;
					if (isset($_GET['ally1type']) AND (!isset($second) OR isset($_GET['ally2type']))){
						$breaker = 1;
					}
					else if ($counter == $first){
						$ally1type = 3;
						$ally1who = $rows['id'];
						$ally1 = new char(3,$ally1who);
						$ally1->setSTAT('HP',$ally1->getSTAT('END'));
					}
					else if (isset($second) AND $counter == $second){
						$ally2type = 3;
						$ally2who = $rows['id'];
						$ally2 = new char(3,$ally2who);
						$ally2->setSTAT('HP',$ally2->getSTAT('END'));
					}
				}
			}
			// NPC Fighters
			$NPC = $faction->typicalwarrior;
			$result = mysql_query("SELECT * FROM monsters WHERE id='$NPC'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			if (!isset($first) AND $party > 1){
				$ally1type = 2;
				$ally1who = $NPC;
				$ally1 = new char(2,$ally1who);
				$ally1->setSTAT('HP',$ally1->getSTAT('END'));
			}
			if (!isset($second) AND $party > 2){
				$ally2type = 2;
				$ally2who = $NPC;
				$ally2 = new char(2,$ally2who);
				$ally2->setSTAT('HP',$ally2->getSTAT('END'));
			}
		}
		else {
			$length = (int) $_GET['length'];
			if (isset($_GET['ally1type'])){
				$ally1type = (int) $_GET['ally1type'];
				$ally1who = (int) $_GET['ally1who'];
				$ally1 = new char($ally1type,$ally1who);
				if (isset($_GET['ally2type'])){
					$ally2type = (int) $_GET['ally2type'];
					$ally2who = (int) $_GET['ally2who'];
					$ally1 = new char($ally2type,$ally2who);
				}
			}
		}
		if ($length == 1 AND $row5['boss'] != 0){
			$foe = new char(2,$row5['boss']);
		}
		else {
			$foe = new char(2,$row5['minion']);
		}
		if(!isset($_GET['mhealth'])){
			$mhealth = $foe->getSTAT('END');
		}
	}
}
else if (isset($_GET['hiddenmission'])){
	$questid = (int) $_GET['hiddenmission'];
	$result5 = mysql_query("SELECT * FROM quests WHERE id='$questid'") or die(mysql_error());
	$row5 = mysql_fetch_array( $result5 );
	if (!isset($_GET['length'])){
		$length = $row5['length'];
		$party = $row5['partysize'];
		
		// Ally Fighters
		$ally = $row5['typicalally'];
		if ($party > 1){
			$first = $ally;
			if ($party > 2){
				$second = $ally;
			}
		}
		
		$result = mysql_query("SELECT * FROM monsters WHERE id='$ally'") or die(mysql_error());
		$row = mysql_fetch_array( $result );
		if (!isset($first) AND $party > 1){
			$ally1type = 2;
			$ally1who = $ally;
			$ally1 = new char(2,$ally1who);
			$ally1->setSTAT('HP',$ally1->getSTAT('END'));
		}
		if (!isset($second) AND $party > 2){
			$ally2type = 2;
			$ally2who = $ally;
			$ally2 = new char(2,$ally2who);
			$ally2->setSTAT('HP',$ally2->getSTAT('END'));
		}
	}
	else {
		$length = (int) $_GET['length'];
		if (isset($_GET['ally1type'])){
			$ally1type = (int) $_GET['ally1type'];
			$ally1who = (int) $_GET['ally1who'];
			$ally1 = new char($ally1type,$ally1who);
			if (isset($_GET['ally2type'])){
				$ally2type = (int) $_GET['ally2type'];
				$ally2who = (int) $_GET['ally2who'];
				$ally1 = new char($ally2type,$ally2who);
			}
		}
	}
	if ($length == 1 AND $row5['boss'] != 0){
		$foe = new char(2,$row5['boss']);
	}
	else {
		$foe = new char(2,$row5['minion']);
	}
	if(!isset($_GET['mhealth'])){
		$mhealth = $foe->getSTAT('END');
	}
}

if(isset($_GET['selection'])){
	$selected = (int) $_GET['selection'];	
	$result = mysql_query("SELECT * FROM services WHERE id='$selected'") or die(mysql_error());
	$row = mysql_fetch_array( $result );
	$user->chgSTAT('GOLD',-$row['price']);
	$middleColumn = $row['desc'];
	$service = true;
	$serviceimg = $row['image'];
}


?>