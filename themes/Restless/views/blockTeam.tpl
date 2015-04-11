@if(count({{blockTeamContent}}) > 0 && {{blockTeamActive}} == true)
<section class="RL_blockRight">
    <header>
        <h3>{{blockTeamTitle}}</h3>
    </header>
    <article>
        @foreach(blockTeamContent as team)
        <figure class="RL_roster">
            <div class="RL_bgCover" style="background-image:url({{team.image}})"><!-- No Content --></div>
            <img src="{{team.image}}" alt="#" />
            <figcaption><a class="RL_linkWhite" href="{{team.link}}">{{team.name}}</a></figcaption>
        </figure>
        @endforeach
    </article>
</section>
@endif