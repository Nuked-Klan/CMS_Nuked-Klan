<header>
    @if({{displayLogo}} === true)
        <img id="RL_mainLogo" src="{{mainLogo}}" alt="{{title}}" />
    @else
        @if({{*HOMEPAGE}} === true)
            <h1 id="RL_mainTitle">{{title}}</h1>
        @else
            <span id="RL_mainTitle">{{title}}</span>
        @endif
    @endif
</header>
