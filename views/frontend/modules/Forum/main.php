
        <!-- ENTETE DU MAIN -->
        <div id="nkForumWrapper">
            <div id="nkForumHeader">
                <h1>Forums <?php echo $forumTitle ?></h1>
                <p><?php echo $forumDesc ?></p>
            </div><!-- Hack inline-block
            --><div id="nkForumMainSearch">
                <form method="get" action="index.php" >
                    <label for="forumSearch"><?php echo _SEARCH ?> :</label>
                    <input id="forumSearch" type="text" name="query" size="25" />
                    <p>[ <a href="index.php?file=Forum&amp;page=search"><?php echo _ADVANCEDSEARCH ?></a> ]</p>
                    <input type="hidden" name="file" value="Forum" />
                    <input type="hidden" name="page" value="search" />
                    <input type="hidden" name="do" value="search" />
                    <input type="hidden" name="into" value="all" />
                </form>
            </div>
            <div class="nkForumMainBreadcrumb">
                <?php echo $breadcrumb ?>
            </div><!-- Hack inline-block
            --><div id="nkForumMainDates">
                <span><?php echo _DAYIS ?> : <?php echo nkDate(time()) ?></span><?php

    if ($user && $user['lastUsed'] != '') :

        ?>&nbsp;<span><?php echo _LASTVISIT ?> : <?php echo nkDate($user['lastUsed']) ?></span>
<?php
    endif
?>
            </div>
            <!-- CONTENU DES FORUMS -->
<?php
    // Init current Forum category
    $currentCat = 0;

    foreach ($forumList as $forum) :
        // Display next Forum category title
        if ($forum['cat'] != $currentCat) :
            // Close current Forum category container before display Forum list
            if ($currentCat != 0) :
?>
                        </div>
                    </div>

<?php
            endif;

            $forum['catName'] = printSecuTags($forum['catName']);
?>
                <div class="nkForumCat">
                    <div class="nkForumCatNameCell">
<?php
            if ($nuked['forum_cat_image'] == 'on' && $forum['catImage'] != '') :
?>
                        <a href="index.php?file=Forum&amp;cat=<?php echo $forum['cat'] ?>">
                            <img src="<?php echo $forum['catImage'] ?>" title="<?php echo $forum['catName'] ?>" class="nkForumCatImage"/>
                        </a>
<?php
            else :
?>
                        <h2><a href="index.php?file=Forum&amp;cat=<?php echo $forum['cat'] ?>"><?php echo $forum['catName'] ?></a></h2>
<?php
            endif
?>
                    </div>
                    <div class="nkForumCatWrapper">
                        <div class="nkForumCatHead nkBgColor3">
                            <div>
                                <div class="nkForumBlankCell"></div>
                                <div class="nkForumForumCell"><?php echo __('FORUM') ?></div>
                                <div class="nkForumStatsCell"><?php echo _STATS ?></div>
                                <div class="nkForumDateCell"><?php echo _LASTPOST ?></div>
                            </div>
                        </div>
<?php
            $currentCat = $forum['cat'];
        endif;

        $forum = formatForumRow($forum);
?>
                        <div class="nkForumCatContent nkBgColor2">
                            <div>
                                <div class="nkForumIconCell nkBorderColor1">
                                    <img src="<?php echo $forum['readStatus'] ?>" alt="" />
                                </div>
                                <div id="nkForumNameCell_" class="nkForumNameCell nkBorderColor1"><?php

        if ($nuked['forum_image'] == 'on' && $forum['forumImage'] != '') :

?><img src="<?php echo $forum['forumImage'] ?>" class="nkForumNameCellImage" alt="" title="<?php echo $forum['forumName'] ?>" /><?php

        endif
?>
                                    <h3><a href="index.php?file=Forum&amp;page=viewforum&amp;forum_id=<?php echo $forum['id'] ?>"><?php echo $forum['forumName'] ?></a></h3>
                                    <p><?php echo $forum['comment'] ?></p>
<?php
        if ($nuked['forum_display_modos'] == 'on') :
?>
                                    <p><small><?php echo $forum['moderatorsList'] ?></small></p>
<?php
        endif
?>
                                </div>
                                <div class="nkForumStatsCell nkBorderColor1">
                                    <strong><?php echo $forum['nbTopics'] ?></strong>&nbsp;<?php echo strtolower(_TOPICS) ?><br/>
                                    <strong><?php echo $forum['nbMessages'] ?></strong>&nbsp;<?php echo strtolower(__('MESSAGES')) ?>
                                </div>
                                <div class="nkForumDateCell nkBorderColor1">
<?php
        if ($forum['nbMessages'] > 0 && $forum['lastMessage']) :
?>
                                    <div class="nkForumAuthorAvatar">
                                        <img src="<?php echo $forum['lastMessage']['authorAvatar'] ?>" alt="IMG" />
                                    </div>
                                    <div>
                                        <p><a href="<?php echo $forum['lastMessage']['url'] ?>" title="<?php echo $forum['lastMessage']['title'] ?>"><img style="border: 0;" src="modules/Forum/images/icon_latest_reply.png" class="nkForumAlignImg" alt="" title="<?php echo _SEELASTPOST ?>" />&nbsp;<?php echo $forum['lastMessage']['cleanedTitle'] ?></a></p>
                                        <p>
                                            <span><?php echo _BY ?></span>
                                            <strong><?php echo $forum['lastMessage']['author'] ?></strong>
                                        </p>
                                        <p><?php echo $forum['lastMessage']['date'] ?></p>
                                    </div>
<?php
        else :
?>
                                    <?php echo _NOPOST ?>
<?php
        endif
?>
                                </div>
                            </div>
                        </div>
<?php
    endforeach
?>
                    </div>
                </div>
                <!-- LEGENDE DU MAIN -->
                <div id="nkForumWhoIsOnline" class="nkBgColor2">
                    <div class="nkForumWhoIsOnlineTitle nkBgColor3">
                        <h3><?php echo _FWHOISONLINE ?></h3>
                    </div>
                    <div id="nkForumWhoIsOnlineIcon" class="nkBorderColor1"></div>
                    <div id="nkForumWhoIsOnlineContent" class="nkBorderColor1">
                        <p><?php echo _TOTAL_MEMBERS_POSTS ?><strong><?php echo $nbTotalMessages ?></strong>&nbsp;<?php echo strtolower(__('MESSAGES')) ?>.</p>
                        <p><?php echo _WE_HAVE ?><strong><?php echo $nbTotalUsers ?></strong><?php echo _REGISTERED_MEMBERS ?></p>
                        <p><?php echo _LAST_USER_IS ?><a href="index.php?file=Members&amp;op=detail&amp;autor=<?php echo urlencode($lastUser) ?>"><?php echo $lastUser ?></a></p>
                        <p>
                            <?php echo _THEREARE ?>&nbsp;<?php echo $onlineStats[0] ?>&nbsp;<?php echo _FVISITORS ?>, <?php echo $onlineStats[1] ?>&nbsp;<?php echo _FMEMBERS ?>&nbsp;<?php echo _AND ?>&nbsp;<?php echo $onlineStats[2] ?>&nbsp;<?php echo _FADMINISTRATORS ?>&nbsp;<?php echo _ONLINE ?><br />
                            <?php echo _MEMBERSONLINE ?>&nbsp;:&nbsp;<?php echo (! empty($onlineList)) ? implode(',', $onlineList) : '<em>'. _NONE .'</em>' ?>
                        </p>
<?php
    if ($nuked['forum_user_details'] == 'on') :
?>
                        <br />
                        <p><?php echo _RANKLEGEND ?>&nbsp;:&nbsp;<?php echo $teamRankList ?></p>
<?php
    endif;

    if ($nuked['forum_birthday'] == 'on') :
?>
                        <p><?php echo _TODAY ?>,&nbsp;<?php echo $birthdayMessage ?></p>
<?php
    endif
?>
                    </div>
                </div>
                <div class="nkForumNavPage"></div><!-- @whitespace
             --><div id="nkForumUserActionLink">
<?php
    if ($user) :
?>
                    <a id="nkForumMarkRead" href="index.php?file=Forum&amp;op=mark"><?php echo _MARKREAD ?></a>
<?php
        if ($user && $user['lastUsed'] != '') :
?>
                    <a id="nkForumViewUnread" href="index.php?file=Forum&amp;page=search&amp;do=search&amp;date_max=<?php echo $user['lastUsed'] ?>"><?php echo _VIEWLASTVISITMESS ?></a>
<?php
        endif;
    endif;
?>
                </div>
                <div id="nkForumReadLegend">
                    <div>
                        <img src="modules/Forum/images/forum_new.png" alt="NEW" />
                        <span><?php echo _NEWSPOSTLASTVISIT ?></span>
                    </div>
                    <div>
                        <img src="modules/Forum/images/forum.png" alt="" />
                        <span><?php echo _NOPOSTLASTVISIT ?></span>
                    </div>
                </div>
        </div>
        <!-- FIN DU MAIN -->
