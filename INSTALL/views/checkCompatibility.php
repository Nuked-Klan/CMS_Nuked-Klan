                <div style="text-align: center;margin:30px auto;">
                    <h3 style="margin-bottom:5px;" ><?php echo _CHECKCOMPATIBILITYHOSTING ?></h3>
                    <table style="width:500px;margin:15px auto;border:1px solid #ddd;text-align:left;background:#fff;" cellpadding="3">
                        <tr>
                            <td style="width:80%;"><b><?php echo _COMPOSANT ?></b></td>
                            <td style="width:20%;text-align:center;"><b><?php echo _COMPATIBILITY ?></b></td>
                        </tr>
<?php
    $i=0;

    foreach ($requirements as $extensionName => $requirement) :
        if ($requirement == 'enabled')
            $src = 'images/ok.png';
        elseif ($requirement == 'optional-disabled')
            $src = 'images/warning.png';
        else
            $src = 'images/nook.png';

        $bg  = ($i % 2 == 0) ? '#e9e9e9' : '#f5f5f5';
?>

                        <tr style="background:<?php echo $bg ?>;">
                            <td><?php echo constant($extensionName) ?></td>
                            <td style="text-align:center;"><img src="<?php echo $src ?>" alt="" /></td>
                        </tr>

<?php
        if (in_array($requirement, array('required-disabled', 'optional-disabled'))) :
?>
                        <tr>
                            <td colspan="2" class="<?php echo ($requirement == 'optional-disabled') ? 'warning' : 'error' ?>_compatibility"><?php echo constant($extensionName .'ERROR') ?></td>
                        </tr>
<?php
        endif;

        $i++;
    endforeach;
?>
                    </table>
<?php
    if (! in_array('optional-disabled', $requirements)) :
?>
                        <a href="index.php?action=chooseSendStats" class="button" ><?php echo _CONTINUE ?></a>
<?php
    else :
?>
                        <p><?php echo _BADHOSTING ?></p>
                        <a href="index.php?action=chooseSendStats" class="button" ><?php echo _FORCE ?></a>
<?php
    endif
?>
                    </div>
