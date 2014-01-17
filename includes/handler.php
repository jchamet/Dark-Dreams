<?php

if($user->location == 'tavern'){
	$middleColumn = $middleColumn."Welcome, my friend, to the good ol' Tavern, the only place willing to let dirty adventurers like you, sleep! From here, you can go just about anywhere... 'cept for the cellar, haha.";
	$rightColumn = $rightColumn."<b><a href='".getURL()."&location=help'>-Help me out?</a></b><br><br><a href='".getURL()."&location=hunt'>-Outlook Forest</a><br><a href='".getURL()."&location=spirit'>-Soul Scrolls</a><br><a href='".getURL()."&location=market'>-Shining Bazaar</a><br><a href='".getURL()."&location=arena'>-Champion's Arena</a><br><a href='".getURL()."&location=factions'>-Faction Outpost</a><br><a href='".getURL()."&location=station'>-Kaze Train Station</a><br><a href='".getURL()."&location=services'>-Maxx Services</a><br>";
}
elseif($user->location == 'help'){
	$result = mysql_query("SELECT * FROM u_unlocks WHERE uid='$user->id' and locid='40'") or die(mysql_error());
	if(!mysql_num_rows($result)){
		$user->addUnlock(40);
	}
	$middleColumn = "Alright kid, let's make this quick. Welcome to Dark Dreams, a realm in constant war. If you're stupid, go waste an hour getting yourself killed in the forest. If you actually want to survive though, get a job and work for basic gear before you go adventuring. 50-50 odds with your current purse won't be enough to get you by.
					<br><br>Oh, and here's a map. It leads to my home in the city. You can pick up special scimitars there for a good price.";
	$rightColumn = "Once you've got enough gold, you might want to open up a business and purchase a license. We've got an arena too where you can fight other players. Winners get decent gold and boasting rights. Also, once you're high enough a level, go find yourself a faction to join. You get a daily cut of the profits.";
}
elseif($user->location == 'services'){
	if(!isset($_GET['selection'])){
		$middleColumn = "Hey there adventurer, if you have some gold to spare, you could be provided ";
		$result = mysql_query("SELECT * FROM services ORDER BY price") or die(mysql_error());
		while ($row = mysql_fetch_array( $result )){
			$buylink = 'Too expensive...';
			if ($user->checkSpend($row['price'])){
				$buylink = '<a href="'.getURL().'&selection='.$row['id'].'">Hire</a>';
			}
			$rightColumn = $rightColumn.$row['name'].' -> '.$buylink.'<br>';
		}	
	}
}
elseif($user->location == 'inventory'){
	$Soul = new item($user->Soul);
	$obj1 = $user->inventory->obj1;
	$obj2 = $user->inventory->obj2;
	$obj3 = $user->inventory->obj3;
	$obj4 = $user->inventory->obj4;
	$middleColumn = 'Soul: '.$Soul->name.'<br>'.$Soul->listStats().'<br><br>'.
					$obj1->name.' | <a href="'.getURL().'&remove='.$obj1->id.'">Remove</a><br>'.$obj1->listStats().'<br><br>'.
					$obj2->name.' | <a href="'.getURL().'&remove='.$obj2->id.'">Remove</a><br>'.$obj2->listStats().'<br><br>'.
					$obj3->name.' | <a href="'.getURL().'&remove='.$obj3->id.'">Remove</a><br>'.$obj3->listStats().'<br><br>'.
					$obj4->name.' | <a href="'.getURL().'&remove='.$obj4->id.'">Remove</a><br>'.$obj4->listStats().'<br>';
	$middleColumn = '<font size=2>'.$middleColumn.'</font>';
	$rightColumn = '<center><a href="'.getURL().'&location=catalogue">View Item Catalogue</a></center>';
}
elseif($user->location == 'catalogue'){
	$rightColumn = "In an old book, you find a detailed list of all weapons and armors used in the game... A pity not all can be reproduced by players... Souls aren't even listed...";
	$result = mysql_query("SELECT * FROM items WHERE mtype=1 ORDER BY item_price") or die(mysql_error());
	while ($row = mysql_fetch_array( $result )){
		$item = new item($row['id']);
		if($item->makeable == 0){
			$pre = '**';
		}
		else {
			$pre = '';
		}
		$middleColumn = $middleColumn.$pre.$item->name.' | Typical Price: '.$item->itemprice.'<br>'.$item->listStats().'<br><br>';
		$middleColumn = '<font size=2>'.$middleColumn.'</font>';
	}
}
elseif ($user->location == 'hunt' and !$user->checkDeath()){
	$rightColumn = $rightColumn.$foe->name.'<br>'.$foe->getSTAT('HP').'/'.$foe->getSTAT('END').'<br><br><font size=1>'.$foe->getStatSheet().'</font>';
	if(!$foe->checkDeath()){
		$middleColumn = $middleColumn."<br><br><center><a href='".getURL()."&monsterid=".$foe->id."&mhealth=".$foe->getSTAT('HP')."'>Keep Fighting!</a></center>";
	}
	else{
		$user->location = 'victory';
		$Gprize = (int) ($foe->getSTAT('GOLD')*rand(10,100)/100);
		$Eprize = (int) ($foe->getSTAT('EXP')*rand(10,100)/100);
		$result = mysql_query("SELECT * FROM u_unlocks WHERE uid='$user->id' and locid='$foe->locid'") or die(mysql_error());
		if($foe->locid != null and $foe->locid != 0 and !mysql_num_rows($result)){
			$user->addUnlock($foe->locid);
			$middleColumn = $middleColumn.'You unlocked a map location!<BR><BR>';
		}
		$user->chgSTAT('GOLD',$Gprize);
		$user->chgSTAT('EXP',$Eprize);
		$user->update();
		$middleColumn = $middleColumn.'<br><br>Player earned '.$Gprize.' gold and '.$Eprize.' experience!';
	}
}
elseif ($user->location == 'attack' and !$user->checkDeath()){
	$rightColumn = $rightColumn.$foe->name.'<br>'.$foe->getSTAT('HP').'/'.$foe->getSTAT('END').'<br><br><font size=1>'.$foe->getStatSheet().'</font>';
	if(!$foe->checkDeath()){
		$middleColumn = $middleColumn."<br><br><center><a href='".getURL()."&monsterid=".$foe->id."&mhealth=".$foe->getSTAT('HP')."'>Keep Fighting!</a></center>";
	}
	else{
		$user->location = 'victory';
		$Rprize = (int) round($foe->getSTAT('GOLD')*rand(10,100)/100/2);
		$Eprize = (int) ($foe->getSTAT('EXP')*rand(10,100)/100);
		$user->chgFacRep($Rprize);
		$user->chgSTAT('EXP',$Eprize);
		$user->update();
		$result = mysql_query("SELECT * FROM wars WHERE active=1 and (attacker='$user->faction' or defender='$user->faction')") or die(mysql_error());
		$row = mysql_fetch_array( $result );
		$id = $row['id'];
		$newcas = $row['casualties'] + 1;
		mysql_query("UPDATE wars SET casualties='$newcas' WHERE id='$id'") or die(mysql_error());
		$middleColumn = $middleColumn.'<br><br>Player earned '.$Rprize.' reputation and '.$Eprize.' experience!';
	}
}
elseif($user->location == 'spirit'){
	$rightColumn = $rightColumn.'A soul is a valuable thing... pick yours wisely.';
	$result = mysql_query("SELECT * FROM items WHERE mtype=2 and locid=0 ORDER BY item_price") or die(mysql_error());
	while ($row = mysql_fetch_array( $result )){
		$Soul = new item($row['id']);
		$middleColumn = $middleColumn.$Soul->name.' <a href="'.getURL().'&soul='.$Soul->id.'">-Purchase</a><br>Price: '.$Soul->itemprice.'<br>'.$Soul->listStats().'<br><br>';
	}
}
elseif ($user->location == 'market'){
	$result1 = mysql_query("SELECT * FROM items WHERE makeable=1 ORDER BY mtype DESC,item_price") or die(mysql_error());
	$options1 = '<option value="">------</option>';
	while ($row = mysql_fetch_array( $result1 )){
		$options1 = $options1.'<option value="'.$row['id'].'">'.$row['name'].'</option>';
	}
	$result2 = mysql_query("SELECT * FROM companies ORDER BY pay DESC") or die(mysql_error());
	$options2 = '<option value="">------</option>';
	while ($row = mysql_fetch_array( $result2 )){
		$options2 = $options2.'<option value="'.$row['id'].'">'.$row['brand'].'</option>';
	}
	if($user->checkCompany()){
		$companyline = "<a href='".getURL()."&location=manage'>Corporate Office</a>";
	}
	else{
		$companyline = '<a href="'.getURL().'&location=manage">Create a Company</a><br><font size=2><i>( 1000 startup required )</i></font>';
	}
	$rightColumn = $rightColumn.'<form name="marketform" method="post">Search By Product:<br>
								<select name="select1" onchange="location.href='."'".getURL()."&prod=1&querymarket='".' + this.options[this.selectedIndex].value;">'.$options1.'</select>
								<br><br>Search By Company:<br>
								<select name="select2" onchange="location.href='."'".getURL()."&comp=1&querymarket='".' + this.options[this.selectedIndex].value;">'.$options2.'</select>
								<br><br><br><center>'.$companyline.'</center>';
	
	if (isset($_GET['querymarket'])){
		$entry = (int) $_GET['querymarket'];
		if (isset($_GET['worked']) or isset($_GET['bought'])){
			$entry = (int) $_GET['worked'] + $_GET['bought'];
			$result = mysql_query("SELECT * FROM companies WHERE id='$entry'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			$company = new company($row['id']);
		}
		elseif (isset($_GET['prod'])){
			$result = mysql_query("SELECT * FROM companies WHERE product='$entry' ORDER BY mprice LIMIT 0,1") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			$company = new company($row['id']);
		}
		else{
			$result = mysql_query("SELECT * FROM companies WHERE id='$entry'") or die(mysql_error());
			$row = mysql_fetch_array( $result );
			$company = new company($row['id']);
		}
		$workline = "No jobs available.";
		if ($company->getPay() != null and $company->checkHire() and $company->checkSpend($company->getPay()*3) and $user->checkWork($company->id)){
			$workline = '<a href="'.getURL().'&comp=1&querymarket='.$entry.'&worked='.$company->id.'">Complete Contract</a>';
		}
		$itemline = "No affordable items available.";
		$item = new item($company->getProduct());
		if(!$user->checkInventory() and $item->type < 3){
			$itemline = "Your inventory is full!";
		}
		elseif ($company->getQuantity()>0 and $user->checkSpend($company->getPrice())){
			$itemline = '<a href="'.getURL().'&comp=1&querymarket='.$entry.'&bought='.$company->id.'">Purchase Product</a>';
		}
		$pay1 = $company->getPay()*3;
		$middleColumn = '<center>'.$company->getName().'<br><font size=2>Owned by: <i>'.$company->getOwnerName().'</i></font></center>';
		$middleColumn = $middleColumn.'<br><br>Product: '.$company->getProductName().'<br>Priced at: '.$company->getPrice().'<br><center>'.$itemline.'</center>';
		$middleColumn = $middleColumn.'<br><br>Hired: '.$company->aspects->hired.'/4 (resets each hour)<br>Pay: '.$pay1.' (produces 3 items)<br><center>'.$workline.'</center>';
	}
	else{
		$middleColumn = $middleColumn."Welcome to the Bazaar where you can buy player-made items, as well as work for player-made companies! I would recommend working, my friend. Creates the goods you can later buy, gives you well needed gold, and also provides you with a little bit of experience points.";
	}
}
elseif ($user->location == 'manage'){ 
	if (isset($_GET['products'])){
		$company = new company($user->getCompany());
		$result = mysql_query("SELECT * FROM items WHERE makeable='1' ORDER BY item_price") or die(mysql_error());
		$rightColumn='<font size=2>Consult the list of available licenses below. Changing which product your company produces will <b>reset</b> the number of items you have in stock.</font>';
		while ($row = mysql_fetch_array( $result )){
			$item = new item($row['id']);
			$checked = '';
			if ($company->getProduct() == $item->id){
				$checked = 'CHECKED';
			}
			$middleColumn = $middleColumn.'<input type="radio" name="productchoice" onClick="'."location.href='".getURL()."&location=manage&productchoice=".$item->id."'".';" '.$checked.' />'.$item->name.' '.$item->licprice.'<br>';
			if ($item->type == 3){
				$middleColumn = $middleColumn.'Healing: '.$item->stats->END.'<br>';
			}
			else {
				$middleColumn = $middleColumn.$item->listStats().'<br>';
			}
		}
	}
	else if($user->checkCompany()){
		$company = new company($user->getCompany());					
		$middleColumn = '<center><b>'.$company->getName().'</b><br><input  type="text" name="cname" value="'.$company->getName().'" maxlength="20" size="30"></center>
			<table><tr><td>
			Profits:</td><td>'.$company->aspects->profit.'</td></tr><tr><td>
			Corporate Funds:</td><td>'.$company->aspects->funds.'</td></tr><tr><td>
			<select name="etype">
			<option value="1">Deposit</option>
			<option value="-1">Withdraw</option>
			</select>
			</td><td><input method=text name="exchange" value=0 size="8" /></td></tr><tr><td>
			Product:</td><td><a href="'.getURL().'&products=1"><b>'.$company->getProductName().'</b></td></tr><tr><td>
			Current Stock:</td><td>'.$company->getQuantity().'</td></tr><tr><td>
			Export:</td><td><input method=text name="quanticeq" value=0 size="8" /></td></tr><tr><td>
			Product Price:</td><td><input method=text name="price" value='.$company->getPrice().' size="8" /></td></tr><tr><td>
			Worker Wages:</td><td><input method=text name="pay" value='.$company->getPay().' size="8" /></td></tr></table>
			<center>
			<button type="button" onclick="var e = myform.elements['."'etype'".'].value;var f = myform.elements['."'exchange'".'].value;var h = myform.elements['."'quanticeq'".'].value;var i = myform.elements['."'price'".'].value;var j = myform.elements['."'pay'".'].value;var k = myform.elements['."'cname'".'].value;'."location.href='".getURL()."&type='+e+'&exchange='+f+'&quanticeq='+h+'&price='+i+'&pay='+j+'&cname='+k".';">Execute Changes</button>';
		$rightColumn = '<a href="'.getURL().'&location=market">-Back to Bazaar</a>';
	}
	else{
		$middleColumn = '<center><b>Company Name</b>
		<input  type="text" name="ncompname" maxlength="20" size="30">
		<button type="button" onclick="var e = myform.elements['."'ncompname'".'].value;location.href='."'".getURL()."&location=manage&ncompname='+e".';">Create Company</button><br><br>
		<font size=2> Founding and maintaining a company costs a lot of gold. To start a company, one must pay 1000 gold up front and purchase the license necessary for the production of the desired goods. Also note that you will need to hire other characters as employees, and that they will need salaries.</font>';
		$rightColumn = '<a href="'.getURL().'&location=market">-Back to Bazaar</a>';
	}
}
elseif($user->location == 'arena'){
	$middleColumn = $middleColumn."Hear the roaring crowds? Listen to how they cry for you to spill the blood of your peers in the name of glory! Take up arms! Defend your honor, or leave in shame! This is no place for the weak. Oh, and victory grants a lot of gold. No experience though.
						<br><br><center><table width='100%'><tr><td><center><b>Victories</td><td><center><b>Losses</td></tr><tr><td><center>".$user->arena_wins."</td><td><center>".$user->arena_losses."</td></table></center>";
	$rightColumn = $rightColumn."<a href='".getURL()."&location=battle'>-Challenge a Foe</a>";
}
elseif($user->location == 'battle' and !$user->checkDeath() and isset($foe)){
	if ($user->name == $foe->name){
		$foe->name = "Doppelganger ".$foe->name;
	}
	$rightColumn = $rightColumn.$foe->name.' ( Level '.$foe->getSTAT('LVL').' )<br>'.$foe->stats->HP.'/'.$foe->stats->END.'<br><br><font size=1>'.$foe->getStatSheet().'</font>';
	if(!$foe->checkDeath()){
		$middleColumn = $middleColumn."<br><br><center><a href='".getURL()."&enemyid=".$foe->id."&mhealth=".$foe->getSTAT('HP')."'>Keep Fighting!</a></center>";
	}
	else{
		$user->location = 'victory';
		$prize = 200*($foe->stats->LVL - $user->stats->LVL);
		if ($prize <= 0){
			$prize = 180;
		}
		$user->chgSTAT('GOLD',$prize);
		$user->arena_wins += 1;
		$foe->arena_losses += 1;
		$user->update();
		$foe->updateArena();
		$middleColumn = $middleColumn.'<br><br>Player earned '.$prize.' gold!';
	}
}
elseif($user->location == 'factions'){
	if($user->getSTAT('LVL') < 5){
		$rightColumn = '<b>You are too weak to join any factions!</b><br>';
	}
	$result = mysql_query("SELECT * FROM wars WHERE active=1 ORDER BY id") or die(mysql_error());
	while ($row = mysql_fetch_array( $result )){
		$counter++;
		if ($counter == 1){
			$faction1 = new faction($row['attacker']);
			$faction2 = new faction($row['defender']);
			$casualties1 = $row['casualties'];
		}
		else {
			$faction3 = new faction($row['attacker']);
			$faction4 = new faction($row['defender']);
			$casualties2 = $row['casualties'];
		}
	}			
	$middleColumn = '<center>Current Wars</center><br><br>
					<table><tr><td><b>Attacker</td><td><b>Defender</td><td><b>Casualties</td></tr>
					<tr><td>'.$faction1->aspects->name.'</td><td>'.$faction2->aspects->name.'</td><td>'.$casualties1.'</td></tr>
					<tr><td>'.$faction3->aspects->name.'</td><td>'.$faction4->aspects->name.'</td><td>'.$casualties2.'</td></tr>
					</table><br><br>';
	$result = mysql_query("SELECT * FROM factions ORDER BY id") or die(mysql_error());
	while ($row = mysql_fetch_array( $result )){
		$faction = new faction($row['id']);
		$middleColumn = $middleColumn.'Visit the <a href="'.getURL().'&faction_outside=1&factionid='.$faction->id.'">'.$faction->aspects->name.'</a><br>';
	}
	$rightColumn = $rightColumn.'Factions are a great place for players to unite their efforts in dominationg other factions. Joining a faction costs gold, but each day you make off with a bit of what is left in the account, haha.';
}
elseif(isset($_GET['faction_outside'])){
	$rightColumn = "<center><b>".$faction->aspects->name."</b></center><br><br>Influence: ".$faction->getInfluence()."<br>Army Size: ".$faction->getArmyAprox()."<br>Wealth: ".$faction->getFundsAprox()."<br>Member Count: ".$faction->getMembers();
	$rightColumn = $rightColumn."<br><br><a href='".getURL()."&location=factions'>-Return to Outpost</a><br>";
	if($user->faction == $faction->id){
		$rightColumn = $rightColumn."<a href='".getURL()."&enter=".$faction->id."'>-Enter Capital</a>";
	}
	elseif($user->faction == 0){
		if($user->checkSpend(1000)){
			$rightColumn = $rightColumn."<a href='".getURL()."&join=".$faction->id."'>-Join Faction</a>";
		}
		else{
			$rightColumn = $rightColumn."<font size=2>You can't afford to join!</font>";
		}
	}
	else{
		$rightColumn = $rightColumn."<font size=2>You are in another faction!</font>";
	}
	$middleColumn = $faction->aspects->legacy;			
}
elseif(substr($user->location, 0, 10) == 'throneroom'){
	$faction = new faction($user->faction);
	if($faction->enemy > 0){
		$launch = "<br><a href='".getURL()."&location=attack'>-Launch Attack</a>";
	}
	$rightColumn = $rightColumn."<a href='".getURL()."&faction_outside=1&factionid=".$user->faction."'>-Return to Outskirts</a>".$launch."<br><a href='".getURL()."&location=citystate'>-Faction Missions</a><br><a href='".getURL()."&location=armory'>-Faction Armory</a><br><a href='".getURL()."&abandon=1'>-Abandon Faction</a>";	
	$middleColumn = '<center><b>'.$faction->aspects->name.'</b><br><br>
		<table width=100%><tr><td><center><b>Funds</b></td><td><center><b>Military</b></td><td><center><b>Influence</b></td></tr>
		<tr><td><center>'.$faction->getFunds().'</td><td><center>'.$faction->getArmy().'/'.$faction->armylimit.'</td><td><center>'.$faction->getInfluence().'/1000</td></tr>
		<tr><td>+<input method=text name="funds" value=0 size="6" /></td><td>+<input method=text name="army" value=0 size="6" /></td><td>+<input method=text name="influence" value=0 size="6" /></td></tr></table>
		<button type="button" onclick="var e = myform.elements['."'funds'".'].value;var f = myform.elements['."'army'".'].value;var h = myform.elements['."'influence'".'].value;'."location.href='".getURL()."&funds='+e+'&army='+f+'&influence='+h".';">Contribute</button></center>
		<br><br>Soldiers cost 200 gold and 10 faction rep each. Influence costs 500 gold and 1 faction rep per points. Influence is restored at the end of a war.';
}
elseif($user->location == 'citystate'){
	$faction = new faction($user->faction);
	$middleColumn = "Would you believe it if I told you that the local blasphemers were once more causing us trouble? This must stop! You'll earn reputation in your faction if you beat any of the missions...";
	$result = mysql_query("SELECT * FROM quests WHERE mtype=2 ORDER BY prize") or die(mysql_error());
	$rightColumn = "<a href='".getURL()."&enter=".$faction->id."'>-Return to Palace</a><br><br>";
	while ($row = mysql_fetch_array( $result )){
		$rightColumn = $rightColumn.'<a href="'.getURL().'&location=mission'.$row['id'].'">-'.$row['title'].'</a><br>';
	}
}
elseif(substr($user->location, 0, 7) == 'mission'){
	$questid = substr($user->location, 7, 8);
	$result = mysql_query("SELECT * FROM quests WHERE id='$questid'") or die(mysql_error());
	$row = mysql_fetch_array( $result );
	$rightColumn = $rightColumn.$foe->name.'<br>'.$foe->getSTAT('HP').'/'.$foe->getSTAT('END').'<br><br><font size=1>'.$foe->getStatSheet().'</font>';
	$rightColumn = $rightColumn.'<br><br>'.$length.' / '.$row['length'];
	if(isset($ally1)){
		if($ally1->checkDeath()){
			unset($ally1);
		}
		else{
			$ally1info = 'ally1type='.$ally1type.'&ally1who='.$ally1who.'&ally1health='.$ally1->getSTAT('HP').'&';
		}
	}
	if(isset($ally2)){
		if($ally2->checkDeath()){
			unset($ally2);
		}
		else{
			$ally2info = 'ally2type='.$ally2type.'&ally2who='.$ally2who.'$ally2health='.$ally2->getSTAT('HP').'&';
		}
	}
	if($foe->checkDeath()){
		if ($length == 1){
			unset($foe);
			$user->location = 'commendation';
			$Rprize = (int) $row['prize'];
			$user->chgFacRep($Rprize);
			$user->update();
			$middleColumn = $middleColumn.'<br><br>Player earned '.$Rprize.' reputation!';
			$rightColumn = "<a href='".getURL()."&location=citystate'>-Return to Missions</a>";
		}
		else{
			$length--;
			$rightColumn = '';
			$middleColumn = "<center><a href='".getURL()."&length=".$length."&".$ally1info.$ally2info."'>Next!</a></center>";
		}
	}
	else{
		$middleColumn = $middleColumn."<br><br><center><a href='".getURL()."&length=".$length."&".$ally1info.$ally2info."monsterid=".$foe->id."&mhealth=".$foe->getSTAT('HP')."'>Keep Fighting!</a></center>";
	}
}
elseif($user->location == 'armory'){
	$faction = new faction($user->faction);
	$rightColumn = $rightColumn."Welcome to the armory, good sir! Here you may purchase goods produced exclusively by our faction (for a nominal fee, of course)!<br><br><a href='".getURL()."&enter=".$faction->id."'>-Return to Palace</a>";
	$result = mysql_query("SELECT * FROM items WHERE facid='$faction->id' ORDER BY item_price") or die(mysql_error());
	while ($row = mysql_fetch_array( $result )){
		$item = new item($row['id']);
		$middleColumn = $middleColumn.$pre.$item->name.' | Price: '.$item->itemprice.' | Rep: '.$item->licprice.'<br><center><b><a href="'.getURL().'&buyfacloot='.$item->id.'">Purchase</a></b></center>'.$item->listStats().'<br><br>';
		$middleColumn = '<font size=2>'.$middleColumn.'</font>';
	}
}
elseif($user->location == 'station'){
	$result = mysql_query("SELECT * FROM u_unlocks WHERE uid='$user->id' ORDER BY locid") or die(mysql_error());
	while ($row = mysql_fetch_array($result)){
		$locationid = (int) $row['locid'];
		$result1 = mysql_query("SELECT * FROM u_hiddens WHERE locid='$locationid'") or die(mysql_error());
		$row1 = mysql_fetch_array($result1);
		$rightColumn = $rightColumn.'<a href="'.getURL().'&location='.$row1['location'].'">-'.$row1['name'].'</a><br>';
	}
	$middleColumn = "Welcome to the Kaze Train Station, your gateway to the world, and all of its secrets.";
}
elseif(isset($_GET['hiddenmission'])){
	$questid = (int) $_GET['hiddenmission'];
	$result = mysql_query("SELECT * FROM quests WHERE id='$questid'") or die(mysql_error());
	$row = mysql_fetch_array( $result );
	$rightColumn = $rightColumn.$foe->name.'<br>'.$foe->getSTAT('HP').'/'.$foe->getSTAT('END').'<br><br><font size=1>'.$foe->getStatSheet().'</font>';
	$rightColumn = $rightColumn.'<br><br>'.$length.' / '.$row['length'];
	if(isset($ally1)){
		$ally1info = '&ally1type='.$ally1type.'&ally1who='.$ally1who.'&ally1health='.$ally1->getSTAT('HP');
	}
	if(isset($ally2)){
		$ally2info = '&ally2type='.$ally2type.'&ally2who='.$ally2who.'$ally2health='.$ally2->getSTAT('HP');
	}
	if($foe->checkDeath()){
		if ($length == 1){
			unset($foe);
			$user->location = 'loot';
			$Gprize = (int) $row['prize'];
			$user->chgSTAT('GOLD',$Gprize);
			$user->update();
			$middleColumn = $middleColumn.'<br><br>Player earned '.$Gprize.' gold!';
		}
		else{
			$length--;
			$rightColumn = '';
			$middleColumn = "<center><a href='".getURL()."&hiddenmission=".$row['id']."&length=".$length.$ally1info.$ally2info."'>Next!</a></center>";
		}
	}
	else{
		$middleColumn = $middleColumn."<br><br><center><a href='".getURL()."&hiddenmission=".$row['id']."&length=".$length."&".$ally1info.$ally2info."monsterid=".$foe->id."&mhealth=".$foe->getSTAT('HP')."'>Keep Fighting!</a></center>";
	}
}


// Hidden Locations
$result1 = mysql_query("SELECT * FROM u_hiddens WHERE location='$user->location'") or die(mysql_error());
if(mysql_num_rows($result1) and !isset($_GET['hiddenmission'])){
	$row1 = mysql_fetch_array($result1);
	if ($row1['mtype'] == 1){
		$locid = (int) $row1['locid'];
		$rightColumn = $row1['desc'];
		$result = mysql_query("SELECT * FROM items WHERE locid='$locid' ORDER BY item_price") or die(mysql_error());
		while ($row = mysql_fetch_array( $result )){
			$item = new item($row['id']);
			$middleColumn = $middleColumn.$pre.$item->name.' | Price: '.$item->itemprice.' | <a href="'.getURL().'&buyloot='.$item->id.'">Purchase</a><br>'.$item->listStats().'<br><br>';
			$middleColumn = '<font size=2>'.$middleColumn.'</font>';
		}
	}
	elseif($row1['mtype'] == 2){
		$locid = (int) $row1['locid'];
		$middleColumn = $row1['desc'];
		$result = mysql_query("SELECT * FROM quests WHERE locid='$locid' ORDER BY id") or die(mysql_error());
		while ($row = mysql_fetch_array( $result )){
			$rightColumn = $rightColumn.'<a href="'.getURL().'&hiddenmission='.$row['id'].'">'.$row['title'].'</a><br>';
		}
	}
}

?>