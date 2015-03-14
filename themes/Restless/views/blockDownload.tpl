<?php if($this->get('nbDownloads') > 0 && $this->get('blockDownloadActive')): ?>
<div>
    <article>
        <header>
            <h1 class="RL_modTitle">{{blockDownloadTitle}}</h1>
            <a class="RL_moreButton" href="index.php?file=Download">Plus</a>
        </header>
        <div id="RL_blockDownload">
            <?php foreach($this->get('blockDownloadContent') as $item): ?>
                <div>
                    <a href="<?php echo $item['link']; ?>"><span>Lien</span></a>
                    <div>
                        <p><?php echo $item['title']; ?></p>
                        <p>T&eacute;l&eacute;charg&eacute; <?php echo $item['count']; ?> fois</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</div><!-- Hack inline-block
--><?php endif; ?>