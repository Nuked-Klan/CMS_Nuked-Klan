<div class="RL_infosFooter">
    <p id="RL_infosVisitor">
        @if($this->get('nbAdmins') > 0)
            <span>{{nbAdmins}}</span> {{*ADMINISTRATOR}}{{adminsPlural}}
        @endif
        @if($this->get('nbMembers') > 0)
            <span>{{nbMembers}}</span> {{*MEMBER}}{{membersPlural}}
        @endif
            <span>{{nbVisitors}}</span> {{*VISITOR}}{{visitorsPlural}} {{*ONLINE}}
    </p>
</div>