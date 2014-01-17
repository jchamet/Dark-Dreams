isBusy = false;
function displayNewScreen(id, extra){
		if(!isBusy){
			isBusy = true;
			dataGrab(extra, id);
			displayScreen(id);
		}
	}
function displayScreen(id){
	var countUp = 1;
	setTimeout("displayFade(" + countUp + ",'" + id + "')", 200);
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