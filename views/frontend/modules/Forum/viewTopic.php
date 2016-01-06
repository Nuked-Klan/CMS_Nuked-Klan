
    <div id="nkForumWrapper">
        <div id="nkForumViewInfos">
            <div class="nkForumNavTopics"><?php echo $prev ?><?php echo $next ?></div>
            <a name="top"></a>
            <div>
                <h2><?php echo $currentTopic['titre'] ?></h2>
            </div>
        </div>
        <div id="nkForumBreadcrumb">
            <?php echo $breadcrumb ?>
        </div>
        <div class="nkForumNavPage">
        <?php echo $pagination ?>
        </div><!-- @whitespace
     --><div id="nkForumPostOrReply">
            <div>
<?php
    if ($forumWriteLevel) :
        //Boutons d'action utilisateur, remplacement automatique du bouton CSS par une image PNG si elle éxiste.
        if ((is_file('themes/'. $theme .'/images/newthread.png')))
            $postNewTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id='. $forumId .'"><img style="border: 0;" src="themes/'. $theme .'/images/newthread.png" alt="" title="'. _NEWSTOPIC .'" /></a>';
        else
            $postNewTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id='. $forumId .'" class="nkButton icon add">'. _NEWTOPIC .'</a>';

        echo $postNewTopic;

        if ($currentTopic['closed'] == 0 || $administrator) :
            if ((is_file('themes/'. $theme .'/images/reply.png')))
                $replyToTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id='. $forumId .'&amp;thread_id='. $threadId .'"><img style="border: 0;" src="themes/'. $theme .'/images/reply.png" alt="" title="'. _REPLY .'" /></a>';
            else
                $replyToTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id='. $forumId .'&amp;thread_id='. $threadId .'" class="nkButton icon chat">'. _REPLY .'</a>';

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
    if ($currentTopic['sondage'] == 1 && $topicPoll && $topicPollOptions) :
        $topicPoll['title'] = printSecuTags($topicPoll['title']);
?>
            <div id="nkForumViewMainPoll" class="nkBorderColor1">
<?php
        if ($user && $userPolled > 0 || (isset($_GET['vote']) && $_GET['vote'] == 'view')) :
?>
                <div class="nkForumViewPollBg"></div><!-- @whitespace
             --><div class="nkForumViewPoll">
                    <div class="nkForumPollTitle">
                        <h3><?php echo $topicPoll['title'] ?></h3>
                    </div>
<?php
            if ($user && $currentTopic['auteur_id'] == $user['id'] && $currentTopic['closed'] == 0 || $administrator) :
?>
                    <div class="nkForumViewActionLinks">
                        <div class="nkButton-group">
                            <a href="index.php?file=Forum&amp;op=editPoll&amp;poll_id=<?php echo $topicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo _EDITPOLL ?>" class="nkButton icon alone edit"></a>
                            <a href="index.php?file=Forum&amp;op=deletePoll&amp;poll_id=<?php echo $topicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo _DELPOLL ?>" class="nkButton icon alone remove danger"></a>
                        </div>
                    </div>
<?php
            endif;

            if (is_file('themes/'. $theme .'/images/bar.gif'))
                $img = 'themes/'. $theme .'/images/bar.gif';
            else
                $img = 'modules/Forum/images/bar.gif';

            $nbVote = array_sum(array_column($topicPollOptions, 'option_vote'));

            foreach ($topicPollOptions as $topicPollOptions) :
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
                    <div id="nkForumPollActionLinks">
                        <a class="nkButton" href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>"><?php echo __('TO_VOTE') ?></a>
                    </div>
                </div>
<?php
        else :
?>
                <div class="nkForumViewPollBg"></div><!-- @whitespace
             --><div class="nkForumViewPoll">
                    <form method="post" action="index.php?file=Forum&amp;op=vote&amp;poll_id=<?php echo $topicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>">
                        <div class="nkForumPollTitle">
                            <h3><?php echo $topicPoll['title'] ?></h3>
                        </div>
<?php
            if ($user && $currentTopic['auteur_id'] == $user['id'] && $currentTopic['closed'] == 0 || $administrator) :
?>
                        <div class="nkForumViewActionLinks">
                            <div class="nkButton-group">
                                <a href="index.php?file=Forum&amp;op=editPoll&amp;poll_id=<?php echo $topicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo _EDITPOLL ?>" class="nkButton icon alone edit"></a>
                                <a href="index.php?file=Forum&amp;op=deletePoll&amp;poll_id=<?php echo $topicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo _DELPOLL ?>" class="nkButton icon alone remove danger"></a>
                            </div>
                        </div>
<?php
            endif;

            foreach ($topicPollOptionsList as $topicPollOption) :
                if ($topicPollOption['option_text'] != '') :
?>
                        <div class="nkForumPollOptions">
                            <input id="voteId<?php echo $topicPollOption['id'] ?>" type="radio" class="checkbox" name="voteid" value="<?php echo $topicPollOption['id'] ?>" />&nbsp;
                            <label for="voteId<?php echo $topicPollOption['id'] ?>"><?php echo printSecuTags($topicPollOption['option_text']) ?></label>
                        </div>
<?php
                endif;
            endforeach;
?>
                        <div id="nkForumPollActionLinks">
                            <input type="submit" class="nkButton" value="<?php echo __('TO_VOTE') ?>" />
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
    foreach ($topicMessagesList as $topicMessage) :
        $topicMessage   = formatTopicMessage($topicMessage);
        $authorInfo     = getAuthorInfo($topicMessage);

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
<?php
        if (isset($authorInfo['rankImage'])) :
?>
                            <div class="nkForumForumRankImage"><?php echo $authorInfo['rankImage'] ?></div>
<?php
        endif
?>
                            <div class="nkForumUserAvatar"><?php echo $authorInfo['avatar'] ?></div>
<?php
        if ($authorInfo['status'] == 'registered') :
?>
                            <div class="nkForumTotalUserPost"><?php echo $authorInfo['totalUserPost'] ?></div>
<?php
        endif;

        if (isset($authorInfo['displayUserIp'])) :
?>
                            <div class="nkForumDisplayUserIp"><?php echo $authorInfo['displayUserIp'] ?></div>
<?php
        endif;

        //User Game details
        if ($authorInfo['status'] == 'registered' && $nuked['forum_gamer_details'] == 'on') :
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
        if ($currentTopic['closed'] == 0 && $administrator || $visiteur >= $currentForum['level']) :
?>
                                    <a href="<?php echo $quoteLink ?>" title="<?php echo _REPLYQUOTE ?>" class="nkButton icon alone chat"></a>
<?php
        endif;

        if ($user && $topicMessage['auteur_id'] == $user['id'] && $currentTopic['closed'] == 0 || $administrator) :
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
<?php
        if ($topicMessage['file'] != '' && is_file($fileUrl = 'upload/Forum/'. $topicMessage['file'])) :
            $roundedFilesize = ceil((int) filesize($fileUrl) / 1024);
?>
                            <div class="nkForumViewAttachedFile"><strong><a href="<?php echo $fileUrl ?>" onclick="window.open(this.href); return false;" title="<?php echo _DOWNLOADFILE ?>"><?php echo $topicMessage['file'] ?></a> (<?php echo $roundedFilesize ?> Ko)<?php

            if ($user && $topicMessage['auteur_id'] == $user['id'] || $administrator) :

                ?>&nbsp;<a href="index.php?file=Forum&amp;op=del_file&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>&amp;mess_id=<?php echo $topicMessage['id'] ?>" class="nkButton icon trash danger"><?php echo _DELFILE ?></a><?php

            endif;

                            ?></strong></div>
<?php
        endif;

        if ($topicMessage['edition'] != '') :
?>
                            <div class="nkForumEditMessage">
                                <small><?php echo $topicMessage['edition'] ?></small>
                            </div>
<?php
        endif;

        if ($topicMessage['auteur_id'] != '' && $topicMessage['signature'] != '' && $topicMessage['usersig'] == 1) :
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

        if ($user && $topicMessage['auteur_id'] != '' && $user['id'] >= $nuked['user_social_level']) :
            if ($nuked['user_email'] == 'on' && $topicMessage['email'] != '') :
                echo '<a class="nkButton icon email small alone" href="mailto:'. $topicMessage['email'] .'" title="'. _SENDEMAIL .'"></a>';
            endif;

            if ($nuked['user_website'] == 'on' && $topicMessage['homepage'] != '') :
                echo '<a class="nkButton icon website small alone" href="'. $topicMessage['homepage'] .'" onclick="window.open(this.href); return false;" title="'. _SEEHOMEPAGE .'&nbsp;'. $topicMessage['name'] .'"></a>';
            endif;

            if ($nuked['user_icq'] == 'on' && $topicMessage['icq'] != '') :
                echo '<a class="nkButton icon icq small alone" href="http://web.icq.com/whitepages/add_me?uin='. $topicMessage['icq'] .'&amp;action=add" title="'. $topicMessage['icq'] .'"></a>';
            endif;

            if ($nuked['user_msn'] == 'on' && $topicMessage['msn'] != '') :
                echo '<a class="nkButton icon msn small alone" href="mailto:'. $topicMessage['msn']  .'" title="'. $topicMessage['msn']  .'"></a>';
            endif;

            if ($nuked['user_aim'] == 'on' && $topicMessage['aim'] != '') :
                echo '<a class="nkButton icon aim small alone" href="aim:goim?screenname='. $topicMessage['aim'] .'&amp;message=Hi+'. $topicMessage['aim'] .'+Are+you+there+?" onclick="window.open(this.href); return false;" title="'. $topicMessage['aim'] .'"></a>';
            endif;

            if ($nuked['user_yim'] == 'on' && $topicMessage['yim'] != '') :
                echo '<a class="nkButton icon yim small alone" href="http://edit.yahoo.com/config/send_webmesg?target='. $topicMessage['yim'] .'&amp;src=pg" title="'. $topicMessage['yim'] .'"></a>';
            endif;

            if ($nuked['user_xfire'] == 'on' && $topicMessage['xfire'] != '') :
                echo '<a class="nkButton icon xfire small alone" href="xfire:add_friend?user='. $topicMessage['xfire'] .'" title="'. $topicMessage['xfire'] .'"></a>';
            endif;

            if ($nuked['user_facebook'] == 'on' && $topicMessage['facebook'] != '') :
                echo '<a class="nkButton icon facebook small alone" href="http://www.facebook.com/'. $topicMessage['facebook'] .'" title="'. $topicMessage['facebook'] .'"></a>';
            endif;

            if ($nuked['user_origin'] == 'on' && $topicMessage['origin'] != '') :
                echo '<a class="nkButton icon origin small alone" href="#" title="'. $topicMessage['origin'] .'"></a>';
            endif;

            if ($nuked['user_steam'] == 'on' && $topicMessage['steam'] != '') :
                echo '<a class="nkButton icon steam small alone" href="http://steamcommunity.com/actions/AddFriend/'. $topicMessage['steam'] .'" title="'. $topicMessage['steam'] .'"></a>';
            endif;

            if ($nuked['user_twitter'] == 'on' && $topicMessage['twitter'] != '') :
                echo '<a class="nkButton icon twitter small alone" href="http://twitter.com/#!/'. $topicMessage['twitter'] .'" title="'. $topicMessage['twitter'] .'"></a>';
            endif;

            if ($nuked['user_skype'] == 'on' && $topicMessage['skype'] != '') :
                echo '<a class="nkButton icon skype small alone" href="skype:'. $topicMessage['skype'] .'?call" title="'. $topicMessage['skype'] .'"></a>';
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
    $mailNotification = '';

    if ($user && $user['id'] != '') :
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
        if ($forumWriteLevel) :
            echo $postNewTopic;

            if ($currentTopic['closed'] == 0 || $administrator) :
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

            if ($currentTopic['closed'] == 1) {
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

            if ($currentTopic['annonce'] == 1) {
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
