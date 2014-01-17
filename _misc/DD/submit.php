<?php session_start(); ?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
<script type="text/javascript" >
$(function() {
$(".submit").click(function() {
var name = $("#name").val();
var password = $("#password").val();
var dataString = 'name='+ name + '&password=' + password;

if(name=='' || password=='')
{
$('.success').fadeOut(200).hide();
$('.error').fadeOut(200).show();
}
else
{
$.ajax({
type: "POST",
url: "submit.php",
data: dataString,
success: function(){
$('.success').fadeIn(200).show();
$('.error').fadeOut(200).hide();
}
});
}
return false;
});
});
</script>
<?php
echo'
<form method="post" name="form">
<input id="name" name="name" type="text" />
<input id="password" name="password" type="password" />
</li></ul>
<div >
<input type="submit" value="Submit" class="submit"/>
<span class="error" style="display:none">Please Enter Valid Data</span>
<span class="success" style="display:none">Registration Successfully</span>
</div></form>';

if($_POST){
$_SESSION['name']=$_POST['name'];
}
if (isset($_SESSION['name'])){
	echo $_SESSION['name'];
}
?>