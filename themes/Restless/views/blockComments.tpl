<?php if($this->get('nbComments') > 0 && $this->get('blockGuestbookActive') == true): ?>
<article>
    <header>
        <h1 class="RL_modTitle">{{blockGuestbookTitle}}</h1>
    </header>
    <div id="RL_commentsContent">
        <?php foreach($this->get('blockGuestbookContent') as $comment): ?>
        <div class="commentsItem">
            <div><?php echo $comment['text']; ?></div>
            <p><a href="#"><?php echo $comment['author']; ?></a>&nbsp;le <?php echo $comment['date']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</article>
<?php endif; ?>