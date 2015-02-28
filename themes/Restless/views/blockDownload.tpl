<div>
    <article>
        <header>
            <h1 class="RL_modTitle"><?php echo $this->blockDownloadTitle; ?></h1>
            <a class="RL_moreButton" href="index.php?file=Download">Plus</a>
        </header>
        <div id="RL_blockDownload">
            <?php foreach($this->blockDownloadContent as $item): ?>
                <div>
                    <a href="#"><span>Lien</span></a>
                    <div>
                        <p><?php echo $item['title']; ?></p>
                        <p>T&eacute;l&eacute;charg&eacute; <?php echo $item['count']; ?> fois</p>
                    </div>
                </div>
            <?php
                endforeach;

                if(count($this->blockDownloadContent) == 0) :
            ?>
                <div>
                    <div class="RL_noDownload">
                        <p class="RL_noDownload"><?php echo NODOWNLOAD; ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </article>
</div>