<?php
class faction{
    
	var $id;
	var $armylimit;
	var $enemy;
	public $aspects;
    
    function __construct($id0){
		$this->id = $id0;
		
		$result = mysql_query("SELECT * FROM factions WHERE id='$this->id' ") or die(mysql_error());
		$loadrow = mysql_fetch_array($result);		
		
		$this->aspects = (object) array('name' => $loadrow['name'],'imageid' => $loadrow['imageid'],'funds' => $loadrow['funds'],'influence' => $loadrow['influence'],'army' => $loadrow['army'],'legacy' => $loadrow['legacy'],'typicalwarrior' => $loadrow['typicalwarrior']); 
    	$this->armylimit = $this->getMembers*10+300;
    	
    	$result1 = mysql_query("SELECT * FROM wars WHERE (attacker='$this->id' or defender='$this->id') and active=1 ") or die(mysql_error());
		$loadrow1 = mysql_fetch_array($result1);
		if($loadrow1['attacker'] == $this->id){
			$this->enemy = $loadrow1['defender'];
		}
		else{
			$this->enemy = $loadrow1['attacker'];
		}
    }
	
	function getName(){
		return $this->aspects->name;
	}
	function getImage(){
		return $this->aspects->imageid;
	}
	function getLegacy(){
		return $this->aspects->legacy;
	}
	function getWarrior(){
		return $this->aspects->typicalwarrior;
	}
	
	function getMembers(){
		$result = mysql_query("SELECT COUNT(*) FROM chars WHERE faction='$this->id'") or die(mysql_error());
		$rows = mysql_fetch_array($result);
		return $rows['COUNT(*)'];
	}
	
	function checkSpend($n){
		if($this->aspects->funds - $n >= 0){
			return true;
		}
		else{
			return false;
		}
	}
	function checkInfluence($n){
		if($this->aspects->influence - $n <= 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getFunds(){
		return $this->aspects->funds;
	}
	function getFundsAprox(){
		$return = round(($this->aspects->funds)/1000)*1000;
		return $return;
	}
	function chgFunds($n){
		$this->aspects->funds += $n;
	}
	function getInfluence(){
		return $this->aspects->influence;
	}
	function chgInfluence($n){
		$this->aspects->influence += $n;
		if ($this->getInfluence > 1000){
			$this->getInfluence = 1000;
		}
		if ($this->getInfluence < 0){
			$this->getInfluence = 0;
		}
	}
	
	function getArmy(){
		return $this->aspects->army;
	}
	function getArmyAprox(){
		$return = round(($this->aspects->army)/10)*10;
		return $return;
	}
	function chgArmy($n){
		$this->aspects->army += $n;
		if ($this->getArmy() > $this->armylimit){
			$this->aspects->army = $this->armylimit;
		}
		if ($this->getArmy() < 0){
			$this->aspects->army = 0;
		}
	}
	
	
	function update(){
		$n_funds = $this->aspects->funds;
		$n_influence = $this->aspects->influence;
		$n_army = $this->aspects->army;
		mysql_query("UPDATE factions SET funds='$n_funds',influence='$n_influence',army='$n_army' WHERE id='$this->id'") or die(mysql_error());
	}
	
}	
?>