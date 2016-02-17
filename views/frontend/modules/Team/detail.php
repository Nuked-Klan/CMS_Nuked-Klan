
<?php
if ($GLOBALS['visiteur'] == 9) :
?>
<div class="nkModuleAdminButton nkAlignRight"><a href="index.php?file=Admin&amp;page=user&amp;op=edit_user&amp;id_user=<?php echo $teamMember['id'] ?>"><img class="nkNoBorder" src="images/edition.gif" alt="" title="<?php echo __('EDIT') ?>" /></a>
<?php
    if ($teamMember['id'] != $GLOBALS['user']['id']) : ?>

    <script type="text/javascript">
    // <![CDATA[
    function deleteMember(nickname, id) {
        var confirmTxt = '<?php echo addslashes(__('CONFIRM_TO_DELETE_DATA')) ?>';

        if (confirm(confirmTxt.replace(/%s/, nickname)))
            document.location.href = 'index.php?file=Admin&page=user&op=del_user&id_user=' + id;
    }
    // ]]>
    </script>
    <a href="javascript:deleteMember('<?php echo addslashes($author) ?>', '<?php echo $teamMember['id'] ?>');"><img class="nkNoBorder" src="images/delete.gif" alt="" title="<?php echo __('DELETE') ?>" /></a><?php

    endif

    ?>&nbsp;</div>
<?php
endif
?>
<br />
<object type="application/x-shockwave-flash" data="modules/Members/images/title.swf" width="100%" height="50">
<param name="movie" value="modules/Members/images/title.swf" />
<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />
<param name="wmode" value="transparent" />
<param name="menu" value="false" />
<param name="quality" value="best" />
<param name="scale" value="exactfit" />
<param name="flashvars" value="text=<?php echo flashTextCleaning($author) ?>" /></object>
<?php
if ($teamMemberDetail) :
?>
<div id="nkTeamMemberWrapper">
    <div class="nkTeamMemberDetail">
        <div id="nkTeamMemberPersonalInfo" class="nkTeamMemberDetailWrapper nkBgColor2">
            <div class="nkTeamMemberDetailHead nkBgColor3">
                <div><?php echo __('PERSONAL_INFO') ?></div>
            </div>
            <div class="nkTeamMemberDetailContent">
                <div>
                    <div id="nkTeamMemberInfo" >
                        <div>
                            <div>»&nbsp;<?php echo __('NICK') ?>&nbsp;:</div>
                            <div><img src="images/flags/<?php echo $teamMember['countryImg'] ?>" alt="<?php echo $teamMember['country'] ?>" />&nbsp;<?php echo $author ?></div>
                        </div>
<?php
//if ($teamMemberDetail) :
//    if ($teamMemberDetail['prenom']) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('LAST_NAME') ?>&nbsp;:</div>
                            <div><?php echo $teamMemberDetail['prenom'] ?></div>
                        </div>

<?php
//    endif;

//    if ($teamMemberDetail['age']) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('AGE') ?>&nbsp;:</div>
                            <div><?php echo getMemberAge($teamMemberDetail['age']) ?></div>
                        </div>

<?php
//    endif;

    if ($teamMemberDetail['sexe'] && in_array($teamMemberDetail['sexe'], array('male', 'female'))) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('SEXE') ?>&nbsp;:</div>
                            <div><?php echo __(strtoupper($teamMemberDetail['sexe'])) ?></div>
                        </div>

<?php
    endif;

//    if ($teamMemberDetail['ville']) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('CITY') ?>&nbsp;:</div>
                            <div><?php echo $teamMemberDetail['ville'] ?></div>
                        </div>

<?php
//    endif;
//endif;

//if ($teamMember['country']) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('COUNTRY') ?>&nbsp;:</div>
                            <div><?php echo $teamMember['country'] ?></div>
                        </div>
<?php
//endif;


if ($GLOBALS['visiteur'] >= $GLOBALS['nuked']['user_social_level']) :
    foreach (nkUserSocial_getConfig() as $userSocial) :
        if (isset($teamMember[$userSocial['field']]) && $teamMember[$userSocial['field']] != '') :
?>
                        <div>
                            <div>»&nbsp;<?php echo nkUserSocial_getLabel($userSocial) ?>&nbsp;:</div>
                            <div><a href="<?php echo nkUserSocial_getLinkUrl($userSocial, $teamMember[$userSocial['field']]) ?>"<?php echo nkUserSocial_openUrlPage($userSocial) ?>><?php echo $teamMember[$userSocial['field']] ?></a></div>
                        </div>
<?php
        endif;
    endforeach;
endif;
?>
                    </div>
                    <div id="nkTeamMemberPhoto">
<?php
if ($teamMemberDetail && $teamMemberDetail['photo'] != '') :
?>
                        <img class="nkBgColor1" src="<?php echo checkimg($teamMemberDetail['photo']) ?>" alt="" />
<?php
else :
?>
                        <img class="nkBgColor1" src="modules/Team/images/noAvatar.png" alt="" />
<?php
endif
?>
                    </div>
                </div>
            </div>
        </div>
<?php
//if ($teamMemberDetail) :
?>
        <div id="nkTeamMemberHardwareConfig" class="nkTeamMemberDetailWrapper">
            <div class="nkTeamMemberDetailHead nkBgColor3">
                <div><?php echo __('HARDWARE_CONFIG') ?></div>
            </div>
            <div class="nkTeamMemberDetailContent nkBgColor2">
                <div>

<?php
//    if ($teamMemberDetail['cpu']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('PROCESSOR') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['cpu'] ?></div>
                    </div>
<?php
//    endif;

//    if ($teamMemberDetail['ram']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('MEMORY') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['ram'] ?></div>
                    </div>
<?php
//    endif;

//    if ($teamMemberDetail['motherboard']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('MOTHERBOARD') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['motherboard'] ?></div>
                    </div>
<?php
//    endif;

//    if ($teamMemberDetail['video']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('VIDEOCARD') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['video'] ?></div>
                    </div>
<?php
//    endif;

//    if ($teamMemberDetail['resolution']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('RESOLUTION') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['resolution'] ?></div>
                    </div>
<?php
//    endif;

//    if ($teamMemberDetail['son']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('SOUNDCARD') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['son'] ?></div>
                    </div>
<?php
//    endif;

//    if ($teamMemberDetail['souris']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('MOUSE') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['souris'] ?></div>
                    </div>
<?php
//    endif;

//    if ($teamMemberDetail['clavier']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('KEYBOARD') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['clavier'] ?></div>
                    </div>
<?php
//    endif;

//    if ($teamMemberDetail['ecran']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('MONITOR') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['ecran'] ?></div>
                    </div>
<?php
//    endif;

//    if ($teamMemberDetail['system']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('OS_SYSTEM') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['system'] ?></div>
                    </div>
<?php
//    endif;

//    if ($teamMemberDetail['connexion']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('CONNEXION') ?>&nbsp;:</div>
                        <div><?php echo $teamMemberDetail['connexion'] ?></div>
                    </div>
<?php
//    endif
?>
                </div>
            </div>
        </div>
        <div id="nkTeamMemberGamePref" class="nkTeamMemberDetailWrapper">
            <div class="nkTeamMemberDetailHead nkBgColor3">
                <div><?php echo nkHtmlEntities($game['titre']) ?>&nbsp;:</div>
            </div>
            <div class="nkTeamMemberDetailContent nkBgColor2">
                <div>
<?php
//    if ($gamePref['pref_1']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo nkHtmlEntities($game['pref_1']) ?>&nbsp;:</div>
                        <div><?php echo $gamePref['pref_1'] ?></div>
                    </div>
<?php
//    endif;

//    if ($gamePref['pref_2']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo nkHtmlEntities($game['pref_2']) ?>&nbsp;:</div>
                        <div><?php echo $gamePref['pref_2'] ?></div>
                    </div>
<?php
//    endif;

//    if ($gamePref['pref_3']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo nkHtmlEntities($game['pref_3']) ?>&nbsp;:</div>
                        <div><?php echo $gamePref['pref_3'] ?></div>
                    </div>
<?php
//    endif;

//    if ($gamePref['pref_4']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo nkHtmlEntities($game['pref_4']) ?>&nbsp;:</div>
                        <div><?php echo $gamePref['pref_4'] ?></div>
                    </div>
<?php
//    endif;

//    if ($gamePref['pref_5']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo nkHtmlEntities($game['pref_5']) ?>&nbsp;:</div>
                        <div><?php echo $gamePref['pref_5'] ?></div>
                    </div>
<?php
//    endif
?>
                </div>
            </div>
        </div>
<?php
//endif
?>
    </div>
</div>
<?php
else :
?>
<br />
<div class="nkAlignCenter"><?php echo __('NO_PREFERENCE') ?></div>
<br />
<?php
endif;

if ($GLOBALS['user']) :
?>
<br />
<div class="nkAlignCenter">[ <a href="index.php?file=Userbox&amp;op=post_message&amp;for=<?php echo $teamMember['id'] ?>"><?php echo __('SEND_PM') ?></a> ]</div>
<br />
<?php
endif
?>