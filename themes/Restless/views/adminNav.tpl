<div class="nkAdminMenu">
    <ul class="shortcut-buttons-set">
       @foreach(arrayNav as item)
            <li {{item.active}} >
                <a class="shortcut-button" href="{{item.link}}">
                    <img src="themes/Restless/images/{{item.icon}}" alt="icon">
                    <span>{{item.text}}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
<div class="clear"></div>