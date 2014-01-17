<?php
// This file and all of its containing script is property of James Hamet.

echo'
<body>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-ca">

<style type="text/css">
html, body {height:100%; margin:0; padding:0;}
#page-background {position:fixed; top:0; left:0; width:100%; height:100%; -moz-opacity: 0.8;
opacity: 0.8;}
#content {position:relative; z-index:1; padding:10px;}

body {
	color: #4E3D4E;
	background-color: #FEFEFE;
}
.field {
	font-size:small;
	width: 800px;
	overflow: auto;
	margin-left: auto;
	margin-right: auto;
}
.menu {
	width: 1200px;
	background-color: transparent;
	padding: 10px;
	margin-left: auto;
	margin-right: auto;
	-moz-border-radius: 15px;
	-webkit-border-radius: 15px;
}
a {
	text-decoration:none;
	filter:alpha(opacity=80);
	-moz-opacity: 0.8;
	opacity: 0.8;
}
a:hover {
	filter:alpha(opacity=100); 
	-moz-opacity: 1.0; 
	opacity: 1.0;
}
#container {
	position: relative;
	top: 15px;
	width: 1100px;
	background-color: #DDDDDD;
	padding: 10px;
	margin-left: auto;
	margin-right: auto;
	-moz-border-radius: 15px;
	-webkit-border-radius: 15px;
	-moz-opacity: 0.9;
	opacity: 0.9;
}
#textzone {
	min-height: 60px;
	background-color: #F8FBEF;
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
</style>
<!--[if IE 6]>
<style type="text/css">
html {overflow-y:hidden;}
body {overflow-y:auto;}
#page-background {position:absolute; z-index:-1;}
#content {position:static;padding:10px;}
<![endif]-->
</style>';
?>

<script>
isBusy = false;
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
		if(section0.style.opacity != 0 || section0.style.display == 'block'){
			section0.style.opacity = 0;
			section0.style.display = 'none';
		}	
		if(section0_1.style.opacity != 0 || section0_2.style.display == 'block'){
			section0_1.style.opacity = 0;
			section0_1.style.display = 'none';
		}	
		if(section0_2.style.opacity != 0 || section0_2.style.display == 'block'){
			section0_2.style.opacity = 0;
			section0_2.style.display = 'none';
		}	
		if(section1.style.opacity != 0 || section1.style.display == 'block'){
			section1.style.opacity = 0;
			section1.style.display = 'none';
		}	
		if (section2.style.opacity != 0 || section2.style.display == 'block'){
			section2.style.opacity = 0;
			section2.style.display = 'none';
		}
		if (section3.style.opacity != 0 || section3.style.display == 'block'){
			section3.style.opacity = 0;
			section3.style.display = 'none';
		}
		if (section4.style.opacity != 0 || section4.style.display == 'block'){
			section4.style.opacity = 0;
			section4.style.display = 'none';
		}
	} 
</script>

<?php 
function indent($string){
					$string = '<center><table width=90%><tr><td>'.$string.'</td></tr></table></center>';
					return $string;
					}

echo'<title>James Hamet</title>
<div id="page-background"><img src="http://darkdreams.zzl.org/tools/mysite/abstract.jpg" width="100%" height="110%"></div><div id="content">
<div class="menu" align="center">
<img src="http://darkdreams.zzl.org/tools/mysite/myimage.png" border="0" width=600px alt="James Hamet" title="James Hamet" /><br />
<a href="#section0" onclick="displayScreen('."'section0'".');"><img src="http://darkdreams.zzl.org/tools/mysite/mysite_button_music.png" border="0" width=150px hspace="20" alt="Music" title="Music" /></a>
<a href="#section1" onclick="displayScreen('."'section1'".');"><img src="http://darkdreams.zzl.org/tools/mysite/mysite_button_programming.png" border="0" width=150px hspace="20" alt="Programming" title="Programming" /></a>
<a href="#section2" onclick="displayScreen('."'section2'".');"><img src="http://darkdreams.zzl.org/tools/mysite/mysite_button_sports.png" border="0" width=150px hspace="20" alt="Sports" title="Sports" /></a>
<a href="#section3" onclick="displayScreen('."'section3'".');"><img src="http://darkdreams.zzl.org/tools/mysite/mysite_button_writing.png" border="0" width=150px hspace="20" alt="Writing" title="Writing" /></a>
<a href="#section3" onclick="displayScreen('."'section4'".');"><img src="http://darkdreams.zzl.org/tools/mysite/mysite_button_lang.png" border="0" width=150px hspace="20" alt="Languages" title="Languages" /></a>
</div>
<div id="container"><div id="textzone">
<div id="section0" style="display:block;"><center><font size=6><b>Virtual Composition</font><br /><font size=4>
<a href="#section0_1" onclick="displayScreen('."'section0_1'".');">Guitar</a> - <a href="#section0_2" onclick="displayScreen('."'section0_2'".');">Viola</a></center></b>
<br /><br />Though science is my passion, music and poetry is my expression. Timing the notes just right, and giving them meaningful pitch has always been a pleasurable past time activity of mine, whether I produced the music with my guitar, or my viola, or with virtual instruments offered to me by various music development programs. Below are a few examples of music I have produced using virtual instruments and audio recordings. All audio used for the production of these songs is original unless otherwise stated.
	<br /><br /><br /><div class="field"><center>
	<img style="visibility:hidden;width:0px;height:0px;" border=0 width=0 height=0 src="http://c.gigcount.com/wildfire/IMP/CXNID=2000002.0NXC/bT*xJmx*PTEzMDg5ODEzOTUzNjgmcHQ9MTMwODk4MTQ1NTY3NyZwPTE4NTM5MSZkPSZnPTImbz*2ZTljNjdmYjM*MDY*MDY4YTgy/OWIyODJjMWZjMTMwNCZvZj*w.gif" />
	<embed flashvars="song_id=180225&gig_lt=1308981395368&gig_pt=1308981455677&gig_g=2" height="80" src="http://www.muziboo.com/swf/new_embed_player.swf" width="345">
	</embed></center></td></tr><tr><td>
	<i>Though the song lacks lyrics, it is a good example of harmonics melodies played together with virtual instruments. I was did not receive much inspiration from external sources for this song. I simply tampered with various chords, and pieced these little jams together.</i>
	</div>
	<br /><br /><div class="field"><center>
	<img style="visibility:hidden;width:0px;height:0px;" border=0 width=0 height=0 src="http://c.gigcount.com/wildfire/IMP/CXNID=2000002.0NXC/bT*xJmx*PTEzMDg5ODIxODgyMTUmcHQ9MTMwODk4MjE4OTQzNSZwPTE4NTM5MSZkPSZnPTImbz*2ZTljNjdmYjM*MDY*MDY4YTgy/OWIyODJjMWZjMTMwNCZvZj*w.gif" /><embed flashvars="song_id=177607&gig_lt=1308982188215&gig_pt=1308982189435&gig_g=2" height="80" src="http://www.muziboo.com/swf/new_embed_player.swf" width="345"></embed>
	</center></td></tr><tr><td>
	<i>In this <b>mix</b>, I sought to produce music that harboured a good deal of build up and intense power chords by using excepts from four different songs by The Immediate. The key difficulty here was getting these very different songs to work together in a way that made them sound as one. To do this, I had to create several guitar rifts and cello pieces for a final product that was definitely worth the effort.</i>
	</div>
</div>
<div id="section0_1" style="display:block;"><center><font size=6><b>Guitar</font><br /><font size=4>
<a href="#section0_2" onclick="displayScreen('."'section0_2'".');">Viola</a> - <a href="#section0" onclick="displayScreen('."'section0'".');">Virtual</a></center></b>
Guitar page, include videos and images.
</div>
<div id="section0_2" style="display:block;"><center><font size=6><b>Viola</font><br /><font size=4>
<a href="#section0" onclick="displayScreen('."'section0'".');">Virtual</a> - <a href="#section0_1" onclick="displayScreen('."'section0_1'".');">Guitar</a></center></b>
Viola page, include videos and images.
</div>
<div id="section1" style="display:none;">Hello world, this is a sample<br />paragraph to test fade1</div>
<div id="section2" style="display:none;">Hello world, this is a sample<br />paragraph to test fade2</div>
<div id="section3" style="display:none;">Hello world, this is a sample<br />paragraph to test fade3</div>
<div id="section4" style="display:none;">Hello world, this is a sample<br />paragraph to test fade4</div>
<br /><hr />
<center><font size=2>Copyright © 2011 James Hamet, All Rights Reserved.</font></center>
</div></div>
</div></div>
</body>
</html>';
?>