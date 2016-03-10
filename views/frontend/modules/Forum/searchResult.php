
<div id="nkForumWrapper">
    <div id="nkForumInfos">
        <div>
            <h2><?php echo __('FSEARCH_RESULT') ?></h2>
            <p><?php echo $nbResult ?>&nbsp;<?php echo __('FSEARCH_FOUND') ?>&nbsp;<strong><?php echo $cleanedQuery ?></strong></p>
        </div>
    </div>
    <div id="nkForumBreadcrumb">
        <a href="index.php?file=Forum"><strong><?php echo __('FORUM_INDEX') ?></strong></a>&nbsp;->&nbsp;<a href="index.php?file=Forum&amp;page=search"><strong><?php echo __('SEARCH') ?></strong></a>
    </div>
<?php echo $pagination ?>
    <div class="nkForumCat">
        <div class="nkForumCatWrapper">
            <div class="nkForumCatHead nkBgColor3">
                <div>
                    <div class="nkForumSearchCell"><?php echo __('FORUMS') ?></div>
                    <div class="nkForumSearchCell"><?php echo __('SUBJECTS') ?></div>
                    <div class="nkForumSearchCell"><?php echo __('AUTHOR') ?></div>
                    <div class="nkForumSearchCell"><?php echo __('DATE') ?></div>
                </div>
            </div>
            <div class="nkForumCatContent nkBgColor2">

<?php
    if ($nbResult > 0) :
        nkDB_dataSeek($result, $start);

        for ($i = 0; $i < $limit; $i++) {
            if ($forumMsg = nkDB_fetchRow($result)) {
                $forumMsg = prepareForumSearchResultRow($forumMsg);
?>
                <div>
                    <div class="nkForumSearchForumCell nkBorderColor1">
                        <a href="index.php?file=Forum&amp;page=viewforum&amp;forum_id=<?php echo $forumMsg['forum_id'] ?>">
                            <strong><?php echo $forumMsg['forumName'] ?></strong>
                        </a>
                    </div>
                    <div class="nkForumSearchTopicCell nkBorderColor1">
                        <a href="<?php echo $forumMsg['url'] ?>" onmouseover="AffBulle('<?php echo $forumMsg['titre'] ?>', '<?php echo $forumMsg['cleanedText'] ?>', 320)" onmouseout="HideBulle()">
                            <b><?php echo $forumMsg['cleanedTitle'] ?></b>
                        </a>
                    </div>
                    <div class="nkForumSearchAuthorCell nkBorderColor1"><?php echo $forumMsg['author'] ?></div>
                    <div class="nkForumSearchDateCell nkBorderColor1"><?php echo $forumMsg['date'] ?></div>
                </div>
<?php
            }
        }

    else :
?>
                <div>
                    <div class="nkForumSearchForumCell nkBorderColor1"></div>
                    <div class="nkForumSearchTopicCell nkBorderColor1">
<?php
        if ($query != '') :
            echo __('FNO_SEARCH_FOUND') .' <strong><i>'. $cleanedQuery .'</i></strong>';
        elseif ($authorSought != '') :
            echo __('FNO_SEARCH_FOUND') .' <strong><i>'. printSecutags($authorSought) .'</i></strong>';
        elseif ($dateMax > 0) :
            echo __('FNO_LAST_VISIT_MESSAGE');
        else :
            echo __('FNO_SEARCH_RESULT');
        endif
?>
                    </div>
                    <div class="nkForumSearchAuthorCell nkBorderColor1"></div>
                    <div class="nkForumSearchDateCell nkBorderColor1"></div>
                </div>
<?php
    endif
?>
            </div>
        </div>
    </div>
<?php echo $pagination ?>
    <div class="nkForumSearch">
        <form method="get" action="index.php" >
            <label for="forumSearch"><?php echo __('SEARCH') ?> :</label>
            <input id="forumSearch" type="text" name="query" size="25" />
            <input type="hidden" name="file" value="Forum" />
            <input type="hidden" name="page" value="search" />
            <input type="hidden" name="do" value="search" />
            <input type="hidden" name="into" value="all" />
            <input type="submit" value="<?php echo __('SEND') ?>" class="nkButton"/>
        </form>
    </div>
</div>
