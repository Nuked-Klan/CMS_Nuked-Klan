
<br />
<?php
foreach ($teamList as $team) :
    prepareTeamData($team);
?>

<div class="nkAlignCenter">
<?php
    if ($team['cid'] != '') :
        //if ($team['coverage'] != '') :
        //
        //else :
?>
    <a href="index.php?file=Team&amp;cid=<?php echo urlencode(nkHtmlEntityDecode($team['cid'])) ?>"><big><b><?php echo $team['titre'] ?></b></big></a>
<?php
        //endif;
    else :
?>
    <big><b><?php echo $team['titre'] ?></b></big>
<?php
    endif
?>

</div>
<table class="nkTeamList nkBgColor2 nkBorderColor3">
    <tr class="nkBgColor3">
        <th class="nkMemberCountry">&nbsp;</th>
        <th class="nkMemberNickname"><b><?php echo __('NICK') ?></b></th>
<?php
    foreach ($userSocialData as $userSocial) :
?>
        <th class="<?php echo $userSocial['cssClass'] ?>"><b><?php echo nkUserSocial_getLabel($userSocial) ?></b></th>
<?php
    endforeach
?>
        <th class="nkMemberRank"><b><?php echo __('RANK') ?></b></th>
    </tr>
<?php

    if ($team['nbMembers'] > 0) :
        $j = 0;

        foreach ($team['teamMembers'] as $teamMember) {
            prepareTeamMemberData($teamMember, $team);
?>
    <tr class="<?php echo ($j++ % 2 == 1) ? 'nkBgColor1' : 'nkBgColor2' ?>">
        <td class="nkMemberCountry"><img src="images/flags/<?php echo $teamMember['countryImg'] ?>" alt="" title="<?php echo $teamMember['country'] ?>" /></td>
        <td class="nkMemberNickname"><a href="<?php echo $teamMember['memberUrl'] ?>" title="<?php echo __('VIEW_PROFIL') ?>"><b><?php echo $teamMember['fullName'] ?></b></a></td>
<?php
            foreach ($userSocialData as $userSocial) :
?>
        <td class="nkUserSocial <?php echo $userSocial['cssClass'] ?>"><?php echo nkUserSocial_formatImgLink($userSocial, $teamMember) ?></td>
<?php
            endforeach
?>

        <td class="nkMemberRank"><?php echo $teamMember['rankName'] ?></td>
    </tr>
<?php
        }

    else :
?>
    <tr>
        <td class="nkAlignCenter" colspan="<?php echo (3 + count($userSocialData)) ?>"><?php echo __('NO_TEAM_MEMBERS') ?></td>
    </tr>
<?php
    endif
?>
</table>
<br /><br />
<?php
    $j = 0;
endforeach
?>
