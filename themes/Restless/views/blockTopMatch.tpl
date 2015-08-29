<aside id="RL_blockTopMatch">
    <h2>{{topMatchContent.title}}</h2>
    <figure>
        <figcaption>{{topMatchContent.teamName}}</figcaption>
        <div class="RL_bgCover" style="background-image:url({{topMatchContent.teamLogo}})"><!-- No Content --></div>
        <img src="{{topMatchContent.teamLogo}}" alt="" />
    </figure>
    <div>
        <p class="RL_matches{{topMatchContent.scoreClass}}" >
            @if({{topMatchContent.state}} == 1)
                {{topMatchContent.score}}
            @endif
        </p>
    </div>
    <figure>
        <figcaption>{{topMatchContent.opponentName}}</figcaption>
        <div class="RL_bgCover" style="background-image:url({{topMatchContent.opponentLogo}})"><!-- No Content --></div>
        <img src="{{topMatchContent.opponentLogo}}" alt="" />
    </figure>
    <p>
        <strong>Date : </strong>{{topMatchContent.date}}<br/>
        <strong>Maps : </strong>{{topMatchContent.map}}
    </p>
    <a href="{{topMatchContent.link}}" class="RL_button">D&eacute;tails</a>
</aside>
