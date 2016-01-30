                <h1><?php echo $title ?></h1>
                <p><?php echo $i18n['ERROR_FIELDS'] ?></p>
                <div>
                    <ul>
<?php
    foreach ($errors as $error) :
?>
                        <li><?php echo $error ?></li>
<?php
    endforeach
?>
                    </ul>
                </div>
                <div id="links">
                    <a href="<?php echo $backLink ?>"><?php echo $i18n['BACK'] ?></a>
                </div>
