
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
            $postNewTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id='. $forumId .'"><img style="border: 0;" src="themes/'. $theme .'/images/newthread.png" alt="" title="'. __('NEW_TOPIC') .'" /></a>';
        else
            $postNewTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id='. $forumId .'" class="nkButton icon add">'. __('NEW_TOPIC') .'</a>';

        echo $postNewTopic;

        if ($currentTopic['closed'] == 0 || $administrator) :
            if ((is_file('themes/'. $theme .'/images/reply.png')))
                $replyToTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id='. $forumId .'&amp;thread_id='. $threadId .'"><img style="border: 0;" src="themes/'. $theme .'/images/reply.png" alt="" title="'. __('REPLY') .'" /></a>';
            else
                $replyToTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id='. $forumId .'&amp;thread_id='. $threadId .'" class="nkButton icon chat">'. __('REPLY') .'</a>';

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
    if ($currentTopic['sondage'] == 1 && $topicPoll && $topicPollOptionsList) :
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
                            <a href="index.php?file=Forum&amp;page=poll&amp;op=edit&amp;id=<?php echo $topicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo __('EDIT_POLL') ?>" class="nkButton icon alone edit"></a>
                            <a href="index.php?file=Forum&amp;page=poll&amp;op=delete&amp;id=<?php echo $topicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo __('DELETE_POLL') ?>" class="nkButton icon alone remove danger"></a>
                        </div>
                    </div>
<?php
            endif;

            if (is_file('themes/'. $theme .'/images/bar.gif'))
                $img = 'themes/'. $theme .'/images/bar.gif';
            else
                $img = 'modules/Forum/images/bar.gif';

            $nbVote = array_sum(array_column($topicPollOptionsList, 'option_vote'));

            foreach ($topicPollOptionsList as $topicPollOptions) :
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
                        <strong><?php echo __('TOTAL_VOTE') ?>&nbsp;:</strong><?php echo $nbVote ?>
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
                    <form method="post" action="index.php?file=Forum&amp;page=poll&amp;op=vote&amp;id=<?php echo $topicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>">
                        <div class="nkForumPollTitle">
                            <h3><?php echo $topicPoll['title'] ?></h3>
                        </div>
<?php
            if ($user && $currentTopic['auteur_id'] == $user['id'] && $currentTopic['closed'] == 0 || $administrator) :
?>
                        <div class="nkForumViewActionLinks">
                            <div class="nkButton-group">
                                <a href="index.php?file=Forum&amp;page=poll&amp;op=edit&amp;id=<?php echo $topicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo __('EDIT_POLL') ?>" class="nkButton icon alone edit"></a>
                                <a href="index.php?file=Forum&amp;page=poll&amp;op=delete&amp;id=<?php echo $topicPoll['id'] ?>&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" title="<?php echo __('DELETE_POLL') ?>" class="nkButton icon alone remove danger"></a>
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
                            <input type="button" class="nkButton" value="<?php echo __('RESULT') ?>" onclick="document.location='index.php?file=Forum&amp;page=viewtopic&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>&amp;vote=view'" />
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
        $deleteLink = 'index.php?file=Forum&amp;page=post&amp;op=delete'. $messageUri;
?>
                    <div>
                        <div class="nkForumViewUserPseudo nkBgColor3"><h3><?php echo $authorInfo['userInfo'] ?></h3></div>
                        <div class="nkForumViewPostHead nkBgColor3">
                            <a href="#top" title="<?php echo __('BACK_TO_TOP') ?>">
                                <img src="modules/Forum/images/interface/top_24.png" class="nkUserButton small" />
                            </a>
                            <a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>&amp;p=<?php echo $p ?>#<?php echo $topicMessage['id'] ?> " title="<?php echo __('PERMALINK_TITLE') ?>">
                                <img src="modules/Forum/images/interface/permalink_24.png" class="nkUserButton small" />

                            </a>
                        </div>
                    </div>
                    <div>
                        <div class="nkForumViewAuthor nkBgColor2 nkBorderColor1">
                            <a name="<?php echo $topicMessage['id'] ?>"></a>
                            <div class="nkForumUserRankName" <?php echo $authorInfo['rankStyle'] ?>><?php echo $authorInfo['rankName'] ?></div>
<?php
        if ($authorInfo['rankImage'] != '') :
?>
                            <div class="nkForumForumRankImage"><img src="<?php echo $authorInfo['rankImage'] ?>" alt="" /></div>
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
                                    <?php echo __('FAVORITE_GAME') ?>&nbsp;:&nbsp;
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
                                    <a href="<?php echo $quoteLink ?>" title="<?php echo __('REPLY_QUOTE') ?>" class="nkButton icon alone chat"></a>
<?php
        endif;

        if ($user && $topicMessage['auteur_id'] == $user['id'] && $currentTopic['closed'] == 0 || $administrator) :
?>
                                    <a href="<?php echo $editLink ?>" title="<?php echo __('EDIT_MESSAGE') ?>" class="nkButton icon alone edit"></a>
<?php
        endif;

        if ($administrator) :
?>
                                    <a href="<?php echo $deleteLink ?>" title="<?php echo __('DELETE_MESSAGE') ?>" class="nkButton icon alone remove danger"></a>
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
                            <div class="nkForumViewAttachedFile"><strong><a href="<?php echo $fileUrl ?>" onclick="window.open(this.href); return false;" title="<?php echo __('DOWNLOAD_FILE') ?>"><?php echo $topicMessage['file'] ?></a> (<?php echo $roundedFilesize ?> Ko)<?php

            if ($user && $topicMessage['auteur_id'] == $user['id'] || $administrator) :

                ?>&nbsp;<a href="index.php?file=Forum&amp;op=del_file&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>&amp;mess_id=<?php echo $topicMessage['id'] ?>" class="nkButton icon trash danger"><?php echo __('DELETE_FILE') ?></a><?php

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
            echo '<a class="nkButton icon user small alone" href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($authorInfo['name']) .'" title="'. __('SEE_PROFIL') .'"></a>';

            if ($user) :
                echo '<a class="nkButton icon pm small alone" href="index.php?file=Userbox&amp;op=post_message&amp;for='. $topicMessage['auteur_id'] .'" title="'. __('SEND_PM') .'"></a>';
            endif;
        endif;

        if ($topicMessage['auteur_id'] != '' && $visiteur >= $nuked['user_social_level'])
            echo nkUserSocial_getButtonList($topicMessage, 'authorName');
?>
                            </div>
                        </div>
                        <div class="nkForumPostInfos nkBorderColor1">
                            <div class="nkForumPostDate">
                                <?php echo __('POSTED_ON') ?>&nbsp;<?php echo formatForumMessageDate($topicMessage['date']) ?>
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
            $mailNotification = '<a href="index.php?file=Forum&amp;op=notify&amp;do=off&amp;forum_id=' . $forumId . '&amp;thread_id=' . $threadId . '">' . __('NOTIFY_OFF') . '</a>';
        else
            $mailNotification = '<a href="index.php?file=Forum&amp;op=notify&amp;do=on&amp;forum_id=' . $forumId . '&amp;thread_id=' . $threadId . '">' . __('NOTIFY_ON') . '</a>';
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
                $closeTitle = __('TOPIC_UNLOCK');
                $closeClass = 'unlock';
                $closeLink .= '&amp;do=open';
            }
            else {
                $closeTitle = __('TOPIC_LOCK');
                $closeClass = 'lock';
                $closeLink .= '&amp;do=close';
            }

            $pinedLink = 'index.php?file=Forum&amp;op=announce'. $threadUri;

            if ($currentTopic['annonce'] == 1) {
                $pinedTitle = __('TOPIC_DOWN');
                $pinedClass = 'arrowdown';
                $pinedLink .= '&amp;do=down';
            }
            else {
                $pinedTitle = __('TOPIC_UP');
                $pinedClass = 'arrowup';
                $pinedLink .= '&amp;do=up';
            }

?>
        <div id="nkForumAdminLinks">
            <div class="nkButton-group">
                <a href="<?php echo $delLink ?>" title="<?php echo __('TOPIC_DELETE') ?>" class="nkButton icon alone remove danger"></a>
                <a href="<?php echo $moveLink ?>" title="<?php echo __('TOPIC_MOVE') ?>" class="nkButton icon alone move"></a>
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
