<?php
class company{
    
	var $id;
	public $aspects;
    
    function __construct($id0){
		$this->id = $id0;
		
		$result = mysql_query("SELECT * FROM companies WHERE id='$this->id' ") or die(mysql_error());
		$loadrow = mysql_fetch_array($result);		
		
		$this->aspects = (object) array('ownerid' => $loadrow['ownerid'],'brand' => $loadrow['brand'],'hired' => $loadrow['hired'],'funds' => $loadrow['funds'],'profit' => $loadrow['profit'],'product' => $loadrow['product'],'mprice' => $loadrow['mprice'],'quantity' => $loadrow['quantity'],'pay' => $loadrow['pay']); 
    }
	
	function getOwnerID(){
		return $this->aspects->ownerid;
	}
	function getOwnerName(){
		$owner = $this->getOwnerID();
		$result = mysql_query("SELECT * FROM chars WHERE id='$owner' ") or die(mysql_error());
		if (mysql_num_rows($result)){
			$row = mysql_fetch_array($result);	
			return $row['username'];
		}
		else{
			return 'Citizens';
		}
	}

	function getID(){
		return $this->id;
	}
	function getName(){
		return $this->aspects->brand;
	}
	function setName($n){
		$this->aspects->brand = $n;
	}
	
	function checkHire(){
		if ($this->aspects->hired <= 3){
			return true;
		}
		else{
			return false;
		}
	}
	function checkSpend($n){
		if($this->aspects->funds - $n >= 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function addFunds($n){
		$this->aspects->funds += $n;
	}
	function chgProfit($n){
		$this->aspects->profit += $n;
	}
	function chgFunds($n){
		$this->addFunds($n);
		$this->chgProfit($n);
	}
	
	function getProduct(){
		return $this->aspects->product;
	}
	function getProductName(){
		$product = $this->getProduct();
		$result = mysql_query("SELECT * FROM items WHERE id='$product' ") or die(mysql_error());
		$row = mysql_fetch_array($result);	
		return $row['name']; 
	}
	function setProduct($n){
		$this->aspects->product = $n;
	}

	function getPrice(){
		return $this->aspects->mprice;
	}
	function setPrice($n){
		$this->aspects->mprice = $n;
	}
	
	function getPay(){
		return $this->aspects->pay;
	}
	function setPay($n){
		$this->aspects->pay = $n;
	}

	function getQuantity(){
		return $this->aspects->quantity;
	}
	function chgQuantity($n){
		$this->aspects->quantity += $n;
	}
	
	function update(){
		$n_brand = $this->aspects->brand;
		$n_hired = $this->aspects->hired;
		$n_funds = $this->aspects->funds;
		$n_profit = $this->aspects->profit;
		$n_product = $this->aspects->product;
		$n_mprice = $this->aspects->mprice;
		$n_quantity = $this->aspects->quantity;
		$n_pay = $this->aspects->pay;
		mysql_query("UPDATE companies SET brand='$n_brand',hired='$n_hired',funds='$n_funds',profit='$n_profit',product='$n_product',mprice='$n_mprice',quantity='$n_quantity',pay='$n_pay' WHERE id='$this->id'") or die(mysql_error());
	}
	
}	
?>