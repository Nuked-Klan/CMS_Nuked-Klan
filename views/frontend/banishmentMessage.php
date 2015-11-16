<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
        <title><?php echo $GLOBALS['nuked']['name'] ?> - <?php echo $GLOBALS['nuked']['slogan'] ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <meta http-equiv="content-style-type" content="text/css" />
        <link title="style" type="text/css" rel="stylesheet" href="themes/<?php echo $GLOBALS['theme'] ?>/style.css" />
    </head>
    <body style="background : <?php echo $GLOBALS['bgcolor2'] ?>">
        <div style="margin: 200px auto; padding: 20px; width: 800px; border: 1px solid <?php echo $GLOBALS['bgcolor3'] ?>; background: <?php echo $GLOBALS['bgcolor1'] ?>; text-align: center">
            <big><b><?php echo $GLOBALS['nuked']['name'] ?> - <?php echo $GLOBALS['nuked']['slogan'] ?></b><br /><br /><?php echo _IPBANNED ?></big>
<?php
    if (! empty($reason)) :
?>
            <br /><p><hr style="color: <?php echo $GLOBALS['bgcolor3'] ?>;height: 1px; width: 95%" />
            <big><b><?php echo _REASON ?></b><br /><?php echo nkHtmlEntityDecode($reason) ?></big></p>
<?php
    endif
?>
            <hr style="color: <?php echo $GLOBALS['bgcolor3'] ?>;height: 1px; width: 95%" /><br />
            <?php echo _DURE ?><?php echo strtolower($duration) ?><br />
            <?php echo _CONTACTWEBMASTER ?> : <a href="mailto:<?php echo $GLOBALS['nuked']['mail'] ?>"><?php echo $GLOBALS['nuked']['mail'] ?></a>
        </div>
    </body>
</html>