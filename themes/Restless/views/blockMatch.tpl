@if(count({{blockMatchContent}}) > 0 && {{blockMatchActive}} == true)
<section class="RL_blockRight">
    <header>
        <h3>{{blockMatchTitle}}</h3>
    </header>
    <article>
        <table class="RL_matchesTable" cellspacing="0" cellpadding="0">
            @foreach(blockMatchContent as match)
            <tr>
                <td class="RL_matchesIcon">
                    <img src="{{match.icon}}" alt="" />
                </td>
                <td class="RL_matchesFlag">
                    <img src="{{match.flag}}" alt="" />
                </td>
                <td class="RL_matchesTeams">
                    <span><a href="{{match.teamLink}}">{{match.teamName}}</a></span>
                    <span><a href="{{match.opponentLink}}">{{match.opponentName}}</a></span>
                </td>
                <td class="RL_matchesScore">
                    {{match.score}}
                </td>
                <td class="RL_matchesIconScore RL_matches{{match.scoreClass}}">
                    <span>{{match.scoreClass}}</span>
                </td>
            </tr>
            @endforeach
        </table>
    </article>
</section>
@endif