<?php
class char{
    
    public $stats;
	public $inventory;
    
    function __construct(){
		$this->stats = (object) array('LVL' => '1', 'EXP' => '0'); 
    }
	
	function getSTAT($variable){
		return $this->stats->$variable;
	}
}

echo'Testing<br>';
$user = new char();
echo $user->getSTAT('LVL');

?> 