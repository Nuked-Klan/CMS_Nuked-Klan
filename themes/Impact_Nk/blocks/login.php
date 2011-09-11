<?php
/************************************************
*	Thème Impact_Nk pour Nuked Klan	*
*	Design :  djgrim(http://www.impact-design.fr/)	*
*	Codage : fce (http://www.impact-design.fr/)			*
************************************************/
defined("INDEX_CHECK") or die ("<div style=\"text-align: center;\">Access deny</div>");
?>
                    <form class="header" id="login" action="index.php?file=User&amp;nuked_nude=index&amp;op=login" method="post">
                        <div>
                        <label for="pseudo">Login :</label>
                        <input type="text" maxlength="255" id="pseudo" name="pseudo" value="" />

                        <label for="pass">Password :</label>
                        <input type="password" maxlength="15" id="pass" name="pass" value="" />

                        <input type="hidden" name="remember_me" value="ok" />
                        <input type="submit" class="submit" value="<?php echo _INCONECT; ?>" />
                        </div>
                    </form>