<?php
// Identify User
$unique_identifier = htmlspecialchars($_GET['id']);
$username = htmlspecialchars($_GET['user']);

echo'<font color="white">';
echo 'Welcome, '.$username.'!<br>';
include('stat.php');

// ***Loaders

	// Load Character
	$endurance = new stat(100);
	$vitality = new stat(5);
	$defense = new stat(10);

	$strength = new stat(20);
	$ferocity = new stat(10);

	$dodge = new stat(10);
	$precision = new stat(80);
	$accuracy = new stat(5);

	echo '<br>';

	// Load Enemy
	$e_endurance = new stat(100);
	$e_vitality = new stat(5);
	$e_defense = new stat(10);

	$e_strength = new stat(20);
	$e_ferocity = new stat(10);

	$e_dodge = new stat(10);
	$e_precision = new stat(80);
	$e_accuracy = new stat(5);

// ***Battle Sequence
while ($endurance->get() > 0 and $e_endurance->get() > 0){

	// Hits
	$hit = false;	$e_hit = false;
	if ((rand(0,100) + $e_dodge->get() - $precision->get()) <= 0){ $hit = true; }
	if ((rand(0,100) + $dodge->get() - $e_precision->get()) <= 0){ $e_hit = true; }
	// Crits
	$crit = false;	$e_crit = false;
	if ((rand(0,100) - $accuracy->get()) <= 0){ $crit = true; }
	if ((rand(0,100) - $e_accuracy->get()) <= 0){ $e_crit = true; }

	// Damage Taken
	if ($hit){
		$damage_dealt = round(rand(50,100)*($strength->get())/100 + rand(0,100)*($ferocity->get())/100 - rand(30,60)*($e_defense->get())/100);
		if ($crit){
			$damage_dealt = round(1.5*$damage_dealt);
			echo'<br>Critical hit!';
		}
		echo '<br>Player dealt '.$damage_dealt.' damage!<br>';
		$e_endurance->decrease($damage_dealt);
	}
	else {
		echo '<br>Player misses with an attack!<br>';
	}
	if ($e_hit){
		$e_damage_dealt = round(rand(50,100)*($e_strength->get())/100 + rand(0,100)*($e_ferocity->get())/100 - rand(30,60)*($defense->get())/100);
		if ($e_crit){
			$e_damage_dealt = round(1.5*$e_damage_dealt);
			echo'<br>Critical hit!';
		}
		echo '<br>Enemy dealt '.$e_damage_dealt.' damage!<br>';
		$endurance->decrease($e_damage_dealt);
	}
	else {
		echo '<br>Enemy misses with an attack!<br>';
	}

	//End Turn
	if ($endurance->get() <= 0){ echo '<br>Player defeated!'; }
	else if ($e_endurance->get() <= 0){ echo '<br>Enemy defeated!'; }
	else {
		$endurance->increase($vitality->get());
		$e_endurance->increase($e_vitality->get());
	}
}
echo'</font>';

?>