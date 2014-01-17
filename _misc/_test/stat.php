<?php
class stat{
    
    var $current = 0;
    var $normal = 0;
    var $zero = 0;
    
    function __construct($n){
        $this->normal = $n;
		$this->current = $n;
    }
    
    function increase($n){
        $i = 0;
        while(($this->current < $this->normal) && ($n>0)){
            $this->current++;
            $n--;
            $i++;
        }
        return $i;
    }
    
    function decrease($n){
        $i = 0;
        while(($this->current > 0) && ($n>0)){
            $this->current--;
            $n--;
            $i++;
        }
        if($this->current <= $this->zero){
                return false;
        }
        return $i;
    }
	
	function overdrive($n){
        $this->current = $this->current + $n;
    }
    
	function set($n){
        $this->current = $n;
    }
	
    function get(){
        return $this->current;
    }
    
    function reset(){
        $this->current = $this->normal;
    }

}
?> 