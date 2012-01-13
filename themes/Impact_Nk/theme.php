<?php
/************************************************
*    Thème Impact_Nk pour Nuked Klan    *
*    Design :  djGrim (http://www.impact-design.fr/)    *
*    Codage : fce & huge (http://www.impact-design.fr/)            *
************************************************/
defined("INDEX_CHECK") or die ("<div style=\"text-align: center;\">Accès interdit</div>");
include(dirname(__FILE__) . "/block-best.php");
include(dirname(__FILE__) . '/admin/config_best_unique.php');
$module_2 = explode('|', $config_best['affiche-block-unique']);

foreach ($module_2 as $module_2){
        $module_aff_unique[$module_2] = $module_2;
}
include(dirname(__FILE__) . '/admin/complet.php');
$module_2 = explode('|', $config_best['complet']);

foreach ($module_2 as $module_2){
        $complet[$module_2] = $module_2;
}

function top(){
        global $nuked, $theme, $user, $language, $bgcolor2, $bgcolor1, $color1, $complet, $module_aff_unique;
        
        translate("themes/Impact_Nk/lang/" . $language . ".lang.php");
        include(dirname(__FILE__) . '/admin/logo.php');
?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
                <head>
                        <meta name="keywords" content="<?php echo $nuked['keyword'] ?>" />
                        <meta name="Description" content="<?php echo $nuked['description'] ?>" />
                        <meta http-equiv="content-style-type" content="text/css" />
                        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
                        <!-- IE Mode Compatibility -->
                        <meta http-equiv="X-UA-Compatible" content="IE=8" />
                        <title><?php echo $nuked['name'] ?> - <?php echo $nuked['slogan'] ?></title>
                        <link rel="search" type="application/opensearchdescription+xml" title="<?php echo $nuked['name']; ?>" href="opensearch.php" />
                        <link rel="shortcut icon"  type="image/x-icon" href="images/favicon.ico" />
                        <link title="css" rel="stylesheet" type="text/css" href="themes/<?php echo $theme; ?>/style.css" media="screen" />
                </head>
                <body>
                        <div id="main">
                                <div id="header">
                                        <div id="b-top">
                                                <div id="c-right"></div>
                                                <div id="c-left"></div>
                                                <div id="c-center">
                                                        <div id="ban">
                                                                <div style="margin-left:30px;float:left;">
                                                                        <img src="<?php echo $logo; ?>" alt="logotitre" width="121" height="108" />
                                                                </div>
                                                                <div style="float:left;">
                                                                        <h1><?php echo $nuked['name'] ?> - <?php echo $nuked['slogan'] ?></h1>
                                                                </div>
                                                                <div style="clear:both;"></div>
                                                        </div>
                                                        <div id="nav">
<?php
                                                                include(dirname(__FILE__) . '/blocks/test.php');
                                                                include(dirname(__FILE__) . '/admin/menu.php');
?>
                                                                <div id="buttons">
                                                                    <a id="bt1" title="<?php echo $menu[1]; ?>" href="<?php echo $menu1[1]; ?>"><?php echo $menu[1]; ?></a>
                                                                    <a id="bt2" title="<?php echo $menu[2]; ?>" href="<?php echo $menu1[2]; ?>"><?php echo $menu[2]; ?></a>
                                                                    <a id="bt3" title="<?php echo $menu[3]; ?>" href="<?php echo $menu1[3]; ?>"><?php echo $menu[3]; ?></a>
                                                                    <a id="bt4" title="<?php echo $menu[4]; ?>" href="<?php echo $menu1[4]; ?>"><?php echo $menu[4]; ?></a>
                                                                </div>
                                                                <form class="header" id="search" action="index.php?file=Search&amp;op=mod_search" method="post">
                                                                    <div>
                                                                    <input type="text" name="main" id="keywords" value="" />
                                                                    <input type="submit" name="submit" class="submit" value="<?php echo _INSEARCH; ?>" />
                                                                    </div>
                                                                </form>
                                                        </div>
                                                        <div id="misc">
                                                                <div id="links">
                                                                        <a href="index.php?file=Contact"><?php echo _INCONTACT; ?></a> -
<?php
                                                                        if (!$user){
?>
                                                                        <a href="index.php?file=User&amp;op=reg_screen"><?php echo _ININSCRIPT; ?></a>
<?php
                                                                        }
                                                                        elseif ($user[1] > 2){
?>
                                                                        <a href="index.php?file=Admin"><?php echo _INADMIN; ?></a>
<?php
                                                                        }
                                                                        else{
?>
                                                                        <a href="index.php?file=User"><?php echo _INCOMPTE; ?></a>
<?php
                                                                        }
?>
                                                                </div>
                                                                <div id="dateContent">
                                                                        <?php echo _INSOMMES; ?><?php echo nkDate(time()); ?>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <div id="content">
                                        <div id="b-right" class="blocks"></div>
                                        <div id="b-left" class="blocks"></div>
<?php
        if($_REQUEST['file'] == $complet[$_REQUEST['file']] AND $_REQUEST['page'] != "admin"){
?>
                                        <div id="blocks-center2"><div>
                                        <?php get_blok('centre'); ?>
                                </div>
<?php
        }
        else if ($_REQUEST['file'] == $module_aff_unique[$_REQUEST['file']] || $_REQUEST['page'] == "admin"){
?>
                                        <div id="site1">
                                                <div id="b-center" class="blocks">
                                                        <div id="blocks-left">
                                                                <?php get_blok('gauche'); ?>
                                                                <div style="height:30px;">&nbsp;</div>
                                                        </div>
                                                        <div id="blocks-center1"><div>
                                                        <?php get_blok('centre'); ?>
                                                </div>
<?php
        }
        else{
?>
                                        <div id="site">
                                                        <div id="b-center" class="blocks">
                                                                <div id="blocks-right">
                                                                        <?php get_blok('droite'); ?>
                                                                        <div style="height:30px;">&nbsp;</div>
                                                                </div>
                                                                <div id="blocks-left">
                                                                        <?php get_blok('gauche'); ?>
                                                                        <div style="height:30px;">&nbsp;</div>
                                                                </div>
                                                                <div id="blocks-center">
                                                                <?php get_blok('centre'); ?>                                                        
                                                                        <div class="open1nn"></div>
<?php
        }
}

function footer(){
    global $nuked, $theme, $complet;
?>
                                                                <div>
                                                                <?php get_blok('bas'); ?>
                                                        </div>
                                                        <div style="height:30px;">&nbsp;</div>
                                                </div>
                                        </div>
                                        <div style="clear: both;" ></div>
                                </div>
                        <?php if($_REQUEST['file'] != $complet[$_REQUEST['file']]) echo '</div>'; ?>
                        <div id="b-bottom">
                                <div id="bottom">
                                        <div id="footer">
                                                <div class="copyright1"></div>
                                                <div class="copyright">
                                                        <a href="http://www.design-impacts.fr" style="text-decoration:none;color:#ffffff;">
                                                                Design by DjGr!m &amp; Codage by fce/G4V
                                                        </a>
                                                </div>
                                        </div>
                                </div>
                        </div>
<?php
    if ($_REQUEST['file'] != $complet[$_REQUEST['file']]) echo '</div>';
}

function news($data){
        global $theme;
        
        $posted = _NEWSPOSTBY . "&nbsp;<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($data['auteur']) . "\">" . $data['auteur'] . "</a>&nbsp;" . _THE . "&nbsp;". $data['date'];
        $comment = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $data['id'] . "\">" . _NEWSCOMMENT . "</a>&nbsp;(" . $data['nb_comment'] . ")";
?>
<div class="block center">
        <div class="top">
                <div class="bottom">
                    	<h2><?php echo $data['titre']; ?></h2>
						<div style="padding:5px;">
								<div style="float:right;"><?php echo $data['image']; ?></div>
								<span style="color: #ffffff;"><?php echo $data['texte']; ?></span>                                        
						</div>
						<div style="width:100%;">
								<div style="text-align:right;">
										<?php echo $data['friend']; ?> <?php echo $data['printpage']; ?>
								</div>
						</div>
						<div>
							<?php echo $comment; ?> - <?php echo _INPUBL; ?>
							<a href="index.php?file=News&amp;op=categorie&amp;cat_id=<?php echo $data['catid']; ?>">
									<?php echo $data['cat']; ?>
							</a>
						</div>
						<?php echo $posted; ?>
						<br />
				</div>
		 </div>
</div>
<?php
}

function block_centre($block){
        global $theme;
?>
        <div class="bas">
                <div class="titre">
                        <div class="titre2"></div>
                        <div class="titre1"></div>
                </div>
                <div class="txt">
                        <div class="txt1">
                                <?php echo $block['content']; ?>
                        </div>
                </div>
                <div class="footer"></div>
        </div>
<?php
}

function block_bas($block){
        global $theme;
?>
        <div class="bas">
                <div class="titre">
                        <div class="titre2"></div>
                        <div class="titre1"></div>
                </div>
                <div class="txt">
                        <div class="txt1">
                                <?php echo $block['content']; ?>
                        </div>
                </div>
                <div class="footer"></div>
        </div>
<?php
}

function block_gauche($block){
        global $theme;
?>
        <div class="block left">
                <div class="top">
                        <div class="bottom">
                                <h2><?php echo $block['titre']; ?></h2>
                                <?php echo $block['content']; ?>
                        </div>
                </div>
        </div>
<?php
}

function block_droite($block){
        global $theme;
?>
        <div class="block right">
                <div class="top">
                        <div class="bottom">
                                <h2><?php echo $block[titre]; ?></h2>
                                <?php echo $block['content']; ?>
                        </div>
                </div>
        </div>
<?php
}

function opentable(){
        global $nuked, $theme, $nuked_nude, $module_aff_unique,$complet;

        if($_REQUEST['file'] == $complet[$_REQUEST['file']] AND $_REQUEST['page'] != "admin"){
?>
                <div id="open1cc">
                        <h2><?php echo $_REQUEST['file']; ?></h2>
                </div>
                <div id="open2cc">
<?php
        }
        elseif ($_REQUEST['file'] == $module_aff_unique[$_REQUEST['file']] || $_REQUEST['page'] == "admin"){
?>
                <div id="open1ss">
                        <h2><?php echo $_REQUEST['file']; ?></h2>
                </div>
                <div id="open2ss">
<?php
        }
        else{
?>
                <div id="open1nn"></div>
                <div id="open2nn">
<?php
        }
}

function closetable(){
        global $nuked, $theme, $module_aff_unique, $complet;
        if($_REQUEST['file'] == $complet[$_REQUEST['file']] AND $_REQUEST['page'] != "admin"){
?>
                </div>
                <div id="open3cc"></div>
<?php
        }
        elseif ($_REQUEST['file'] == $module_aff_unique[$_REQUEST['file']] || $_REQUEST['page'] == "admin"){
?>
                </div>
                <div id="open3ss"></div>
<?php
        }
        else{
?>
                </div>
                <div id="open3nn"></div>
<?php
        }
}
?>