
<?php
if ($GLOBALS['visiteur'] == 9) :
?>
<div class="nkModuleAdminButton nkAlignRight"><a href="index.php?file=Admin&amp;page=user&amp;op=edit_user&amp;id_user=<?php echo $member['id'] ?>"><img class="nkNoBorder" src="images/edition.gif" alt="" title="<?php echo __('EDIT') ?>" /></a><?php

    if ($member['id'] != $GLOBALS['user']['id']) : ?>

    <script type="text/javascript">
    // <![CDATA[
    function deleteMember(nickname, id) {
        var confirmTxt = '<?php echo addslashes(__('CONFIRM_TO_DELETE_DATA')) ?>';

        if (confirm(confirmTxt.replace(/%s/, nickname)))
            document.location.href = 'index.php?file=Admin&page=user&op=del_user&id_user=' + id;
    }
    // ]]>
    </script>
    <a href="javascript:deleteMember('<?php echo addslashes($author)) ?>', '<?php echo $member['id'] ?>');"><img class="nkNoBorder" src="images/delete.gif" alt="" title="<?php echo __('DELETE') ?>" /></a><?php

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
    <param name="flashvars" value="text=<?php echo flashTextCleaning($author) ?>" />
</object>
<div id="nkMemberWrapper">
    <div class="nkMemberDetail">
        <div id="nkMemberPersonalInfo" class="nkMemberDetailWrapper nkBgColor2">
            <div class="nkMemberDetailHead nkBgColor3">
                <div><?php echo __('PERSONAL_INFO') ?></div>
            </div>
            <div class="nkMemberDetailContent">
                <div>
                    <div id="nkMemberInfo" >
                        <div>
                            <div>»&nbsp;<?php echo __('NICK') ?>&nbsp;:</div>
                            <div><img src="images/flags/<?php echo $member['countryImg'] ?>" alt="<?php echo $member['country'] ?>" />&nbsp;<?php echo $author ?></div>
                        </div>
<?php
if ($memberDetail) :
    if ($memberDetail['prenom']) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('LAST_NAME') ?>&nbsp;:</div>
                            <div><?php echo $memberDetail['prenom'] ?></div>
                        </div>

<?php
    endif;

    if ($memberDetail['age']) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('AGE') ?>&nbsp;:</div>
                            <div><?php echo getMemberAge($memberDetail['age']) ?></div>
                        </div>

<?php
    endif;

    if ($memberDetail['sexe'] && in_array($memberDetail['sexe'], array('male', 'female'))) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('SEXE') ?>&nbsp;:</div>
                            <div><?php echo __(strtoupper($memberDetail['sexe'])) ?></div>
                        </div>

<?php
    endif;

    if ($memberDetail['ville']) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('CITY') ?>&nbsp;:</div>
                            <div><?php echo $memberDetail['ville'] ?></div>
                        </div>

<?php
    endif;
endif;

if ($member['country']) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('COUNTRY') ?>&nbsp;:</div>
                            <div><?php echo $member['country'] ?></div>
                        </div>
<?php
endif;


if ($GLOBALS['visiteur'] >= $GLOBALS['nuked']['user_social_level']) :
    foreach (nkUserSocial_getConfig() as $userSocial) :
        if (isset($member[$userSocial['field']]) && $member[$userSocial['field']] != '') :
?>
                        <div>
                            <div>»&nbsp;<?php echo nkUserSocial_getLabel($userSocial) ?>&nbsp;:</div>
                            <div><a href="<?php echo nkUserSocial_getLinkUrl($userSocial, $member[$userSocial['field']]) ?>"<?php echo nkUserSocial_openUrlPage($userSocial) ?>><?php echo $member[$userSocial['field']] ?></a></div>
                        </div>
<?php
        endif;
    endforeach;
endif;

if ($member['date']) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('ARRIVAL_DATE') ?>&nbsp;:</div>
                            <div><?php echo nkDate($member['date']) ?></div>
                        </div>
<?php
endif;

if (isset($member['last_used']) && $member['last_used'] > 0) :
?>
                        <div>
                            <div>»&nbsp;<?php echo __('LAST_VISIT') ?>&nbsp;:</div>
                            <div><?php echo nkDate($member['last_used']) ?></div>
                        </div>
<?php
endif
?>
                    </div>
                    <div id="nkMemberPhoto">
<?php
if ($memberDetail && $memberDetail['photo'] != '') :
?>
                        <img class="nkBgColor1 nkBorderColor3" src="<?php echo checkimg($memberDetail['photo']) ?>" style="overflow: auto; max-width: 100px; width: expression(this.scrollWidth >= 100? '100px' : 'auto');" alt="" />
<?php
else :
?>
                        <img class="nkBgColor1 nkBorderColor3" src="modules/Members/images/noAvatar.png" style="width:100px" alt="" />
<?php
endif
?>
                    </div>
                </div>
            </div>
        </div>

<?php
if ($memberDetail) :
?>
        <div id="nkMemberHardwareConfig" class="nkMemberDetailWrapper">
            <div class="nkMemberDetailHead nkBgColor3">
                <div><?php echo __('HARDWARE_CONFIG') ?></div>
            </div>
            <div class="nkMemberDetailContent nkBgColor2">
                <div>

<?php
    if ($memberDetail['cpu']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('PROCESSOR') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['cpu'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['ram']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('MEMORY') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['ram'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['motherboard']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('MOTHERBOARD') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['motherboard'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['video']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('VIDEOCARD') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['video'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['resolution']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('RESOLUTION') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['resolution'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['son']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('SOUNDCARD') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['son'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['souris']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('MOUSE') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['souris'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['clavier']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('KEYBOARD') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['clavier'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['ecran']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('MONITOR') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['ecran'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['system']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('OS_SYSTEM') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['system'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['connexion']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo __('CONNEXION') ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['connexion'] ?></div>
                    </div>
<?php
    endif
?>
                </div>
            </div>
        </div>
        <div id="nkMemberGamePref" class="nkMemberDetailWrapper">
            <div class="nkMemberDetailHead nkBgColor3">
                <div><?php echo nkHtmlEntities($game['titre']) ?>&nbsp;:</div>
            </div>
            <div class="nkMemberDetailContent nkBgColor2">
                <div>
<?php
    if ($memberDetail['pref_1']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo nkHtmlEntities($game['pref_1']) ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['pref_1'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['pref_2']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo nkHtmlEntities($game['pref_2']) ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['pref_2'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['pref_3']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo nkHtmlEntities($game['pref_3']) ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['pref_3'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['pref_4']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo nkHtmlEntities($game['pref_4']) ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['pref_4'] ?></div>
                    </div>
<?php
    endif;

    if ($memberDetail['pref_5']) :
?>
                    <div>
                        <div>»&nbsp;<?php echo nkHtmlEntities($game['pref_5']) ?>&nbsp;:</div>
                        <div><?php echo $memberDetail['pref_5'] ?></div>
                    </div>
<?php
    endif
?>
                </div>
            </div>
        </div>
<?php
endif
?>
    </div>
</div>
<div class="nkAlignCenter"><?php

if ($GLOBALS['user']) :

    ?>&nbsp;[&nbsp;<a href="index.php?file=Userbox&amp;op=post_message&amp;for=<?php echo $member['id'] ?>"><?php echo __('SEND_PM') ?></a>&nbsp;]&nbsp;<?php

endif

    ?>&nbsp;[&nbsp;<a href="index.php?file=Search&amp;op=mod_search&amp;autor=<?php echo $author ?>"><?php echo __('FIND_STUFF') ?></a>&nbsp;]&nbsp;
</div>
