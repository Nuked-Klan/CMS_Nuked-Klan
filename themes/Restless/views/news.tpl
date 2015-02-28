<article>
    <aside>
        <img src="<?php echo $this->newsContent['image']; ?>" alt="" />
    </aside>
    <div>
        <header>
            <h2><?php echo $this->newsContent['title']; ?></h2>
            <a href="#"><?php echo $this->newsContent['nbComments']; ?></a>
        </header>
        <div>
            <?php echo $this->newsContent['texte']; ?>
        </div>
    </div>
    <footer>
        <div>
            <a href="#" class="RL_newsSocialIcon RL_newsFacebook" title="Facebook"><span>Facebook</span></a>
            <a href="#" class="RL_newsSocialIcon RL_newsTwitter" title="Twitter"><span>Twitter</span></a>
            <a href="#" class="RL_newsSocialIcon RL_newsGoogle" title="Google+"><span>Google+</span></a>
            <span>Par <a href="index.php?file=Members&op=detail&autor=<?php echo $this->newsContent['auteur']; ?>"><?php echo $this->newsContent['auteur']; ?></a>&nbsp;le&nbsp;<?php echo $this->newsContent['date']; ?></span>
        </div>
    </footer>
</article>