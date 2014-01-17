<?php
// This file and all of its containing script is property of James Hamet.

echo'
<body>
<style type="text/css">
body {
	color: #4E3D4E;
	background-color: #EDEDED;
}
#container {
	position: relative;
	top: 175px;
	width: 1100px;
	min-height: 224px;
	background-color: #FFFFFF;
	padding: 10px;
	margin-left: auto;
	margin-right: auto;
	-moz-border-radius: 15px;
	-webkit-border-radius: 15px;
}
.zone {
	min-height: 200px;
	background-color: #89abcd;
	color: #234567;
	padding: 12px;
	margin-left: auto;
	margin-right: auto;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	border-color: #AABBCC transparent #AABBCC transparent;
	border-style: solid;
	border-width: 15px 15px 15px 20px;
}
</style>';
?>

<script>
isBusy = false;
	function displayNewScreen(id){
		htmlData('city.php', 'ch='+id);
		displayScreen(id);
	}
	function displayScreen(id){
		if(!isBusy){
			isBusy = true;
			closeALL();
			var countUp = 1;
			setTimeout("displayFade(" + countUp + ",'" + id + "')", 200);
		}
    }
	function displayFade(countUp, id){
		var e = document.getElementById(id);
		e.style.filter = 'alpha(opacity=' + (countUp * 10) + ')';
		e.style.opacity = countUp / 10;
		e.style.display = 'block';
		countUp += 1;
		if (countUp < 10) {
			setTimeout("displayFade(" + countUp + ",'" + id + "')", 75);
		} 
		else {
			isBusy = false;
		}
	}
	function closeALL(){
		if(section1.style.opacity != 0 || section1.style.display == 'block'){
			section1.style.opacity = 0;
			section1.style.display = 'none';
		}	
		if (section2.style.opacity != 0 || section2.style.display == 'block'){
			section2.style.opacity = 0;
			section2.style.display = 'none';
		}
	} 
</script>
 
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />  
   <script src="ajax.js" type="text/javascript"></script>  
 
<form method="post">  
<a href="#" onclick="htmlData('city.php', 'ch=USA')">USA</a>
<select name="country" onchange="htmlData('city.php', 'ch='+this.value)" />  
  <option value="#">-Select-</option>  
  <option value="India">India</option>  
  <option value="USA">USA</option>  
</select>  
<input type="submit" value="Submit" />

<input type="text" value="" onblur="htmlData('city.php', 'ch='+this.value)">  
 <div id="txtResult"></div>
</form>  
<div id="posted"></div>
<table height=400px width=100%><tr><td valign="center"><center>
		<table><tr><td>
		<b>Username: </b></td><td><input name="euser" type="text" value="1"></td></tr><tr><td>
		<b>Password: </b></td><td><input name="epass" type="password" value=""></td></tr></table> 
		</center></td></tr></table>
		<div id="posted"></div>
		<input type="button" onclick="htmlData('fadetest.php', 'ch='+id);" value="Login"/>

<?php 
if (isset($_POST['country'])){
	echo'success';
}
if (isset($_POST['cityList'])){
	echo'success2',$_POST['cityList'];
}
if (isset($_GET['euser'])){
	echo'success3'.$_GET['euser'];
}
if (isset($_GET['ch'])){
	echo'success3'.$_GET['ch'].'/';
}

echo'
<a href="#" onclick="displayNewScreen('."'section0'".');">Full</a>
<a href="#" onclick="displayScreen('."'section1'".');">Section1</a>
<a href="#" onclick="displayScreen('."'section2'".');">Section2</a>
<div id="container">
<div id="section0" class="zone" style="display:none;"><div id="txtResult"></div></div>
<div id="section1" class="zone" style="display:block;">Hello world, this is a sample<br />paragraph to test fade1</div>
<div id="section2" class="zone" style="display:none;">Hello world, this is a sample<br />paragraph to test fade2</div>
</div>
</body>';
?>