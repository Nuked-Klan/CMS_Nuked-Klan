
<br />
<div class="nkAlignCenter">
    <h3><?php echo __('MEMBERS_LIST') ?></h3>
    <p><small>[ <a href="index.php?file=Members"><?php echo __('ALL') ?></a> | <?php

$num = count($alpha) - 1;
$i   = 0;

while (list(, $letter) = each($alpha)) :
    if ($currentLetter == $letter) :
        echo $letter;
    else :

        ?><a href="index.php?file=Members&amp;letter=<?php echo $letter ?>"><?php echo $letter ?></a><?php

    endif;

    if ($i == round($num / 2)) :

        ?> ]<br />[ <?php

    elseif ($i != $num) :

        ?> | <?php

    endif;

    $i++;
endwhile;

    ?> ]</small></p>
</div>
<?php echo $pagination ?>
<table id="nkMembersList" class="nkBgColor2 nkBorderColor3">
    <tr class="nkBgColor3">
        <th class="nkMemberCountry">&nbsp;</th>
        <th class="nkMemberNickname"><?php echo __('NICK') ?></th>
<?php
foreach ($userSocialData as $userSocial) :
?>
        <th class="<?php echo $userSocial['cssClass'] ?>"><?php echo nkUserSocial_getLabel($userSocial) ?></th>
<?php
endforeach
?>
    </tr>
<?php
$j = 0;

foreach ($membersList as $member) :
?>

    <tr class="<?php echo ($j++ % 2 == 1) ? 'nkBgColor1' : 'nkBgColor2' ?>;">
        <td class="nkMemberCountry"><img src="images/flags/<?php echo $member['country'] ?>" alt="" title="<?php echo pathinfo($member['country'], PATHINFO_FILENAME) ?>" /></td>
        <td class="nkMemberNickname"><a href="index.php?file=Members&amp;op=detail&amp;autor=<?php echo urlencode($member['nickname']) ?>" title="<?php echo __('VIEW_PROFIL') ?>"><b><?php echo $member['nickname'] ?></b></a></td>

<?php
    foreach ($userSocialData as $userSocial) :
?>
        <td class="<?php echo $userSocial['cssClass'] ?> nkUserSocial">
            <?php echo nkUserSocial_formatImgLink($userSocial, $member) ?>
        </td>
<?php
    endforeach
?>
    </tr>
<?php
endforeach;

if ($nbMembers == 0) :
?>
    <tr>
        <td colspan="<?php echo (2 + count($userSocialData)) ?>" class="nkAlignCenter"><?php echo __('NO_MEMBERS') ?></td>
    </tr>
<?php
endif
?>
</table>
<?php echo $pagination ?>
<br />
<div class="nkAlignCenter">
<?php
if ($currentLetter != '') :
?>
    <?php echo $nbMembers ?>&nbsp;<?php echo __('MEMBERS_FOUND') ?> <b><?php echo $currentLetter ?></b>
<?php

else :

?>
    <?php echo __('THERE_ARE') ?>&nbsp;<?php echo $nbMembers ?>&nbsp;<?php echo __('MEMBERS_REGISTRED') ?>&nbsp;<?php echo nkDate($GLOBALS['nuked']['date_install']) ?><br />
<?php
    if ($nbMembers > 0) :
?>
    <?php echo __('LAST_MEMBER') ?> <a href="index.php?file=Members&amp;op=detail&amp;autor=<?php echo urlencode($lastMember) ?>"><b><?php echo $lastMember ?></b></a>
<?php
    endif;
endif;
?>
</div>
<br />
