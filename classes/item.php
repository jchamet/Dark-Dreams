<?php

class item{
	
	var $id;
	var $type;
	var $makeable;
	var $name;
	var $itemprice;
	var $licprice;
	public $stats;
	
	function __construct($id){
		$this->id = $id;
		$result = mysql_query("SELECT * FROM items WHERE id='$id' ") or die(mysql_error());
		$loadrow = mysql_fetch_array($result);
		$this->type = $loadrow['mtype'];
		$this->makeable = $loadrow['makeable'];
		$this->name = $loadrow['name'];
		$this->itemprice = $loadrow['item_price'];
		$this->licprice = $loadrow['lic_price'];
		$this->stats = (object) array('END' => $loadrow['END'],'VIT' => $loadrow['VIT'],'DEF' => $loadrow['DEF'],'STR' => $loadrow['STR'],'FER' => $loadrow['FER'],'DOD' => $loadrow['DOD'],'PRE' => $loadrow['PRE'],'ACC' => $loadrow['ACC']); 
    }
    
    function checkMake(){
    	if ($this->makeable == 1){
    		return true;
    	}
    	else{
    		return false;
    	}
    }
    
    function listStats(){
    	return 'END: '.$this->stats->END.' VIT: '.$this->stats->VIT.' DEF: '.$this->stats->DEF.' STR: '.$this->stats->STR.' <br>FER: '.$this->stats->FER.' DOD: '.$this->stats->DOD.' PRE: '.$this->stats->PRE.' ACC: '.$this->stats->ACC;
    }
	
}