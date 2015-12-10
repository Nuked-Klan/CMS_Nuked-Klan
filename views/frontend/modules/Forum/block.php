<?php
    if ($active == 3 || $active == 4) :
?>
<table style="background: <?php echo $bgcolor3 ?>;" border="0" width="100%" cellspacing="1" cellpadding="4">
    <tr style="background: <?php echo $bgcolor3 ?>;">
        <td style="width: 35%;" align="center"><b><?php echo _SUBJECTS ?></b></td>
        <td style="width: 15%;" align="center"><b><?php echo _AUTHOR ?></b></td>
        <td style="width: 10%;" align="center"><b><?php echo _ANSWERS ?></b></td>
        <td style="width: 10%;" align="center"><b><?php echo _VIEWS ?></b></td>
        <td style="width: 30%;" align="center"><b><?php echo _LASTPOST ?></b></td>
    </tr>
<?php
    foreach ($forumThreadList as $forumThread) :
        $dbrLastForumMessage = getLastForumMessageData('thread_id', $forumThread['id'],
            'FM.id, FM.date, FM.auteur, U.pseudo'
        );

        $dbrLastForumMessage['date'] = nkDate($dbrLastForumMessage['date']);

        $title = printSecuTags($forumThread['titre']);
        $title = str_ireplace(array('&amp;lt;', '&amp;gt;'), array('&lt;', '&gt;'), $title);
        $title = nk_CSS($title);

        $author = nkNickname($dbrLastForumMessage);

        // TODO : Remove this SQL query
        if ($forumThread['auteur_id'] != '')
            $dbrUser = nkDB_selectOne(
                'SELECT pseudo
                FROM '. USER_TABLE .'
                WHERE id = '. $forumThread['auteur_id']
            );

            if ($dbrUser && $dbrUser['pseudo'] != '')
                $initiat = '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($dbrUser['pseudo']) .'">'. $dbrUser['pseudo'] .'</a>';
            else
                $initiat = nk_CSS($forumThread['auteur']);
        }
        else
            $initiat = nk_CSS($forumThread['auteur']);

        $threadUrl = 'index.php?file=Forum&amp;page=viewtopic&amp;forum_id='. $forumThread['forum_id'] .'&amp;thread_id='. $forumThread['id'];

        $titleLength = strlen($forumThread['titre']);

        if ($titleLength > 20 && $file == $nuked['index_site'])
            $titre_topic = '<a href="'. $threadUrl .'" title="'. $title .'"><b>'. printSecuTags(substr($forumThread['titre'], 0, 20)) .'...</b></a>';
        else if ($titleLength > 30)
            $titre_topic = '<a href="'. $threadUrl .'" title="'. $title .'"><b>'. printSecuTags(substr($forumThread['titre'], 0, 30)) .'...</b></a>';
        else
            $titre_topic = '<a href="'. $threadUrl .'"><b>'. $title .'</b></a>';

        list($postUrl) = getForumMessageUrl($forumThread['forum_id'], $forumThread['id'], $dbrLastForumMessage['id'], $forumThread['nbReplies'] + 1);
?>
    <tr style="background: <?php echo $bgcolor2 ?>;">
        <td style="width: 35%;">&nbsp;<?php echo $titre_topic ?></td>
        <td style="width: 15%;" align="center"><b><?php echo $initiat ?></b></td>
        <td style="width: 10%;" align="center"><?php echo $forumThread['nbReplies'] ?></td>
        <td style="width: 10%;" align="center"><?php echo $forumThread['view'] ?></td>
        <td style="width: 30%;" align="center"><?php echo $dbrLastForumMessage['date'] ?><br /><a href="<?php echo $postUrl ?>"><img style="border: 0;" src="modules/Forum/images/icon_latest_reply.png" alt="" title="<?php echo _SEELASTPOST ?>" /></a><b><?php echo $author ?></b></td>
    </tr>
<?php
    endforeach
?>
</table>
<div style="text-align: right;">&#187; <a href="index.php?file=Forum"><small><?php echo _VISITFORUMS ?></small></a></div>
<?php
    else :
?>
<table width="100%" cellspacing="5" cellpadding="0" border="0">
<?php
    foreach ($forumThreadList as $forumThread) :
        $dbrLastForumMessage = getLastForumMessageData('thread_id', $forumThread['id'],
            'FM.id, FM.auteur, U.pseudo'
        );

        $date = nkDate($dbrForumThread['last_post']);

        $title = printSecuTags($dbrForumThread['titre']);
        $title = nk_CSS($title);

        $author = nkNickname($dbrLastForumMessage, false);

        list($postUrl) = getForumMessageUrl($forumThread['forum_id'], $forumThread['id'], $dbrLastForumMessage['id'], $forumThread['nbReplies'] + 1);

        if (strlen($dbrForumThread['titre']) > 40)
            $titre_topic = '<a href="'. $postUrl .'" title="'. $title .' ( '. $author .' )"><b>'. printSecuTags(substr($dbrForumThread['titre'], 0, 40)) .'...</b></a>';
        else
            $titre_topic = '<a href="'. $postUrl .'" title="'. _BY .'&nbsp;'. $author .'"><b>'. $title .'</b></a>';

?>

    <tr>
        <td><img src="images/posticon.gif" alt="" title="<?php echo $date ?>" />&nbsp;<?php echo $titre_topic ?></td>
    </tr>
<?php
    endforeach
?>
</table>
<div style="text-align: right;">&#187; <a href="index.php?file=Forum"><small><?php echo _VISITFORUMS ?></small></a></div>&nbsp;
<?php
    endif
?>