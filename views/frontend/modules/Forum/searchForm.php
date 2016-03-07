
<div id="nkForumBreadcrumb">
    <a href="index.php?file=Forum"><strong><?php echo __('FORUM_INDEX') ?></strong></a>&nbsp;->&nbsp;<strong><?php echo __('SEARCH') ?></strong>
</div>
<div class ="nkForumPostHead nkForumSearchCell nkBgColor3">
    <h3><?php echo __('SEARCHING') ?></h3>
</div>
<form method="post" action="index.php?file=Forum&amp;page=search&amp;do=search">
    <div class="nkForumSearchTable">
        <div class="nkForumCatWrapper nkBgColor2">
            <div class="nkForumSearchTableContent">
                <div>
                    <div class="nkForumSearchCat nkBorderColor1">
                        <strong><?php echo __('KEYWORDS') ?></strong>
                    </div>
                    <div class="nkForumSearchContent nkBorderColor1">
                        <div><input type="text" name="query" size="30" /></div>
                        <div><input type="radio" class="checkbox" name="searchtype" value="matchor" /><?php echo __('MATCH_OR') ?></div>
                        <div><input type="radio" class="checkbox" name="searchtype" value="matchand" checked="checked" /><?php echo __('MATCH_AND') ?></div>
                        <div><input type="radio" class="checkbox" name="searchtype" value="matchexact" /><?php echo __('MATCH_EXACT') ?></div>
                    </div>
                </div>
                <div>
                    <div class="nkForumSearchCat nkBorderColor1">
                        <strong><?php echo __('AUTHOR') ?></strong>
                    </div>
                    <div class="nkForumSearchContent nkBorderColor1">
                        <div><input type="text" name="author" id="author" size="30" /></div>
                    </div>
                </div>
                <div>
                    <div class="nkForumSearchCat nkBorderColor1">
                        <strong><?php echo __('FORUM') ?></strong>
                    </div>
                    <div class="nkForumSearchContent nkBorderColor1">
                        <div>
                            <select name="id_forum">
                                <option value=""><?php echo __('ALL') ?></option>

<?php
    $currentCat = 0;

    foreach ($forumList as $forum) :
        if ($currentCat != $forum['catId']) :
?>
                                <option value="cat_<?php echo $forum['catId'] ?>">* <?php echo nkHtmlEntities($forum['catName']) ?></option>
<?php
            $currentCat = $forum['catId'];
        endif;
?>
                                <option value="<?php echo $forum['forumId'] ?>">&nbsp;&nbsp;&nbsp;<?php echo nkHtmlEntities($forum['forumName']) ?></option>
<?php
    endforeach
?>
                            </select>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="nkForumSearchCat nkBorderColor1">
                        <strong><?php echo __('SEARCH_INTO') ?></strong>
                    </div>
                    <div class="nkForumSearchContent nkBorderColor1">
                        <div>
                            <input type="radio" class="checkbox" name="into" value="subject" /><?php echo __('SUBJECTS') ?>&nbsp;
                            <input type="radio" class="checkbox" name="into" value="message" /><?php echo __('MESSAGES') ?>&nbsp;
                            <input type="radio" class="checkbox" name="into" value="all" checked="checked" /><?php echo __('BOTH') ?>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="nkForumSearchCat nkBorderColor1">
                        <strong><?php echo __('NB_ANSWERS') ?></strong>
                    </div>
                    <div class="nkForumSearchContent nkBorderColor1">
                        <div>
                            <input type="radio" class="checkbox" name="limit" value="10" />10 &nbsp;
                            <input type="radio" class="checkbox" name="limit" value="50" checked="checked" />50&nbsp;
                            <input type="radio" class="checkbox" name="limit" value="100" />100
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class ="nkForumPostbutton">
        <input type="submit" value="<?php echo __('SEARCHING') ?>" class="nkButton" />
<?php
    if (initCaptcha()) echo create_captcha();
?>
    </div>
</form>
