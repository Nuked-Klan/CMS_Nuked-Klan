@if(count({{blockTeamContent}}) > 0 && {{blockTeamActive}} == true)
<section class="RL_blockRight">
    <header>
        <h3>{{blockTeamTitle}}</h3>
    </header>
    <article>
        @foreach(blockTeamContent as equipe)
        <figure class="RL_roster">
            <img src="{{equipe.image}}" alt="#" />
            <figcaption>{{equipe.title}}</figcaption>
        </figure>
        @endforeach
    </article>
</section>
@endif