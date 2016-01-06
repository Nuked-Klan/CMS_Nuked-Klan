
<b><?php echo __('NOTE') ?> :</b>&nbsp;<?php

$n = 0;

if ($nbVote > 0) :
    for ($i = 2; $i <= $note; $i += 2) :

        ?><img style="border: 0;" src="modules/Vote/images/z1.png" alt="" title="<?php echo $note ?>/10 (<?php echo $nbVote ?>&nbsp;<?php echo __('VOTES') ?>)" /><?php

        $n++;
    endif;

    if (($note - $i) != -2) :

        ?><img style="border: 0;" src="modules/Vote/images/z2.png" alt="" title="<?php echo $note ?>/10 (<?php echo $nbVote ?>&nbsp;<?php echo __('VOTES') ?>)" /><?php

        $n++;
    endif;

    for ($z = $n; $z < 5; $z++) :

        ?><img style="border: 0;" src="modules/Vote/images/z3.png" alt="" title="<?php echo $note ?>/10 (<?php echo $nbVote ?>&nbsp;<?php echo __('VOTES') ?>)" /><?php

    endif;
else :

    echo __('NOT_EVAL');

endif;

if ($userLevel >= $levelAccess && $levelAccess > -1) :

    ?>&nbsp;<small>[ <a href="#" onclick="javascript:window.open('index.php?file=Vote&amp;op=post&amp;id=<?php echo $vid ?>&amp;module=<?php echo $module ?>','screen','toolbar=0,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=350,height=150,top=30,left=0');return(false)"><?php echo __('RATE') ?></a> ]</small><?php

endif;
