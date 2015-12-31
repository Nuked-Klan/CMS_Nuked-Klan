<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title><?php echo $GLOBALS['nuked']['name'] ?> - <?php echo _ADMINISTRATION ?></title>
        <link rel="stylesheet" href="modules/Admin/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="modules/Admin/css/style.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="modules/Admin/css/invalid.css" type="text/css" media="screen" />
    </head>
    <body id="login">
        <div id="login-wrapper" class="png_bg">
            <div id="login-top">
                <h1><?php echo $GLOBALS['nuked']['name'] ?> - <?php echo _ADMINSESSION ?></h1>
                <img id="logo" src="modules/Admin/images/logo.png" alt="NK Logo" />
            </div>
            <div id="login-content">
<?php
    if (isset($message)) :
?>
                <div style="text-align: center">
<?php
        if ($message == 'zoneAmin') :
?>
                    <?php echo __('ZONE_ADMIN') ?><br />
                    <a href="javascript:history.back()"><b><?php echo __('BACK') ?></b></a>
<?php
        else :
            echo $message;
?>
                </div>
<?php
        endif;
    else :
?>
                <form action="index.php?file=Admin&amp;page=login" method="post">
                    <p>
                        <label><?php echo _NICK ?></label>
                        <input class="text-input" type="text" name="admin_pseudo" value="<?php echo $user['name'] ?>" maxlenght="180" />
                    </p>
                    <div class="clear"></div>
                    <p>
                        <label><?php echo _PASSWORD ?></label>
                        <input class="text-input" type="password" name="admin_password" maxlength="40" />
                    </p>
                    <div class="clear"></div>
                    <p>
                        <input class="button" type="submit" value="<?php echo _TOLOG ?>" />
                        <input class="button" type="button" value="<?php echo _TOBACK ?>" style="margin-right: 10px" onclick="javascript:history.back()" />
                        <input type="hidden" name="formulaire" value="<?php echo $check  ?>" />
                    </p>
                </form>
<?php
    endif
?>
            </div>
        </div>
    </body>
</html>