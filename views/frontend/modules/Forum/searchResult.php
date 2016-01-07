
<div id="nkForumWrapper">
    <div id="nkForumInfos">
        <div>
            <h2><?php echo _FSEARCHRESULT ?></h2>
            <p><?php echo $nbResult ?>&nbsp;<?php echo _FSEARCHFOUND ?>&nbsp;<strong><?php echo $cleanedQuery ?></strong></p>
        </div>
    </div>
    <div id="nkForumBreadcrumb">
        <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM ?></strong></a>&nbsp;->&nbsp;<a href="index.php?file=Forum&amp;page=search"><strong><?php echo _SEARCH ?></strong></a>
    </div>
<?php echo $pagination ?>
    <div class="nkForumCat">
        <div class="nkForumCatWrapper">
            <div class="nkForumCatHead nkBgColor3">
                <div>
                    <div class="nkForumSearchCell"><?php echo _FORUMS ?></div>
                    <div class="nkForumSearchCell"><?php echo _SUBJECTS ?></div>
                    <div class="nkForumSearchCell"><?php echo _AUTHOR ?></div>
                    <div class="nkForumSearchCell"><?php echo _DATE ?></div>
                </div>
            </div>
            <div class="nkForumCatContent nkBgColor2">

<?php
    if ($nbResult > 0) :
        mysql_data_seek($result, $start);

        for ($i = 0; $i < $limit; $i++) {
            if ($forumMsg = mysql_fetch_row($result)) {
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
            echo _FNOSEARCHFOUND .' <strong><i>'. $cleanedQuery .'</i></strong>';
        else if ($authorSought != '') :
            echo _FNOSEARCHFOUND .' <strong><i>'. printSecutags($authorSought) .'</i></strong>';
        else if ($dateMax > 0) :
            echo _FNOLASTVISITMESS;
        else :
            echo _FNOSEARCHRESULT;
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
            <label for="forumSearch"><?php echo _SEARCH ?> :</label>
            <input id="forumSearch" type="text" name="query" size="25" />
            <input type="hidden" name="file" value="Forum" />
            <input type="hidden" name="page" value="search" />
            <input type="hidden" name="do" value="search" />
            <input type="hidden" name="into" value="all" />
            <input type="submit" value="<?php echo __('SEND') ?>" class="nkButton"/>
        </form>
    </div>
</div>
