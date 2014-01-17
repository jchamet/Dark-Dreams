    function GetXmlHttpObject(handler){  
       var objXMLHttp=null  
       if (window.XMLHttpRequest){  
           objXMLHttp=new XMLHttpRequest()  
       }  
       else if (window.ActiveXObject){  
           objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")  
       }  
       return objXMLHttp  
    }  
      
    function stateChanged(){  
       if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){  
               document.getElementById("txtResult").innerHTML= xmlHttp.responseText;  
       }   
    }  
      
    function htmlData(url, qStr){  
       if (url.length==0){  
           document.getElementById("txtResult").innerHTML="";  
           return;  
       }  
       xmlHttp=GetXmlHttpObject()  
       if (xmlHttp==null){  
           alert ("Browser does not support HTTP Request");  
           return;  
       }  
      
       url=url+"?"+qStr;  
       url=url+"&sid="+Math.random();  
       xmlHttp.onreadystatechange=stateChanged;  
       xmlHttp.open("GET",url,true) ;  
       xmlHttp.send(null);  
    }  
	
	
	$(function() {
	$(".submit").click(function() {
		var name = $("#name").val();
		var password = $("#password").val();
		var dataString = 'name='+ name + '&password=' + password;
		if(name=='' || password==''){
			$('.success').fadeOut(200).hide();
			$('.error').fadeOut(200).show();
		}
		else{
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

	
	isBusy = false;
	function displayNewScreen(id, extra){
		htmlData('main.php', extra);
		displayScreen(id);
	}
	function displayScreen(id){
		if(!isBusy){
			isBusy = true;
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