<?php
	
if (isset($_GET['inventory'])){
	$Soul = new item($user->Soul);
	$obj1 = new item($user->inventory->obj1);
	$obj2 = new item($user->inventory->obj2);
	$obj3 = new item($user->inventory->obj3);
	$obj4 = new item($user->inventory->obj4);
	$middleColumn = $Soul->name.' <a href="'.$getURL().'&remove="'.$Soul->id.'>Remove</a><br>'.$Soul->listStats().'<br><br>'.$obj1->name.' <a href="'.$getURL().'&remove="'.$obj1->id.'>Remove</a><br>'.$obj1->listStats().'<br><br>'.$obj2->name.' <a href="'.$getURL().'&remove="'.$obj2->id.'>Remove</a><br>'.$obj2->listStats().'<br><br>'.$obj3->name.' <a href="'.$getURL().'&remove="'.$obj3->id.'>Remove</a><br>'.$obj3->listStats().'<br><br>'.$obj4->name.' <a href="'.$getURL().'&remove="'.$obj4->id.'>Remove</a><br>'.$obj4->listStats().'<br>';
}

if (isset($_GET['quest'])){
	if (isset($_GET['engage'])) {
		$view = (int) $_GET['engage'];
		$result = mysql_query("SELECT * FROM quests WHERE id='$view'") or die(mysql_error());
		$row = mysql_fetch_array($result);
		$image = $row['image'];
		if (!$user->checkDeath() and $monster->checkDeath() and $distance == 0){
			$image = 43;
			$view = $_GET['tquest'];
			$prize = $row5['prize'];
			$user->chgSTAT('GOLD',$prize);
			$middleColumn = '<center><b>Congratulations!</b><br /><br />You earned: '.$prize.' gold</center>';
		}
		else {
			$bnum = 0;
			if (isset($row['boss']) AND $row['boss'] != 0){
				$bnum = 1;
			}
			$minions = $distance - $bnum;
			$bosses = $bnum;
			$middleColumn += '<center><font size=2>Minions left: '.$minions.' / Bosses left: '.$bosses.'</font><table>';	
			
			$rightColumn = '<font size=2><center><b>'.$monster->name.'<br/>
						<div id="ProfileBox" style="height:100px;width:100px;overflow:none;" ><img src="'.$monster->image.'" width="100%"/></div></center></font><br />
						<font size=2><b>Health:</b> '.$monter->getSTAT('HP').' / '.$monster->getSTAT('END').'<br><br>'.$monster->getStatSheet;
		}
	}
	
if (isset($_GET['arena'])){
	$user->location = 'arena';
	if (isset($_GET['explain'])){
		$width = 200;
		$middleColumn += "<font size=1>Welcome to the arena, kid, the place to smash skulls if you've got nothin' better to do. If you end up beating a hired Defender, we'll pay you a handsome sum of gold, which should cover the damages. O' course, you can always be a Defender yourself... they get paid for their services, and we cover the damage for 'em. Just a note: they lose money if they lose us battles! Amount of money earned and lost depends on your level.</font><br /><br />".indent('<font size=2>Prize money: '.(100 + $clevel*10).'<br />Defender earnings: '.($clevel*20).' per hour<br />Defender penalty: '.($clevel * 2).' per loss</font>');
		$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&arena=1'".');">Return to Arena</a>';
	}
	else if (isset($_GET['records'])){
		$image = 8;
		$result = mysql_query("SELECT * FROM arena WHERE ownerid='$myid'") or die(mysql_error());
		$arenarow = mysql_fetch_array( $result );
		$wins = $arenarow['wins'];
		$losses = $arenarow['losses'];
		$total = $wins + $losses;
		$middleColumn += "<font size=2>Ah, want to have a look at your personal records, eh? Here, number of matches, wins, and defeats. We include results from defending matches.</font><br /><br />".indent('<font size=2>Wins: '.($wins).'<br />Defeats: '.($losses).'<br />Total Matches: '.($total).'</font>');
		$middleColumn += '<a href="'.getURL().'&arena=1">Return to Arena</a>';
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
			
			$expgain = rand( 2 * $clevel, 4 * $clevel);
			$profit = (100 + $clevel*10);
			$mgold = $mrow['gold'] - ($mrow['level'] * 2);
			mysql_query("UPDATE chars SET gold='$mgold' WHERE id='$defender'") or die(mysql_error());
			
			$cgold = $cgold + $profit;
			$cexp = $cexp + $expgain;
			mysql_query("UPDATE chars SET gold='$cgold' WHERE id='$cid'") or die(mysql_error());
			mysql_query("UPDATE chars SET experience='$cexp' WHERE id='$cid'") or die(mysql_error());
			$middleColumn += "Victory!<br />You gained ".$profit." gold and ".$expgain." experience!";	
			
			$wins = $crow['wins'] + 1;
			mysql_query("UPDATE arena SET wins='$wins' WHERE ownerid='$myid'") or die(mysql_error());
			$losses = $erow['losses'] + 1;
			mysql_query("UPDATE arena SET losses='$losses' WHERE ownerid='$rivid'") or die(mysql_error());						
		}
		else if ($chealth <= 0){
			$image = 4;
			$middleColumn += "<font size=2>You lost the battle!</font>";
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
			$middleColumn += '<center><table>';
			while ($number < 4){
				$checkit = '';
				if ($number == $checked){
					$checkit = 'CHECKED';
				}
				$middleColumn += $next.'<tr><td><input type="radio" name="attackchoice" onClick="displayNewScreen('."'myDiv','main.php?type=3&arena=1&fight=1&defender=".$defender."&mhealth=".$mhealth."&edamage=".$edamage."&edefense=".$edefense."&next=1&choice=".$number."'".');"  '.$checkit.' /></td><td>'.$move[$number].'</td></tr>';
				$number++;
			}	
			$middleColumn += $next.'</table></center>';
			
			$result = mysql_query("SELECT * FROM chars WHERE id='$defender'") or die(mysql_error());
			$mrow = mysql_fetch_array($result);
			
			$middleColumn += '<font size=2><center><b>'.$mrow['name'].'<br /><font size=1>( Level '.$mrow['level'].' )</b></font><br/>
						<div id="ProfileBox" style="height:100px;width:100px;" ><img src="'.$mrow['image'].'" width="100%"/></div></center></font><br />
						<font size=2><b>Health:</b> '.$mhealth.' / '.(100 + ($mrow['level']) * 10).'<br /><b>Attack Power:</b> '.$edamage.'<br /><b>Defense Power: </b>'.$edefense.'</font></center><br />';
		}
	}
	else {
		$width = 150;
		$about = '<a href="'.getURL().'type=3&arena=1&explain=1">What is this?</a>';
		$combat = '<a href="'.getURL().'type=3&arena=1&setup=1&fight=1">Challenge a Defender!</a>';
		$records = '<a href="'.getURL().'type=3&arena=1&records=1">View Personal Record</a>';
		$rightColumn += '<b>Battle Arena:</b><br /><font size=2>'.$about.'<br/>'.$combat.'</br>'.$records.'<br /></font>';
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
			
			$middleColumn += '<center>'.$row1['brand'].'</center><font size=2><b>Secretary:</b><br />'.indent($about.'<br />'.$profits.'<br />'.$destroy).
							'<b>Corporate Fund:</b><br />'.indent($funds).
							'<b>Production:</b><br />'.indent($production.'<br />'.$stock.'<br />'.$trades).
							'<b>Product Price:</b><br />'.indent($price).
							'<b>Worker Wages:</b><br />'.indent($wages).
							'</font><center><a href="#" onclick="var e = myForm.elements['."'etype'".'].value;var f = myForm.elements['."'exchange'".'].value;var g = myForm.elements['."'quantic'".'].value;var h = myForm.elements['."'quanticeq'".'].value;var i = myForm.elements['."'price'".'].value;var j = myForm.elements['."'pay'".'].value;displayNewScreen('."'myFrame','main.php?type=3&market=1&manage=1&etype='+e+'&exchange='+f+'&quantic='+g+'&quanticeq='+h+'&price='+i+'&pay='+j".');">Execute Changes</a></center>';
		}
		else if (isset($_GET['explain'])){
			$image = 15;
			$middleColumn += "<font size=1>Why, Hello, and welcome to the Company Management screen! It's all real easy to use, so I'm sure my explanation will be more than adequate. First, the Corporate Funds. That's what the company owns. When you sell something, or when someone gets paid, the money leaves and enters in there. Make sure it's plenty full, because people who work for you will need to be paid! If that thing hits zero, you won't be able to hire anyone! Next, Wages, that's how much someone is paid for making the desired item. The Price is what the selling rate is of your product, and finally, in the Production area, we can purchase new licenses and see how many of the desired product we have in stock. Be careful though, switching licenses destroys the previous one, along with all produced products!</font>";
			$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&market=1&manage=1'".');">Return to Management Screen</a>';
			$width = 200;
		}
		else if (isset($_GET['profits'])){
			$image = 15;
			$width = '300';
			$result1 = mysql_query("SELECT * FROM companies WHERE ownerid='$myid'") or die(mysql_error());
			$row1 = mysql_fetch_array( $result1 );
			$middleColumn += '<center>Profits: '.$row1['profit'].'</center>'.
			indent('<b>Note:<font size=2> The above number shows the sum of all of your sales, minus the worker salaries and licensing fees. Up front fee of 1000 gold and startup funds of 1000 gold are not included. If the number is negative, it is strongly advised that you change your marketing strategy.</font></b>');
			$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&market=1&manage=1'".');">Return to Management Screen</a>';
		}
		else if (isset($_GET['work'])){
			$image = 11;
			$width=350;
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
			
			$middleColumn += '<font size=2>Come to get a job, eh? Note that you can only work <b>6</b> jobs an hour. Also note that you will only be able to fabricate products that your level allows you to use.</font><br /><br />'
						.'Product:<form name="findprod" method="post">
						<select name="select" onChange="displayNewScreen('."'myFrame','main.php?type=3&market=1&work=1&prod='+this.value".');">'.$options.'</select></form>
						<br /><br />
						<center>'.$work.'<br />Best Pay: '.$pay.'</form><br /><br /><font size=2>Offered to you by</font><br />'.$company.'</center>';
		}
		else {
			$image = 10;
			$width='350';

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
						
			$middleColumn += '<font size=2>Welcome, one and all, to the great market! Select the product you want to buy, and see if we have it stocked in one of the many player-owned markets! We give you lowest price guaranteed, my friend.</font><br /><br />'
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
			$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1'".');">Return to the Entity Council</a></center>';	
			$middleColumn += "<font size=2>Why hello there! If you're looking to join a faction, you must be at least level <b>20</b>, and willing to pay the entry fee of <b>1000 gold</b>. Try talking to some of the players about factions. They know more than I do.</font>";
		}
		else if (isset($_GET['warsnow'])){
			$image = 29;
			$width = 180;
			$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1'".');">Return to the Entity Council</a></center>';	
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
			$middleColumn += '<center><b>Current Wars</b></center>
							  <b>First War:</b><br />'.indent('<font size=2>Attacker: '.$attacker1.'<br/>Defender: '.$defender1.'</br>Casualties: '.$casualties1.'</font>')
							  .'<br />
							  <b>Second War:</b><br />'.indent('<font size=2>Attacker: '.$attacker2.'<br/>Defender: '.$defender2.'</br>Casualties: '.$casualties2.'</font>');
		}
		else if (isset($_GET['awars'])){
			$image = 29;
			$width = 200;
			$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1'".');">Return to the Entity Council</a></center>';	
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
			$middleColumn += '<center><b>Previous Wars</b></center>
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
				$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."'".');">Return to the Outskirts</a>';	
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
					$middleColumn += '<center><b>'.$row1['name'].'</b></center><br />'
					.'<b>Palace Options:</b>'.indent('<font size=2>'.$legacy.'<br />'.$attack.$teamquests.'<br />'.$contribute.'<br />'.$armory.'<br />'.$barracks.'<br />'.$workshop.'<br />'.$quit.'</font>');
				}
				else if ($cfaction == 0){
					$join = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&join=1'".');">Join Faction</a>';	
					$middleColumn += '<center><b>'.$row1['name'].'</b></center><br />'
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
				$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
				$middleColumn += '<center><font size=2><i>Launch an Attack against the '.$enemy.'!</i></font></center>';
			}
			else if (isset($_GET['quests'])){
				if (isset($_GET['fquest'])){
					$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1'".');">Return to Mission Selection</a>';							
					$quest = (int) $_GET['fquest'];
					$result = mysql_query("SELECT * FROM quests WHERE id='$quest'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
					$image = $row['image'];
					$begin = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1&mission=".$quest."'".');">Begin Mission!</a>';
					$middleColumn += '<font size=2><center>'.$row['title'].'</center><br /><br />'.$row['descript'].'<br /><br /><b>Party Size:</b> '.$row['partysize'].'<br /><b>Duration:</b> '.$row['length'].' battles</font><br /><br /><center>'.$begin.'</center>';
				}
				else if ($chealth <= 0 or (isset($_GET['mission']) and $cfacques > 10)){
					$image = 4;
					$middleColumn += '<font size=2>You are too weak to go on, or you have exceeded your limit of quests per hour. Mission failed.</font>';
				}
				else if (isset($_GET['distance']) and $_GET['distance'] == 0){
					$mission = (int) $_GET['mission'];
					$result = mysql_query("SELECT * FROM quests WHERE id='$mission'") or die(mysql_error());
					$row = mysql_fetch_array( $result );
					$prize = $row['prize'];
					$middleColumn += '<center><b>We have victory!</b><br /><br /><table width=90%><tr><td><font size=2>Commendations Earned:</font></td><td>'.$prize.'</td></tr>
					<tr><td><font size=2>Experience Earned:</font></td><td>10</td></tr></table></center>';
					$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to the Palace</a>';
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
					$middleColumn += '<center><table>';
					while ($number < 4){
						$checkit = '';
						if ($number == $checked){
							$checkit = 'CHECKED';
						}
						$middleColumn += $next.'<tr><td><input type="radio" name="attackchoice" onClick="displayNewScreen('."'myDiv','main.php?type=3&factions=y&party=".$faction.'&quests=y&mission='.$quest.'&distance='.$distance.'&monster='.$monster.'&mhealth='.$mhealth.$location."&choice=".$number."'".');"  '.$checkit.' /></td><td>'.$move[$number].'</td></tr>';
						$number++;
					}	
					$middleColumn += $next.'</table></center>';
			 
					$result = mysql_query("SELECT * FROM monsters WHERE id='$monster'") or die(mysql_error());
					$mrow = mysql_fetch_array($result);
					
					$middleColumn += '<font size=2><center><b>'.$mrow['name'].'<br /><font size=1>( Class '.$mrow['mclass'].' ; Level '.$mrow['level'].' )</b></font><br/>
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
					$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to the Palace</a>';	
					$rescue = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1&fquest=1'".');">Rescue Mission</a>';
					$seize = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1&fquest=2'".');">Seize a Temple</a>';
					$boss = '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&quests=1&fquest=3'".');">Kill a Warbeast</a>';
					$middleColumn += '<font size=2>Would you believe it if I told you that the local blasphemers were once more causing us trouble? This must stop!<br /><br /><b>(Note: quests reward Faction Commendations. You can only attempt up to 10 missions per hour.)</b></font>'.'<br /><br />'.indent('<font size=2>'.$rescue.'<br />'.$seize.'<br />'.$boss.'</font>').'<br /><center>'.(10 - $cfacques).' Attempts Remaining</center>';
				}
			}
			else if (isset($_GET['armory'])){
				$image = 42;
				$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
				$middleColumn += '<center><b>'.$row1['name'].' Armory</b></center><br /><table width=90%><tr><td>
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
					$middleColumn += $controlpan.'<tr><td width=55%>
					<input type="radio" name="factiongear" onClick="displayNewScreen('."'myDiv','main.php?type=3&factions=y&party=".$faction."&armory=y&factiongear='+".$row['id'].');" '.$check.' />'.$row['name'].'<br /></td>
					<td width=35%>'.$details.'</td>
					<td width=10%>'.$row['license'].'</td></tr>';
				}
				$middleColumn += $controlpan.'</table></center></td></tr></table></td></tr></table></center>';
				if ($cfaction != $faction){
					$middleColumn += '<center><b>'.$row1['name'].' Armory</b><center><br /><center>Dude, GTFO.</center>';
				}
			}
			else if (isset($_GET['barracks'])){
				$image = 20;
				$width = 160;
				$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
				if ($cfaction == $faction){
					$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction='$faction'") or die(mysql_error());
					$rows = mysql_fetch_array($result);
					$number = $rows['COUNT(*)'];
					$middleColumn += '<center><b>'.$row1['name'].'</b><center><br />'
							.'<b>Faction Barracks:</b>'.indent('Soldiers: '.$row1['army'].' / '.(400 + $number*10).'<br />
							<input method=text name="recruit" size="8" /><button type="button" onclick="var e = myForm.elements['."'recruit'".'].value;displayNewScreen('."'myDiv','main.php?type=3&factions=1&party=".$faction."&barracks=y&recruit='+e".');">Recruit</button>
							<br /><br /><font size=1>Note: 1 soldiers costs 10 faction commendations and 200 gold out of the Faction Funds</font>');
				}
			}
			else if (isset($_GET['workshop'])){
				$image = 20;
				$width = 160;
				$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
				if ($cfaction == $faction){
					$middleColumn += '<center><b>'.$row1['name'].'</b><center><br /><b>Faction Workshop:</b><br />Influence: '.$row1['influence'].' / 1000<br />
							<input method=text name="recover" size="8" /><button type="button" onclick="var e = myForm.elements['."'recover'".'].value;displayNewScreen('."'myDiv','main.php?type=3&factions=1&party=".$faction."&workshop=y&recover='+e".');">Repair</button>
							<br /><br /><font size=1>Note: each point of reparation costs 15 faction commendations and 100 gold out of the Faction Funds</font>';
				}
			}
			else if (isset($_GET['contribute'])){
				$image = 33 + $faction;
				$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
				$middleColumn += '<center><b>'.$row1['name'].'</b><center><br /><b>Faction Funds:</b> '.$row1['funds'].'<br /><br />
							<input method=text name="donate" size="8" /><button type="button" onclick="var e = myForm.elements['."'donate'".'].value;displayNewScreen('."'myDiv','main.php?type=3&factions=1&party=".$faction."&contribute=y&donate='+e".');">Donate</button>';
			}
			else if (isset($_GET['legacy'])){
				$image = 33 + $faction;
				$width = 200;
				$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
				$middleColumn += indent('<font size=2>'.$row1['legacy'].'</font>');
			}
			else if (isset($_GET['leave'])){
				$image = 41;
				$width = 120;
				$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
				$middleColumn += "<font size=2>He who abandons his friends and his faith, abandons himself. Sign your character's name below to confirm your decision.<br /><br /><b>(Note: leaving a faction will result in the loss of all faction items)</b></font>".'<br />
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
				
				$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1&party=".$faction."&palace=1'".');">Return to Palace</a>';
				$middleColumn += "<font size=2>You make a wise decision joining us, for the other factions are far below your standards and expertise! Sign your character's name below to confirm your decision.<br /><br /><b>(Note: you must be level 20+ and pay 1000 gold to join a faction.)</b></font>".'<br /><br />
				<center>'.$join.'</center>';
			}
			else {
				$image = 15 + $faction;
				$width = 140;
				$middleColumn += '<a href="#" onclick="displayNewScreen('."'myFrame','main.php?type=3&factions=1".');">Return to the Entity Council</a>';	
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
				$middleColumn += '<center><b>'.$row1['name'].'</b></center><br />'
								.'<b>War Record:</b>'.indent('<font size=2><center>Wins: '.$number2.'</center></td><td><center>Losses: '.$number3.'</center></font>').'<br />'
								.'<b>Statistics:</b>'.indent('<font size=2>Capital: '.(intval($row1['funds'] / 100) * 100)
								.'+<br />Influence: '.$row1['influence'].'<br/>'.'Total Heros: '.$number.'</font>')
								.'<br /><center>'.$enter.'</center>';
			}
		}
		else{
			$image = 26;
			$width = 200;
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
			$middleColumn += 	"<font size=2>Hey there, welcome to the Entity Council! We try to use it to maintain peace between the civilizations, but you know how things are... Old enemies remain enemies, right?</font><br />".
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
		$middleColumn += '<div align="left"><b>Adventure:</b><br />'.indent('<font size=2>'.$quest.'<br/>'.$hunt.'<br/>'.$arena.'</font>').'
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
		$middleColumn += '<font size=2>'.$next.'</font>';
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
}