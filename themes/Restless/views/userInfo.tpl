<div>
    <p>{{*_WELCOME}}, {{pseudo}}</p>
    <p>
        <a id="RL_userMP" href="index.php?file=Userbox">
            <span {{messagesCss}} >
                {{nbMessages}}
            </span>
        </a>
        <a href="index.php?file=User">
            {{*_ACCOUNT}}
        </a>
        @if({{GLOBALS.user.1}} >= 2)
        <a href="index.php?file=Admin">
            {{*_ADMINISTRATION}}
        </a>
        @endif
    </p>
</div>
<img src="{{avatar}}" alt="" />
<a id="RL_userDisconnect" class="RL_button" href="index.php?file=User&amp;nuked_nude=index&amp;op=logout">
    <span>{{*_LOGOUT}}</span>
</a>