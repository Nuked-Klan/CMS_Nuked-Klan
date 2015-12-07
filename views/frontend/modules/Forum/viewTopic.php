
    <div id="nkForumWrapper">
        <div id="nkForumViewInfos">
            <div class="nkForumNavTopics"><?php echo $prev ?><?php echo $next ?></div>
            <a name="top"></a>
            <div>
                <h2><?php echo $dbrCurrentTopic['titre'] ?></h2>
            </div>
        </div>
        <div id="nkForumBreadcrumb">
            <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM ?></strong></a>&nbsp;<?php echo $nav ?>
        </div>
        <div class="nkForumNavPage">
        <?php echo $pagination ?>
        </div><!-- @whitespace
     --><div id="nkForumPostOrReply">
            <div>
<?php
        if ($dbrCurrentForum['level'] == 0 || $visiteur >= $dbrCurrentForum['level'] || $moderator) :
            echo $postNewTopic;

            if ($dbrCurrentTopic['closed'] == 0 || $administrator) :
                echo $replyToTopic;
            endif;
        endif;
?>
            </div>
        </div>

        <div id="img_resize_forum" class="nkForumViewTopic">
            <!-- SONDAGE -->
<?php
    //Poll
    if ($dbrCurrentTopic['sondage'] == 1) :
        $dbrTopicPoll['titre'] = printSecuTags($dbrTopicPoll['titre']);
?>
            <div id="nkForumViewMainPoll" class="nkBorderColor1">
<?php
        if ($user && $userPolled > 0 || $_REQUEST['vote'] == 'view') :
?>
                <div class="nkForumViewPollBg"></div><!-- @whitespace
             --><div class="nkForumViewPoll">
                    <div class="nkForumPollTitle">
                        <h3><?php echo $dbrTopicPoll['titre'] ?></h3>
                    </div>
<?php
            if ($user && $dbrCurrentTopic['auteur_id'] == $user['id'] && $dbrCurrentTopic['closed'] == 0 || $administrator) :
?>
                    <div class="nkForumViewActionLinks">
                        <div class="nkButton-group">
                            <a href="index.php?file=Forum&amp;op=edit_poll&amp;poll_id=<?php echo $dbrTopicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo _EDITPOLL ?>" class="nkButton icon alone edit"></a>
                            <a href="index.php?file=Forum&amp;op=del_poll&amp;poll_id=<?php echo $dbrTopicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo _DELPOLL ?>" class="nkButton icon alone remove danger"></a>
                        </div>
                    </div>
<?php
            endif;

            if (is_file('themes/'. $theme .'/images/bar.gif'))
                $img = 'themes/'. $theme .'/images/bar.gif';
            else
                $img = 'modules/Forum/images/bar.gif';

            $nbVote = array_sum(array_column($dbrTopicPollOptions, 'option_vote'));

            foreach ($dbrTopicPollOptions as $topicPollOptions) :
                $ratio = ($nbVote <> 0) ? ($topicPollOptions['option_vote'] * 100) / $nbVote : 0;
                $width = ($ratio < 1) ? 2 : $ratio * 2;
                $roundedRatio = round($ratio);
?>
                    <div class="nkForumPollOptionsTxt"><?php echo printSecuTags($topicPollOptions['option_text']) ?></div>
                    <div class="nkForumPollImage"><img src="<?php echo $img ?>" width="<?php echo $width ?>" height="10" alt="" title="<?php echo $roundedRatio ?>%" />&nbsp;<?php echo $roundedRatio ?>% (<?php echo $topicPollOptions['option_vote'] ?>)</div>
<?php
            endforeach
?>
                    <div class="nkForumPollStats">
                        <strong><?php echo _TOTALVOTE ?>&nbsp;:</strong><?php echo $nbVote ?>
                    </div>
                </div>
<?php
        else :
?>
                <div class="nkForumViewPollBg"></div><!-- @whitespace
             --><div class="nkForumViewPoll">
                    <form method="post" action="index.php?file=Forum&amp;op=vote&amp;poll_id=<?php echo $dbrTopicPoll['id'] ?>">
                        <div class="nkForumPollTitle">
                            <h3><?php echo $dbrTopicPoll['titre'] ?></h3>
                        </div>
<?php
            if ($user && $dbrCurrentTopic['auteur_id'] == $user['id'] && $dbrCurrentTopic['closed'] == 0 || $administrator) :
?>
                        <div class="nkForumViewActionLinks">
                            <div class="nkButton-group">
                                <a href="index.php?file=Forum&amp;op=edit_poll&amp;poll_id=<?php echo $dbrTopicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo _EDITPOLL ?>" class="nkButton icon alone edit"></a>
                                <a href="index.php?file=Forum&amp;op=del_poll&amp;poll_id=<?php echo $dbrTopicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo _DELPOLL ?>" class="nkButton icon alone remove danger"></a>
                            </div>
                        </div>
<?php
            endif;

            foreach ($dbrTopicPollOptions as $topicPollOptions) :
?>
                        <div class="nkForumPollOptions">
                            <input type="radio" class="checkbox" name="voteid" value="<?php echo $topicPollOptions['id'] ?>" />&nbsp;
                            <span><?php echo printSecuTags($topicPollOptions['option_text']) ?></span>
                        </div>
<?php
            endforeach
?>
                        <div>
                            <input type="hidden" name="forum_id" value="<?php echo $forumId ?>" />
                            <input type="hidden" name="thread_id" value="<?php echo $threadId ?>" />
                        </div>
                        <div id="nkForumPollActionLinks">
                            <input type="submit" class="nkButton" value="<?php echo _TOVOTE ?>" />
                            <input type="button" class="nkButton" value="<?php echo _RESULT ?>" onclick="document.location='index.php?file=Forum&amp;page=viewtopic&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>&amp;vote=view'" />
                        </div>
                    </form>
                </div>
<?php
        endif
?>
            </div>
<?php
    endif
    //Fin Poll
?>
            <div class="nkForumCatWrapper">
                <div id="forum-table" class="nkForumViewContent nkBorderColor1">
<?php
    foreach ($dbrTopicMessages as $topicMessage) :
        $topicMessage = formatTopicMessage($topicMessage, $administrator, $forumId, $threadId);
        $authorInfo = getAuthorInfo($topicMessage, $administrator);

        //$tmpcnt++ % 2 == 1 ? $color = $color1 : $color = $color2;

        // Liens interface utilisateur/administrateurs
        $messageUri = '&amp;forum_id='. $forumId .'&amp;thread_id='. $threadId .'&amp;mess_id='. $topicMessage['id'];
        $quoteLink  = 'index.php?file=Forum&amp;page=post'. $messageUri .'&amp;do=quote';
        $editLink   = 'index.php?file=Forum&amp;page=post'. $messageUri .'&amp;do=edit';
        $deleteLink = 'index.php?file=Forum&amp;op=del'. $messageUri;
?>
                    <div>
                        <div class="nkForumViewUserPseudo nkBgColor3"><h3><?php echo $authorInfo['userInfo'] ?></h3></div>
                        <div class="nkForumViewPostHead nkBgColor3">
                            <a href="#top" title="<?php echo _BACKTOTOP ?>">
                                <img src="modules/Forum/images/interface/top_24.png" class="nkUserButton small" />
                            </a>
                            <a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>&amp;p=<?php echo $p ?>#<?php echo $topicMessage['id'] ?> " title="<?php echo _PERMALINK_TITLE ?>">
                                <img src="modules/Forum/images/interface/permalink_24.png" class="nkUserButton small" />

                            </a>
                        </div>
                    </div>
                    <div>
                        <div class="nkForumViewAuthor nkBgColor2 nkBorderColor1">
                            <a name="<?php echo $topicMessage['id'] ?>"></a>
                            <div class="nkForumUserRankName" <?php echo $authorInfo['rankStyle'] ?>><?php echo $authorInfo['rankName'] ?></div>
                            <div class="nkForumForumRankImage"><?php echo $authorInfo['rankImage'] ?></div>
                            <div class="nkForumUserAvatar"><?php echo $authorInfo['avatar'] ?></div>
                            <div class="nkForumTotalUserPost"><?php echo $authorInfo['totalUserPost'] ?></div>
                            <div class="nkForumDisplayUserIp"><?php echo $authorInfo['displayUserIp'] ?></div>
<?php
        //User Game details
        if ($nuked['forum_gamer_details'] == 'on') :
?>
                            <div class="nkForumGamerDetails">
                                <div class="nkForumUserGameIcon">
                                    <?php echo _FAVORITEGAME ?>&nbsp;:&nbsp;
                                    <img src="<?php echo $authorInfo['gameIcon'] ?>" alt="" title="<?php echo $authorInfo['gameName'] ?>" />
                                </div>
<?php
            if (isset($authorInfo['gameUserPref1'])) :
                echo '<div>'. $authorInfo['gamePref1'] .'&nbsp;:&nbsp;' . $authorInfo['gameUserPref1'] .'</div>';
            endif;

            if (isset($authorInfo['gameUserPref2'])) :
                echo '<div>'. $authorInfo['gamePref2'] .'&nbsp;:&nbsp;' . $authorInfo['gameUserPref2'] .'</div>';
            endif;

            if (isset($authorInfo['gameUserPref3'])) :
                echo '<div>'. $authorInfo['gamePref5'] .'&nbsp;:&nbsp;' . $authorInfo['gameUserPref3'] .'</div>';
            endif;

            if (isset($authorInfo['gameUserPref4'])) :
                echo '<div>'. $authorInfo['gamePref4'] .'&nbsp;:&nbsp;' . $authorInfo['gameUserPref4'] .'</div>';
            endif;

            if (isset($authorInfo['gameUserPref5'])) :
                echo '<div>'. $authorInfo['gamePref5'] .'&nbsp;:&nbsp;' . $authorInfo['gameUserPref5'] .'</div>';
            endif;
?>
                            </div>
<?php
        endif;
        //Fin User Game details
?>
                        </div><!-- fin colonne auteur -->
                        <div class="nkForumViewMessage nkBgColor2 nkBorderColor1">
                            <div class="nkForumViewTitle">
                                <h3><?php echo $topicMessage['titre'] ?></h3>
                            </div><!-- @whitespace
                         --><div class="nkForumViewActionLinks">
                                <div class="nkButton-group">
<?php
        if ($dbrCurrentTopic['closed'] == 0 && $administrator || $visiteur >= $dbrCurrentForum['level']) :
?>
                                    <a href="<?php echo $quoteLink ?>" title="<?php echo _REPLYQUOTE ?>" class="nkButton icon alone chat"></a>
<?php
        endif;

        if ($user && $topicMessage['auteur_id'] == $user['id'] && $dbrCurrentTopic['closed'] == 0 || $administrator) :
?>
                                    <a href="<?php echo $editLink ?>" title="<?php echo _EDITMESSAGE ?>" class="nkButton icon alone edit"></a>
<?php
        endif;

        if ($administrator) :
?>
                                    <a href="<?php echo $deleteLink ?>" title="<?php echo _DELMESSAGE ?>" class="nkButton icon alone remove danger"></a>
<?php
        endif
?>
                                </div>
                            </div>
                            <div class="nkForumViewTxt">
                                <?php echo $topicMessage['txt'] ?>
                            </div>
                            <?php echo $topicMessage['joinedFile'] ?>
<?php
        if ($topicMessage['edition'] != '') :
?>
                            <div class="nkForumEditMessage">
                                <small><?php echo $topicMessage['edition'] ?></small>
                            </div>
<?php
        endif;

        if ($topicMessage['auteur_id'] != '' && $authorInfo['signature'] != '' && $topicMessage['usersig'] == 1) :
?>
                            <div class="nkForumViewSig nkBorderColor1">
                                <?php echo $authorInfo['signature'] ?>
                            </div>
<?php
        endif;
?>
                        </div>
                    </div>
                    <div class="nkForumViewPostLegend nkBgColor2">
                        <div class="nkForumUserSocialLinks nkBorderColor1">
                            <div class="nkButton-group">
<?php
        if ($authorInfo['status'] == 'registered' && $topicMessage['auteur_id'] != '') :
            echo '<a class="nkButton icon user small alone" href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($authorInfo['name']) .'" title="'. _SEEPROFIL .'"></a>';

            if ($user) :
                echo '<a class="nkButton icon pm small alone" href="index.php?file=Userbox&amp;op=post_message&amp;for='. $topicMessage['auteur_id'] .'" title="'. _SENDPM .'"></a>';
            endif;
        endif;

        if ($topicMessage['auteur_id'] != '' && $user['id'] >= $nuked['user_social_level']) :
            if ($nuked['user_email'] == 'on' && $authorInfo['email'] != '') :
                echo '<a class="nkButton icon email small alone" href="mailto:'. $authorInfo['email'] .'" title="'. _SENDEMAIL .'"></a>';
            endif;

            if ($nuked['user_website'] == 'on' && $authorInfo['homepage'] != '') :
                echo '<a class="nkButton icon website small alone" href="'. $authorInfo['homepage'] .'" onclick="window.open(this.href); return false;" title="'. _SEEHOMEPAGE .'&nbsp;'. $authorInfo['name'] .'"></a>';
            endif;

            if ($nuked['user_icq'] == 'on' && $authorInfo['icq'] != '') :
                echo '<a class="nkButton icon icq small alone" href="http://web.icq.com/whitepages/add_me?uin='. $authorInfo['icq'] .'&amp;action=add" title="'. $authorInfo['icq'] .'"></a>';
            endif;

            if ($nuked['user_msn'] == 'on' && $authorInfo['msn'] != '') :
                echo '<a class="nkButton icon msn small alone" href="mailto:'. $authorInfo['msn']  .'" title="'. $authorInfo['msn']  .'"></a>';
            endif;

            if ($nuked['user_aim'] == 'on' && $authorInfo['aim'] != '') :
                echo '<a class="nkButton icon aim small alone" href="aim:goim?screenname='. $authorInfo['aim'] .'&amp;message=Hi+'. $authorInfo['aim'] .'+Are+you+there+?" onclick="window.open(this.href); return false;" title="'. $authorInfo['aim'] .'"></a>';
            endif;

            if ($nuked['user_yim'] == 'on' && $authorInfo['yim'] != '') :
                echo '<a class="nkButton icon yim small alone" href="http://edit.yahoo.com/config/send_webmesg?target='. $authorInfo['yim'] .'&amp;src=pg" title="'. $authorInfo['yim'] .'"></a>';
            endif;

            if ($nuked['user_xfire'] == 'on' && $authorInfo['xfire'] != '') :
                echo '<a class="nkButton icon xfire small alone" href="xfire:add_friend?user='. $authorInfo['xfire'] .'" title="'. $authorInfo['xfire'] .'"></a>';
            endif;

            if ($nuked['user_facebook'] == 'on' && $authorInfo['facebook'] != '') :
                echo '<a class="nkButton icon facebook small alone" href="http://www.facebook.com/'. $authorInfo['facebook'] .'" title="'. $authorInfo['facebook'] .'"></a>';
            endif;

            if ($nuked['user_origin'] == 'on' && $authorInfo['origin'] != '') :
                echo '<a class="nkButton icon origin small alone" href="#" title="'. $authorInfo['origin'] .'"></a>';
            endif;

            if ($nuked['user_steam'] == 'on' && $authorInfo['steam'] != '') :
                echo '<a class="nkButton icon steam small alone" href="http://steamcommunity.com/actions/AddFriend/'. $authorInfo['steam'] .'" title="'. $authorInfo['steam'] .'"></a>';
            endif;

            if ($nuked['user_twitter'] == 'on' && $authorInfo['twitter'] != '') :
                echo '<a class="nkButton icon twitter small alone" href="http://twitter.com/#!/'. $authorInfo['twitter'] .'" title="'. $authorInfo['twitter'] .'"></a>';
            endif;

            if ($nuked['user_skype'] == 'on' && $authorInfo['skype'] != '') :
                echo '<a class="nkButton icon skype small alone" href="skype:'. $authorInfo['skype'] .'?call" title="'. $authorInfo['skype'] .'"></a>';
            endif;
        endif;
?>
                            </div>
                        </div>
                        <div class="nkForumPostInfos nkBorderColor1">
                            <div class="nkForumPostDate">
                                <?php echo _POSTEDON ?>&nbsp;<?php echo formatForumMessageDate($topicMessage['date']) ?>
                            </div><!-- @whitespace
                         --><div class="nkForumPermalinks">
                            </div>
                        </div>
                    </div>
<?php
    endforeach
?>
               </div>
            </div>
        </div>
<?php
    if ($user['id'] != '') :
        if ($notify > 0)
            $mailNotification = '<a href="index.php?file=Forum&amp;op=notify&amp;do=off&amp;forum_id=' . $forumId . '&amp;thread_id=' . $threadId . '">' . _NOTIFYOFF . '</a>';
        else
            $mailNotification = '<a href="index.php?file=Forum&amp;op=notify&amp;do=on&amp;forum_id=' . $forumId . '&amp;thread_id=' . $threadId . '">' . _NOTIFYON . '</a>';
    endif;
?>
        <div class="nkForumNavPage">
        <?php echo $pagination ?>
        </div><!-- @whitespace
     --><div id="nkForumUserActionLink"><?php echo $mailNotification ?></div>
        <div id="nkForumPostNewTopic">
            <div>
<?php
        if ($dbrCurrentForum['level'] == 0 || $visiteur >= $dbrCurrentForum['level'] || $moderator) :
            echo $postNewTopic;

            if ($dbrCurrentTopic['closed'] == 0 || $administrator) :
                echo $replyToTopic;
            endif;
        endif;
?>
            </div>
        </div>

<?php
        // Liens d'administration
        if ($administrator) :
            $threadUri = '&amp;forum_id='. $forumId .'&amp;thread_id='. $threadId;
            $delLink    = 'index.php?file=Forum&amp;op=del_topic'. $threadUri;
            $moveLink   = 'index.php?file=Forum&amp;op=move'. $threadUri;

            $closeLink = 'index.php?file=Forum&amp;op=lock'. $threadUri;

            if ($dbrCurrentTopic['closed'] == 1) {
                $closeTitle = _TOPICUNLOCK;
                $closeClass = 'unlock';
                $closeLink .= '&amp;do=open';
            }
            else {
                $closeTitle = _TOPICLOCK;
                $closeClass = 'lock';
                $closeLink .= '&amp;do=close';
            }

            $pinedLink = 'index.php?file=Forum&amp;op=announce'. $threadUri;

            if ($dbrCurrentTopic['annonce'] == 1) {
                $pinedTitle = _TOPICDOWN;
                $pinedClass = 'arrowdown';
                $pinedLink .= '&amp;do=down';
            }
            else {
                $pinedTitle = _TOPICUP;
                $pinedClass = 'arrowup';
                $pinedLink .= '&amp;do=up';
            }

?>
        <div id="nkForumAdminLinks">
            <div class="nkButton-group">
                <a href="<?php echo $delLink ?>" title="<?php echo _TOPICDEL ?>" class="nkButton icon alone remove danger"></a>
                <a href="<?php echo $moveLink ?>" title="<?php echo _TOPICMOVE ?>" class="nkButton icon alone move"></a>
                <a href="<?php echo $closeLink ?>" title="<?php echo $closeTitle ?>" class="nkButton icon alone <?php echo $closeClass ?>"></a>
                <a href="<?php echo $pinedLink ?>" title="<?php echo $pinedTitle ?>" class="nkButton icon alone <?php echo $pinedClass ?>"></a>
            </div>
        </div>
<?php
        endif;

        //Redimensionnement automatique des images du Forum
        echo '<script type="text/javascript">
            <!--
            MaxWidth = document.getElementById("nkForumWrapper").offsetWidth - 300;
            var Img = document.getElementById("img_resize_forum").getElementsByTagName("img");
            var NbrImg = Img.length;
            for(var i = 0; i < NbrImg; i++){
                if (Img[i].width > MaxWidth){
                    Img[i].style.height = Img[i].height * MaxWidth / Img[i].width+"px";
                    Img[i].style.width = MaxWidth+"px";
                }
            }
        -->
        </script>';
    //Fin de l'affichage du viewtopic
?>
    </div>