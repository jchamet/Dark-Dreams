<?php
class char{
    
	var $id;
	var $trueid;
	var $type;
	var $name;
	var $image;
	var $location;
	var $locationTemp;
	var $alignment;
	var $Soul;
	var $arena_wins;
	var $arena_losses;
	var $job1;
	var $job2;
	var $faction;
	var $factionrep;
	var $factionquestnum;
	var $questnum;
	var $maxExp;
	var $locid;
    public $stats;
	public $inventory;
    
    function __construct($type0,$id0){
		$this->type = $type0;
		$this->id = $id0;
		$this->locationTemp = null;
		
		if ($this->type == 1){
			$result = mysql_query("SELECT * FROM chars WHERE uid='$this->id'") or die(mysql_error());
		}
		else if ($this->type == 2){
			$result = mysql_query("SELECT * FROM monsters WHERE id='$this->id'") or die(mysql_error());
		}
		else if ($this->type == 3){
			$result = mysql_query("SELECT * FROM chars WHERE id='$this->id'") or die(mysql_error());
		}
		$loadrow = mysql_fetch_array($result);
		$this->id = $loadrow['id'];
		
		if ($this->type == 1 or $this->type == 3){
			$this->name = $loadrow['username'];
			$this->location = $loadrow['loc'];
			$this->aligment = $loadrow['alignment'];
			$this->Soul = $loadrow['Soul'];
			$this->inventory = (object)array(	'obj1' => new item($loadrow['obj1']),
												'obj2' => new item($loadrow['obj2']),
												'obj3' => new item($loadrow['obj3']),
												'obj4' => new item($loadrow['obj4']));
			$this->arena_wins = $loadrow['arena_wins'];
			$this->arena_losses = $loadrow['arena_losses'];	
			$this->job1 = $loadrow['job1'];
			$this->job2 = $loadrow['job2'];
			$this->faction = $loadrow['faction'];	
			$this->factionrep = $loadrow['factionrep'];	
			$this->factionquestnum = $loadrow['factionquestnum'];				
			$this->questnum = $loadrow['questnum'];					
		}
		else {
			$this->name = $loadrow['name'];
			$this->img = $loadrow['image'];
			$this->locid = $loadrow['locid'];
			$this->Soul = $loadrow['Soul'];
			$this->inventory = (object)array(	'obj1' => new item($loadrow['obj1']),
												'obj2' => new item($loadrow['obj2']),
												'obj3' => new item($loadrow['obj3']),
												'obj4' => new item($loadrow['obj4']));
		}
		
		$this->trueid = $loadrow['id'];
		
		$this->stats = (object)array('LVL' => $loadrow['LVL'],'EXP' => $loadrow['EXP'],'GOLD' => $loadrow['GOLD'],'HP' => $loadrow['HP'],'END' => $loadrow['END'],'VIT' => $loadrow['VIT'],'DEF' => $loadrow['DEF'],'STR' => $loadrow['STR'],'FER' => $loadrow['FER'],'DOD' => $loadrow['DOD'],'PRE' => $loadrow['PRE'],'ACC' => $loadrow['ACC']); 
    	$this->maxExp = $this->getSTAT('LVL')*$this->getSTAT('LVL')*10;
    }
	
	function getSTAT($variable){
		return $this->stats->$variable;
	}
	function setSTAT($variable,$n){
		$this->stats->$variable = $n;
	}
	function chgSTAT($variable,$n){
		$this->stats->$variable += $n;
	}
	
	function chgFacRep($n){
		$this->factionrep += $n;
		if($this->factionrep > 2000){
			$this->factionrep = 2000;
		}
	}
	
	function searchItem($n){
		$item = null;
		foreach($this->inventory as $struct) {
		    if ($n == $struct->id) {
		    	$item = $struct;
		        return true;
		        break;
		    }
		}
		if ($item == null){
			return false;
		}
	}
	function checkInventory(){
		if ($this->searchItem(0)){
			return true;
		}
		else {
			return false;
		}
	}
	function checkExp(){
		if ($this->getSTAT('EXP') >= $this->maxExp and $this->getSTAT('LVL') < 20){
			return true;
		}
		else{
			return false;
		}
	}
	function checkDeath(){
		if ($this->getSTAT('HP') <= 0){
			return true;
		}
		else {
			return false;
		}
	}
	function checkSpend($n){
		if ($this->getSTAT('GOLD') >= $n){
			return true;
		}
		else {
			return false;
		}
	}
	function checkSpendR($n){
	if ($this->factionrep >= $n){
			return true;
		}
		else {
			return false;
		}
	}
	function checkCompany(){
		$result = mysql_query("SELECT * FROM companies WHERE ownerid='$this->id' ") or die(mysql_error());
		if (mysql_num_rows($result)){
			return true;
		}
		else{
			return false;
		}
	}
	function checkFaction(){
		if ($this->faction == 0 and $this->getSTAT('LVL') >= 5){
			return true;
		}
		else{
			return false;
		}
	}
	function checkQuest(){
		if($this->questnum < 6){
			return true;
		}
		else{
			return false;
		}
	}
	function checkWork($n){
		if(($this->job1 == 0 or $this->job2 == 0) and $this->job1 != $n){
			return true;
		}
		else{
			return false;
		}
	}
	function addJob($n){
		if($this->job1 == 0){
			$this->job1 = $n;
		}
		elseif($this->job2 == 0){
			$this->job2 = $n;
		}
	}
	function checkFacNum(){
		if($this->factionquestnum < 10){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getCompany(){
		$result = mysql_query("SELECT * FROM companies WHERE ownerid='$this->id' ") or die(mysql_error());
		$loadrow = mysql_fetch_array($result);
		return $loadrow['id'];
	}
	
	function loadItem($n){
		$result = mysql_query("SELECT * FROM items WHERE id='$n'") or die(mysql_error());
		$item = mysql_fetch_array($result);
		$this->chgSTAT('END',$item['END']);
		$this->chgSTAT('VIT',$item['VIT']);
		$this->chgSTAT('DEF',$item['DEF']);
		$this->chgSTAT('STR',$item['STR']);
		$this->chgSTAT('FER',$item['FER']);
		$this->chgSTAT('DOD',$item['DOD']);
		$this->chgSTAT('PRE',$item['PRE']);
		$this->chgSTAT('ACC',$item['ACC']);
	}
	function loadGear(){
		$this->loadItem($this->Soul);
		$this->loadItem($this->inventory->obj1->id);
		$this->loadItem($this->inventory->obj2->id);
		$this->loadItem($this->inventory->obj3->id);
		$this->loadItem($this->inventory->obj4->id);
	}
	
	function findItem($n){
		$done = false;
		reset($this->inventory);
		while($obj = current($this->inventory) and !$done) {
		    if ($n == $obj->id) {
		        return key($this->inventory);
		        $done = true;
		    }
		    next($this->inventory);
		}
	}
	function chgItem($n,$m){
		$this->inventory->$n = new item($m);
	}
	function addItem($n){
		$this->chgItem($this->findItem(0),$n);
	}
	function removeItem($n){
		$this->chgItem($this->findItem($n),0);
	}
	function getSoul(){
		$soul = new item($this->Soul);
		return $soul->name;
	}
	
	function recover(){
		$this->setSTAT('HP',($this->getSTAT('END')));
	}
	
	function levelUp(){
		$this->chgSTAT('LVL',1);
		$this->chgSTAT('EXP',-$this->maxExp);
		$this->chgSTAT('END',10); 
	}
	
	function increaseWins(){
		$this->arena_wins += 1;
	}
	function increaseLosses(){
		$this->arena_losses += 1;
	}
		
function update(){
		$n_experience = $this->getSTAT('EXP');
		$n_gold = $this->getSTAT('GOLD');
		$n_health = $this->getSTAT('HP');
		$n_wins = $this->arena_wins;
		$n_losses = $this->arena_losses;
		$obj1 = $this->inventory->obj1->id;
		$obj2 = $this->inventory->obj2->id;
		$obj3 = $this->inventory->obj3->id;
		$obj4 = $this->inventory->obj4->id;
		mysql_query("UPDATE chars SET 	username='$this->name',
										loc='$this->location',
										alignment='$this->alignment',
										Soul='$this->Soul',
										obj1='$obj1',
										obj2='$obj2',
										obj3='$obj3',
										obj4='$obj4',
										EXP='$n_experience',
										GOLD='$n_gold',
										HP='$n_health',
										arena_wins='$n_wins',
										arena_losses='$n_losses',
										job1='$this->job1',
										job2='$this->job2',
										faction='$this->faction',
										factionrep='$this->factionrep',
										factionquestnum='$this->factionquestnum',
										questnum='$this->questnum' WHERE id='$this->id'") or die(mysql_error());
	}
	function updateArena(){
		$n_wins = $this->arena_wins;
		$n_losses = $this->arena_losses;
		mysql_query("UPDATE chars SET arena_wins='$n_wins',arena_losses='$n_losses' WHERE id='$this->id'") or die(mysql_error());
	}
	function updateStats(){
		$n_level = $this->getSTAT('LVL');
		$n_endurance = $this->getSTAT('END');
		$n_vitality = $this->getSTAT('VIT');
		$n_defense = $this->getSTAT('DEF');
		$n_strength = $this->getSTAT('STR');
		$n_ferocity = $this->getSTAT('FER');
		$n_dodge = $this->getSTAT('DOD');
		$n_precision = $this->getSTAT('PRE');
		$n_accuracy = $this->getSTAT('ACC');
		mysql_query("UPDATE chars SET LVL='$n_level',END='$n_endurance',VIT='$n_vitality',DEF='$n_defense',STR='$n_strength',FER='$n_ferocity',DOD='$n_dodge',PRE='$n_precision',ACC='$n_accuracy' WHERE id='$this->id'") or die(mysql_error());
	}
	
	function addUnlock($n){
		mysql_query("INSERT INTO u_unlocks (uid,locid) VALUES('$this->id','$n') ") or die(mysql_error());  
	}
	
	function getStatSheet(){
		return 'Endurace: '.$this->getSTAT('END').'<br>Vitality: '.$this->getSTAT('VIT').'<br>Defense: '.$this->getSTAT('DEF').'<br>Strength: '.$this->getSTAT('STR').'<br>Ferocity: '.$this->getSTAT('FER').'<br>Dodge: '.$this->getSTAT('DOD').'%<br>Precision: '.$this->getSTAT('PRE').'%<br>Accuracy: '.$this->getSTAT('ACC').'%';
	}

}

?> 