<div id="RL_loginPopupContainer">
    <form method="post" action="index.php?file=User&amp;op=login&amp;nuked_nude=index">
        <p>
            {{*_LOGINUSER}}
        </p>
        <div>
            <label class="RL_labelIcons" id="RL_inputNick" for="pseudo">
                <span>{{*_NICK}}</span>
            </label>
            <input type="text" name="pseudo" placeholder="{{*_NICK}}" required="required"/>
        </div>
        <div>
            <label class="RL_labelIcons" id="RL_inputPass" for="pass">
                <span>{{*_PASSWORD}}</span>
            </label>
            <input type="password" name="pass" placeholder="{{*_PASSWORD}}" required="required" />
        </div>
            @if({{captcha}} === true)
                <?php echo create_captcha(); ?>
            @endif
        <div>
            <input type="checkbox" name="remember_me" value="ok" checked="checked" />
            <label for="remember_me">
                <span>{{*REMEMBER_ME}}</span>
            </label>
        </div>
        <div>
            <input type="submit" value="{{*_SEND}}" />
        </div>
        <a href="index.php?file=User&amp;op=oubli_pass">
            {{*LOST_PASS}}
        </a>
    </form>
</div>