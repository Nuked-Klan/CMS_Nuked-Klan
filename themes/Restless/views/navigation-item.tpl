<li>
    <a href="{{data.link}}" {{data.blank}}>
        <span>{{data.title}}</span>
    </a>
    @if(array_key_exists('subnav', {{data}}))
        <ul class="RL_subMenu">
            @foreach(data.subnav as sub)<!--
                -->@include(navigation-item, sub)<!--
            -->@endforeach
        </ul>
    @endif
</li>