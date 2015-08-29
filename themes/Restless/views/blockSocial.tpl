@if(count({{blockSocialContent}}) > 0 && {{blockSocialActive}} == true)
<article>
    <header>
        <h1 class="RL_modTitle">{{blockSocialTitle}}</h1>
    </header>
    <div id="RL_followContent">
        @foreach(blockSocialContent as social => socialLink)
        <a href="{{socialLink}}" class="RL_follow{{social}}" target="_blank">
            <span>{{social}}</span>
        </a>
        @endforeach
    </div>
</article>
@endif