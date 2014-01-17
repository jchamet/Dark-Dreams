<html>
<head>
<script type="text/javascript">
var xmlhttp;
function loadXMLDoc(url,cfunc){
if (window.XMLHttpRequest){
	xmlhttp=new XMLHttpRequest();
  }
else{
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=cfunc;
xmlhttp.open("GET",url,true);
xmlhttp.send();
}
function dataGrab(what, where){
	loadXMLDoc(what,function(){
	if (xmlhttp.readyState==4 && xmlhttp.status==200){
		document.getElementById(where).innerHTML=xmlhttp.responseText;
    }
  });
}
</script>
</head>
<body>

<div id="myDiv"><h2>Let AJAX change this text</h2></div>
<div id="myDiv2"><h2>Let AJAX change this text</h2></div>
<button type="button" onclick="dataGrab('main.php','myDiv2')">Change Content</button>

</body>
</html>

