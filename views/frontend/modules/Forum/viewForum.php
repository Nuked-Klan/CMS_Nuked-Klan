
    <div id="nkForumWrapper">
        <div id="nkForumInfos">
<?php
    if ($nuked['forum_image'] == 'on' && $dbrForum['image'] != '') :
?>
            <img src="<?php echo $dbrForum['image'] ?>" alt="" />
<?php
    endif
?>
            <div>
                <h2><?php echo $dbrForum['forumName'] ?></h2>
                <p><?php echo $dbrForum['comment'] ?></p>
                <div class="nkForumModos"><small><?php echo _MODO ?> : <?php echo $moderatorList ?></small></div>
            </div>
        </div>
        <div id="nkForumBreadcrumb">
            <?php echo $breadcrumb ?>
        </div>

<?php
    if ($dbrForum['forumLevel'] == 0 || $visiteur >= $dbrForum['forumLevel'] || $moderator) :
?>
        <div id="nkForumPostNewTopic">
            <a class="nkButton icon add" href="index.php?file=Forum&amp;page=post&amp;forum_id=<?php echo $forumId ?>"><?php echo _NEWTOPIC ?></a>
        </div>
<?php
    endif
?>
        <div class="nkForumNavPage">
<?php echo $pagination ?>
        </div><!-- @whitespace
     --><div id="nkForumUserActionLink">
<?php
    if ($user) :
?>
                <a id="nkForumMarkRead" href="index.php?file=Forum&amp;op=mark&amp;forum_id=<?php echo $forumId ?>"><?php echo _MARKSUBJECTREAD ?></a>
<?php
    endif
?>
        </div>
        <div class="nkForumCat">
            <div class="nkForumCatWrapper">
                <div class="nkForumCatHead nkBgColor3">
                    <div>
                        <div class="nkForumBlankCell"></div>
                        <div class="nkForumForumCell"><?php echo _SUBJECTS ?></div>
                        <div class="nkForumStatsCell"><?php echo _STATS ?></div>
                        <div class="nkForumDateCell"><?php echo _LASTPOST ?></div>
                    </div>
                </div>
                <div class="nkForumCatContent nkBgColor2">
<?php
    if (count($dbrForumthread) == 0) :
?>
                    <div>
                        <div class="nkForumIconCell nkBorderColor1"></div>
                        <div class="nkForumForumCell nkBorderColor1"><?php echo _NOPOSTFORUM; ?></div>
                        <div class="nkForumStatsCell nkBorderColor1"></div>
                        <div class="nkForumDateCell nkBorderColor1"></div>
                    </div>
<?php
    endif;

    foreach ($dbrForumthread as $forumthread) :
        $threadData = formatTopicRow($forumthread, $forumId);
?>
                    <div>
                        <div class="nkForumIconCell nkBorderColor1">
                            <span class="nkForumTopicIcon <?php echo $threadData['topicIcon'] ?>"></span>
                        </div>
                        <div class="nkForumForumCell nkBorderColor1">
                                <h3><?php echo $threadData['topicTitle'] ?></h3>
                            <div>
                                <span>
                                    <?php echo _CREATEDBY ?>
                                    <?php echo $threadData['createdBy'] ?>
                                    <?php echo _THE ?>&nbsp;<?php echo nkdate($forumthread['date']) . $threadData['topicPagination'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="nkForumStatsCell nkBorderColor1">
                            <strong><?php echo $threadData['nbReply'] ?></strong>&nbsp;<?php echo strtolower(_ANSWERS) ?>
                            <br/>
                            <strong><?php echo $forumthread['view'] ?></strong>&nbsp;<?php echo strtolower(_VIEWS) ?>
                        </div>
                        <div class="nkForumDateCell nkBorderColor1">
                            <div class="nkForumAuthorAvatar">
                                <img src="<?php echo $threadData['lastMsgAuthorAvatar'] ?>" alt="" />
                            </div>
                            <div>
                                <p>
                                    <span><?php echo _BY; ?></span>
                                    <strong><?php echo $threadData['lastMsgAuthor'] ?>&nbsp;<?php echo $threadData['lastMsgLink'] ?></strong>
                                </p>
                                <p><?php echo $threadData['lastMsgDate'] ?></p>
                            </div>
                        </div>
                    </div>
<?php
    endforeach
?>
                </div>
            </div>
        </div>
        <div class="nkForumNavPage">
<?php echo $pagination ?>
        </div><!-- @whitespace
     --><div id="nkForumUserActionLink">
<?php
    if ($user) :
?>
                <a id="nkForumMarkRead" href="index.php?file=Forum&amp;op=mark&amp;forum_id=<?php echo $forumId ?>"><?php echo _MARKSUBJECTREAD ?></a>
<?php
    endif
?>
        </div>
<?php
        if ($dbrForum['forumLevel'] == 0 || $visiteur >= $dbrForum['forumLevel'] || $moderator) :
?>
        <div id="nkForumPostNewTopic">
            <a class="nkButton icon add" href="index.php?file=Forum&amp;page=post&amp;forum_id=<?php echo $forumId ?>"><?php echo _NEWTOPIC ?></a>
        </div>
<?php
        endif
?>
        <div class="nkForumViewLegend">
            <div class="nkForumNewTopicLegend"><?php echo _POSTNEW ?></div>
            <div class="nkForumNoNewTopicLegend"><?php echo _NOPOSTNEW ?></div>
            <div class="nkForumNewTopicLockLegend"><?php echo _POSTNEWCLOSE ?></div>
        </div>
        <div class="nkForumViewLegend">
            <div class="nkForumTopicLockLegend"><?php echo _SUBJECTCLOSE ?></div>
            <div class="nkForumNewTopicPopularLegend"><?php echo _POSTNEWHOT ?></div>
            <div class="nkForumTopicPopularLegend"><?php echo _NOPOSTNEWHOT ?></div>
        </div>
        <div class="nkForumQuickShotcuts"><!-- Shortcuts -->
            <form method="post" action="index.php?file=Forum&amp;page=viewforum">
                <div class="nkForumSelectTopics">
                    <?php echo _JUMPTO ?> : 
                    <select name="forum_id" onchange="submit();">
                        <option value=""><?php echo _SELECTFORUM ?></option>
<?php
    foreach ($forumList as $id => $name) :
?>
                        <option value="<?php echo $id ?>"><?php echo $name ?></option>
<?php
    endforeach
?>
                    </select>
                </div>
            </form>
            <form method="post" action="index.php?file=Forum&amp;page=viewforum&amp;forum_id=<?php echo $forumId ?>">
                <div class="nkForumSelectDate">
                    <?php echo _SEETHETOPIC ?> : 
                    <select name="date_max" onchange="submit();">
                        <option><?php echo _THEFIRST ?></option>
                        <option value="86400"><?php echo _ONEDAY ?></option>
                        <option value="604800"><?php echo _ONEWEEK ?></option>
                        <option value="2592000"><?php echo _ONEMONTH ?></option>
                        <option value="15552000"><?php echo _SIXMONTH ?></option>
                        <option value="31104000"><?php echo _ONEYEAR ?></option>
                    </select>
                </div>
            </form>
        </div>
    </div>
