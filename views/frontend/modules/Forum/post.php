    <div id="nkForumWrapper">
        <form method="post" action="<?php echo $action ?>" enctype="multipart/form-data">
            <div id="nkForumBreadcrumb">
                <?php echo $breadcrumb ?>
            </div>
            <div class ="nkForumPostHead">
                <h3><?php echo $actionName ?></h3>
            </div>
            <div class="nkForumPost">
                <div class="nkForumCatWrapper">
                    <div class="nkForumPostContent">

                        <div><!--Pseudo -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _PSEUDO ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">
<?php
    if ($do == 'edit') :
        echo $author;
    elseif ($user && $user['name'] != '') :
?>
                                <?php echo $user['name'] ?>&nbsp;<a href="index.php?file=User&amp;op=logout" class="nkButton icon remove danger"><?php echo _FLOGOUT ?></a>
<?php
    endif;

    if ($author != '') :
?>
                                <input type="hidden" name="author" value="<?php echo $author ?>" />
<?php
    else :
?>
                                <input type="text" name="author" size="35" maxlength="35" />
                                <a href="index.php?file=User&amp;op=login_screen" class="nkButton icon user"><?php echo _FLOGIN ?></a>
<?php
    endif;
?>
                            </div>
                        </div>

                        <div><!--Title -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _TITLE ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">
                                <input id="forum_titre" type="text" size="70"  maxlength="70" name="titre" value="<?php echo $postTitle ?>" />
                            </div>
                        </div>

                        <div><!--Message -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1" style="vertical-align:top;">
                                <strong><?php echo _MESSAGE ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">
                                <textarea id="e_advanced" name="texte" cols="70" rows="15"><?php echo $postText ?></textarea>
                            </div>
                        </div>
                        <div><!--Options -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _OPTIONS ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">
<?php
    if ($visiteur > 0) :
?>
                                <input id="forum_sign" type="checkbox" class="checkbox" name="usersig" value="1" <?php echo $usersigChecked ?> />&nbsp;<?php echo _USERSIGN ?><br />
                                <input type="checkbox" class="checkbox" name="emailnotify" value="1" <?php echo $emailnotifyChecked ?> />&nbsp;<?php echo _EMAILNOTIFY ?><br />
<?php
    endif;

    if ($do == 'edit') :
        if ($force_edit_message == 'on' && ! $moderator) :
?>
                                <input type="hidden" name="edit_text" value="1" />
<?php
        else :
?>
                                <input type="checkbox" name="edit_text" value="1" checked="checked" />&nbsp;<?php echo _EDITTEXT ?>
<?php
        endif;
    endif;

    if ($do == 'post') :
        if ($administrator) :
?>
                                <input type="checkbox" class="checkbox" name="annonce" value="1"<?php echo $announceChecked ?> />&nbsp;<?php echo _ANNONCE ?><br />
<?php
        endif;
    else :
        echo '<br />';
    endif;
?>
                            </div>
                        </div>
<?php
    if ($visiteur >= $pollLevel && $do == 'post') :
?>
                        <div><!--Sondage -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _SURVEY ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">  
                                <input type="checkbox" class="checkbox" name="survey" value="1" />&nbsp;<?php echo _POSTSURVEY ?><br />
                                <input type="text" name="survey_field" size="2" value="4" />&nbsp;<?php echo _SURVEYFIELD ?>&nbsp;(<?php echo _MAX ?> : <?php echo $nuked['forum_field_max'] ?>)
                            </div>
                        </div>
<?php
    endif;

    if ($visiteur >= $nuked['forum_file_level'] && $nuked['forum_file'] == 'on' && $nuked['forum_file_maxsize'] > 0 && $do != 'edit') {
        if ($nuked['forum_file_maxsize'] >= 1000) {
            $max_size = $nuked['forum_file_maxsize'] * 1000;
            $maxfile = $nuked['forum_file_maxsize'] / 1000;
            $maxfilesize = $maxfile .'&nbsp;'. _MO;
        }
        else {
            $maxfilesize = $nuked['forum_file_maxsize'] .'&nbsp;'. _KO;
        }

?>
                        <div><!--attachedFile -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _ATTACHFILE ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">
                                <input type="file" name="fichiernom" size="30" />&nbsp;(<?php echo _MAXFILESIZE ?> : <?php echo $maxfilesize ?>)
                                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_size ?>" />
                            </div>
                        </div>
<?php
    }
?>
                    </div>
                </div>
            </div>
            <div class ="nkForumPostbutton">
                <input type="submit" value="<?php echo _SEND ?>" class="nkButton" />
                <input type="hidden" name="token" value="<?php echo $token ?>" />
                <input type="hidden" name="forum_id" value="<?php echo $forumId ?>" />
<?php
    if ($threadId > 0) :
?>
                <input type="hidden" name="thread_id" value="<?php echo $threadId ?>" />
<?php
    endif;

    if ($messId > 0) :
?>
                <input type="hidden" name="mess_id" value="<?php echo $messId ?>" />
<?php
    endif
?>
            </div>
<?php
    if ($GLOBALS['captcha'] === true) echo create_captcha();
?>
        </form>
<?php
    if ($do == 'reply' || $do == 'quote') :
?>
            <div class ="nkForumPostHead">
                <h3><?php echo _PREVIOUSMESSAGES; ?></h3>
            </div>
            <div class="nkForumPostReview nkBgColor2 nkBorderColor1">
                <div class="nkForumCatWrapper">
                    <div class="nkForumPostReviewContent">
<?php
        foreach ($dbrLastMessageList as $lastMessage) :
            //$tmpcnt++ % 2 == 1 ? $color = $color1 : $color = $color2;
?>
                        <div>
                            <div class="nkForumPostReviewAuthor">
                                <strong><?php echo nk_CSS($lastMessage['auteur']) ?></strong>
                            </div>
                            <div class="nkForumPostReviewMessage">
                                <div>
                                    <img src="images/posticon.gif" alt="" />
                                    <?php echo _POSTEDON ?>&nbsp;<?php echo nkDate($lastMessage['date']) ?>
                                </div>
                                <p><?php echo $lastMessage['txt'] ?></p>
                            </div>
                        </div>
<?php
        endforeach
?>
                    </div>
                </div>
            </div>
<?php
    endif
?>
    </div>
