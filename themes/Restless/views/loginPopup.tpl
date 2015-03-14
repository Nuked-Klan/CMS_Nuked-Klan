<div id="RL_loginPopupContainer">
    <form method="post" action="index.php?file=User&amp;op=login&amp;nuked_nude=index">
        <p>
            <?php echo _LOGINUSER; ?>
        </p>
        <div>
            <label class="RL_labelIcons" id="RL_inputNick" for="pseudo">
                <span><?php echo _NICK; ?></span>
            </label>
            <input type="text" name="pseudo" required="required"/>
        </div>
        <div>
            <label class="RL_labelIcons" id="RL_inputPass" for="pass">
                <span><?php echo _PASSWORD; ?></span>
            </label>
            <input type="password" name="pass" required="required" />
        </div>

        <?php
            if($this->captcha === true){
                echo create_captcha();
            }
        ?>

        <div>
            <input type="checkbox" name="remember_me" value="ok" checked="checked" />
            <label for="remember_me">
                <span><?php echo REMEMBER_ME; ?></span>
            </label>
        </div>
        <div>
            <input type="submit" value="<?php echo _SEND; ?>" />
        </div>
        <a href="index.php?file=User&amp;op=oubli_pass">
            <?php echo LOST_PASS; ?>
        </a>
    </form>
</div>