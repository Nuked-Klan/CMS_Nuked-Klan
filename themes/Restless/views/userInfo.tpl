<div>
    <p>Bienvenue, Samoth</p>
    <p>
        <a id="RL_userMP" href="index.php?file=Userbox">
            <span <?php echo $this->messagesCss; ?> >
                <?php echo $this->nbMessages; ?>
            </span>
        </a>
        <a href="index.php?file=User">
            Compte
        </a>
        <?php if($GLOBALS['user'][1] >= 2): ?>
            <a href="index.php?file=Admin">
                Administration
            </a>
        <?php endif; ?>
    </p>
</div>
<img src="<?php echo $this->avatar; ?>" alt="" />
<a id="RL_userDisconnect" class="RL_button" href="index.php?file=User&amp;nuked_nude=index&amp;op=logout">
    <span>D&eacute;connexion</span>
</a>