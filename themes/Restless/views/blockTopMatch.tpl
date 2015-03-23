<aside id="RL_blockTopMatch">
    <h2>{{topMatchContent.title}}</h2>
    <figure>
        <figcaption>{{topMatchContent.teamName}}</figcaption>
        <img src="{{topMatchContent.teamLogo}}" alt="" />
    </figure>
    <div>
        <p class="RL_{{topMatchContent.scoreClass}}" >{{topMatchContent.score}}</p>
    </div>
    <figure>
        <figcaption>{{topMatchContent.opponentName}}</figcaption>
        <img src="{{topMatchContent.opponentLogo}}" alt="" />
    </figure>
    <p>
        <strong>Date : </strong>{{topMatchContent.date}}<br/>
        <strong>Maps : </strong>{{topMatchContent.maps}}
    </p>
    <a href="#" class="RL_button">D&eacute;tails</a>
</aside>
