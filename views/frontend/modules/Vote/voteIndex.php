
<b><?php echo __('NOTE') ?> :</b>&nbsp;<?php

$n = 0;

if ($nbVote > 0) :

    ?><span title="<?php echo $note ?>/10 (<?php echo $nbVote ?>&nbsp;<?php echo __('VOTES') ?>)"><?php

    for ($i = 2; $i <= $note; $i += 2) :

        ?><img class="nkNoBorder voteStar" src="<?php echo $voteCfg['fullStar'] ?>" alt="" /><?php

        $n++;
    endfor;

    if (($note - $i) != -2) :

        ?><img class="nkNoBorder voteStar" src="<?php echo $voteCfg['halfStar'] ?>" alt="" /><?php

        $n++;
    endif;

    for ($z = $n; $z < 5; $z++) :

        ?><img class="nkNoBorder voteStar" src="<?php echo $voteCfg['emptyStar'] ?>" alt="" /><?php

    endfor;

    ?></span><?php

else :

    echo __('NOT_EVAL');

endif;

if ($userLevel >= $levelAccess && $levelAccess > -1) :

    ?>&nbsp;<small>[ <a href="#" onclick="javascript:window.open('index.php?file=Vote&amp;op=post&amp;im_id=<?php echo $imId ?>&amp;module=<?php echo $module ?>','screen','toolbar=0,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=350,height=150,top=30,left=0');return(false)"><?php echo __('RATE') ?></a> ]</small><?php

endif;
