<?php

function Battle($player1,$player2){

	$result = '';
	
	// Hits
	$hit = false;	$e_hit = false;
	if ((rand(0,100) + $player2->getSTAT('DOD') - $player1->getSTAT('PRE')) <= 0){ $hit = true; }
	if ((rand(0,100) + $player1->getSTAT('DOD') - $player2->getSTAT('PRE')) <= 0){ $e_hit = true; }
	// Crits
	$crit = false;	$e_crit = false;
	if ((rand(0,100) - $player1->getSTAT('ACC')) <= 0){ $crit = true; }
	if ((rand(0,100) - $player2->getSTAT('ACC')) <= 0){ $e_crit = true; }

	// Damage Taken
	if ($hit){
		$damage_built = round(rand(50,100)*($player1->getSTAT('STR'))/100 + rand(0,100)*($player1->getSTAT('FER'))/100);
		if ($crit){
			$damage_built = round(2.1*$damage_built);
			$result = $result.'Critical hit!<br>';
		}
		$damage_dealt = round($damage_built - rand(30,60)*($player2->getSTAT('DEF'))/100);
		if ($damage_dealt < 0){
			$damage_dealt = 0;
		}
		$result = $result.$player1->name.' dealt '.$damage_dealt.' damage!<br>';
		$player2->chgSTAT('HP',-$damage_dealt);
	}
	else {
		$result = $result.$player1->name.' misses with an attack!<br>';
	}
	if ($e_hit){
		$e_damage_built = round(rand(50,100)*($player2->getSTAT('STR'))/100 + rand(0,100)*($player2->getSTAT('FER'))/100);
		if ($crit){
			$e_damage_built = round(2.1*$e_damage_built);
			$result = $result.'Critical hit!<br>';
		}
		$e_damage_dealt = round($e_damage_built - rand(30,60)*($player1->getSTAT('DEF'))/100);
		if ($e_damage_dealt < 0){
			$e_damage_dealt = 0;
		}
		$result = $result.$player2->name.' dealt '.$e_damage_dealt.' damage!<br>';
		$player1->chgSTAT('HP',-$e_damage_dealt);
	}
	else {
		$result = $result.'<br>'.$player2->name.' misses with an attack!<br>';
	}

	//End Turn
	if (!$player1->checkDeath() and !$player2->checkDeath()) {
		$player1->chgSTAT('HP',$player1->getSTAT('VIT'));
		if($player1->getSTAT('HP') > $player1->getSTAT('END')){
			$player1->setSTAT('HP',$player1->getSTAT('END'));
		}
		$player2->chgSTAT('HP',$player2->getSTAT('VIT'));
		if($player2->getSTAT('HP') > $player2->getSTAT('END')){
			$player2->setSTAT('HP',$player2->getSTAT('END'));
		}
	}
	
	return $result;
}

?>