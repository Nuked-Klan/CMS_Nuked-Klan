<?php
/************************************************
*	Thème Impact_Nk pour Nuked Klan	*
*	Design :  Djgrim (http://www.impact-design.fr/)	*
*	Codage : fce (http://www.impact-design.fr/)			*
************************************************/
defined("INDEX_CHECK") or die ("<div style=\"text-align: center;\">Access deny</div>");

echo '<div class="colog">',_INWELC,' ',$user[2],' - <a href="index.php?file=User" style="margin-right: 5px;">',_INYCOM,'</a>',"\n"
, ' - <a href="index.php?file=User&amp;nuked_nude=index&amp;op=logout">',_INDECO,'</a>',"\n"
, '<div>', $mess ,'</div></div>',"\n";
?>