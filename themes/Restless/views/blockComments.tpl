<article>
    <header>
        <h1 class="RL_modTitle"><?php echo $this->blockCommentsTitle; ?></h1>
    </header>
    <div id="RL_commentsContent">
        <?php foreach($this->blockCommentsContent as $comment): ?>
        <div class="commentsItem">
            <div><?php echo $comment['texte']; ?></div>
            <p><a href="#"><?php echo $comment['auteur']; ?></a>&nbsp;le <?php echo $comment['date']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</article>