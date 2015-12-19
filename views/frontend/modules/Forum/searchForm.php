
<div id="nkForumBreadcrumb">
    <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM ?></strong></a>&nbsp;->&nbsp;<strong><?php echo _SEARCH ?></strong>
</div>
<div class ="nkForumPostHead nkForumSearchCell nkBgColor3">
    <h3><?php echo _SEARCHING ?></h3>
</div>
<form method="post" action="index.php?file=Forum&amp;page=search&amp;do=search">
    <div class="nkForumSearchTable">
        <div class="nkForumCatWrapper nkBgColor2">
            <div class="nkForumSearchTableContent">
                <div>
                    <div class="nkForumSearchCat nkBorderColor1">
                        <strong><?php echo _KEYWORDS ?></strong>
                    </div>
                    <div class="nkForumSearchContent nkBorderColor1">
                        <div><input type="text" name="query" size="30" /></div>
                        <div><input type="radio" class="checkbox" name="searchtype" value="matchor" /><?php echo _MATCHOR ?></div>
                        <div><input type="radio" class="checkbox" name="searchtype" value="matchand" checked="checked" /><?php echo _MATCHAND ?></div>
                        <div><input type="radio" class="checkbox" name="searchtype" value="matchexact" /><?php echo _MATCHEXACT ?></div>
                    </div>
                </div>
                <div>
                    <div class="nkForumSearchCat nkBorderColor1">
                        <strong><?php echo _AUTHOR ?></strong>
                    </div>
                    <div class="nkForumSearchContent nkBorderColor1">
                        <div><input type="text" name="author" id="author" size="30" /></div>
                    </div>
                </div>
                <div>
                    <div class="nkForumSearchCat nkBorderColor1">
                        <strong><?php echo _FORUM ?></strong>
                    </div>
                    <div class="nkForumSearchContent nkBorderColor1">
                        <div>
                            <select name="id_forum">
                                <option value=""><?php echo _ALL ?></option>

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
                        <strong><?php echo _SEARCHINTO ?></strong>
                    </div>
                    <div class="nkForumSearchContent nkBorderColor1">
                        <div>
                            <input type="radio" class="checkbox" name="into" value="subject" /><?php echo _SUBJECTS ?>&nbsp;
                            <input type="radio" class="checkbox" name="into" value="message" /><?php echo _MESSAGES ?>&nbsp;
                            <input type="radio" class="checkbox" name="into" value="all" checked="checked" /><?php echo _BOTH ?>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="nkForumSearchCat nkBorderColor1">
                        <strong><?php echo _NBANSWERS ?></strong>
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
        <input type="submit" value="<?php echo _SEARCHING ?>" class="nkButton" />
<?php
    if (initCaptcha()) echo create_captcha();
?>
    </div>
</form>
