<?php
function clean_string($string) {
					$bad = array("content-type","bcc:","to:","cc:","href");
					return str_replace($bad,"",$string);
					}
function fix_string($string) {
					$string = nl2br($string);
					$string = preg_replace('#\[color=(red|green|blue|yellow|purple|olive)\](.+)\[/color\]#isU', '<span style="color:$1">$2</span>', $string);
					$string = preg_replace('#\[i\](.+)\[/i\]#isU', '<em>$1</em>', $string);
					$string = preg_replace('#\[b\](.+)\[/b\]#isU', '<b>$1</b>', $string);
					$string = preg_replace('#\[u\](.+)\[/u\]#isU', '<u>$1</u>', $string);
					$string = preg_replace('#http://[a-z0-9._/-]+#i', '<a href="$0">$0</a>', $string);
					$string = preg_replace('#\[img\](.+)\[/img\]#isU', '<img src="http://$1" />', $string);
					$string = preg_replace('#\[table\](.+)\[/table\]#isU', '<table>$1</table>', $string);
					$string = preg_replace('#\[tr\](.+)\[/tr\]#isU', '<tr>$1</tr>', $string);
					$string = preg_replace('#\[td\](.+)\[/td\]#isU', '<td>$1</td>', $string);
					$string = preg_replace('#\[center\](.+)\[/center\]#isU', '<center>$1</center>', $string);
					return $string;
					}
function indent($string){
					$string = '<center><table width=90%><tr><td>'.$string.'</td></tr></table></center>';
					return $string;
					}
?>