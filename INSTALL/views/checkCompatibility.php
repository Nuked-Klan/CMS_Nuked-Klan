                <div style="text-align: center;margin:30px auto;">
                    <h3 style="margin-bottom:5px;" ><?php echo $i18n['CHECK_COMPATIBILITY_HOSTING'] ?></h3>
                    <table style="width:500px;margin:15px auto;border:1px solid #ddd;text-align:left;background:#fff;" cellpadding="3">
                        <tr>
                            <td colspan="2" style="width:80%;"><b><?php echo $i18n['COMPOSANT'] ?></b></td>
                            <td style="width:20%;text-align:center;"><b><?php echo $i18n['COMPATIBILITY'] ?></b></td>
                        </tr>
<?php
    $i = 0;

    $nbRowspan = $nbChmodDirectory;

    foreach ($requirements as $extensionName => $requirement) :
        if (strpos($extensionName, 'CHMOD_TEST') !== false
            && in_array($requirement, array('required-disabled', 'optional-disabled'))
        )
            $nbRowspan++;
    endforeach;
    
    foreach ($requirements as $extensionName => $requirement) :
        if ($requirement == 'enabled')
            $src = 'media/images/ok.png';
        elseif ($requirement == 'optional-disabled')
            $src = 'media/images/warning.png';
        else
            $src = 'media/images/nook.png';

        $bg  = ($i % 2 == 0) ? '#e9e9e9' : '#f5f5f5';
?>

                        <tr style="background:<?php echo $bg ?>;">
<?php
        if (strpos($extensionName, 'CHMOD_TEST') !== false) :
            $nbColspan = 2;

            if (! isset($rowspan)) :
?>
                            <td rowspan="<?php echo $nbRowspan ?>"><?php echo $i18n['CHMOD_TEST'] ?></td>
<?php
                $rowspan = true;
            endif;

            $dir = str_replace('CHMOD_TEST_', '', $extensionName);
            $extensionName = str_replace('_'. $dir, '', $extensionName);
?>
                            <td><?php echo ($dir == 'WEBSITE_DIRECTORY') ? $i18n['WEBSITE_DIRECTORY'] : $dir ?></td>
                            <td style="text-align:center;"><img src="<?php echo $src ?>" alt="" /></td>
<?php
        else :
            $nbColspan = 3;
?>
                            <td colspan="2"><?php echo $i18n[$extensionName] ?></td>
                            <td style="text-align:center;"><img src="<?php echo $src ?>" alt="" /></td>
<?php
        endif
?>
                        </tr>

<?php
        if (in_array($requirement, array('required-disabled', 'optional-disabled'))) :
?>
                        <tr>
                            <td colspan="<?php echo $nbColspan ?>" class="<?php echo ($requirement == 'optional-disabled') ? 'warning' : 'error' ?>_compatibility">
<?php
            if (strpos($extensionName, 'CHMOD_TEST') !== false) :
                if ($dir == 'WEBSITE_DIRECTORY') :
?>
                                <?php echo sprintf($i18n['CHMOD_TEST_ERROR'], substr(sprintf('%o', fileperms('../')), -4));

                else : ?>
                                <?php echo sprintf($i18n['CHMOD_TEST_ERROR'], substr(sprintf('%o', fileperms('../'. $dir)), -4));
                endif;
            else :
?>
                                <?php echo $i18n[$extensionName .'_ERROR'] ?>
<?php
            endif
?>
                            </td>
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
                        <a href="index.php?action=chooseSendStats" class="button" ><?php echo $i18n['NEXT'] ?></a>
<?php
    else :
?>
                        <p><?php echo $i18n['BAD_HOSTING'] ?></p>
                        <a href="index.php?action=chooseSendStats" class="button" ><?php echo $i18n['FORCE'] ?></a>
<?php
    endif
?>
                    </div>
