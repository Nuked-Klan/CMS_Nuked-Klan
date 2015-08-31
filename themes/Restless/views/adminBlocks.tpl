<form id="RL_formBlocks" action="index.php?file=Admin&amp;page=theme&amp;op=saveBlocks" method="post">
    <table>
        <thead>
            <th style="width:70%;">{{*BLOCK_NAME}}</th>
            <th style="width:15%;text-align:center;">Activation</th>
            <th style="width:15%;text-align:center;">{{*_EDIT}}</th>
        </thead>
        @foreach(arrayBlocks as blockName => block)
        <tr>
            <td>
                <strong>{{block.title}} : </strong>
            </td>
            <td style="text-align:center;">
                @if({{blockName}} == 'About' || {{blockName}} == 'Sponsors')
                    <img src="themes/Restless/images/block.png" alt="" />
                @else
                    %checkboxButton('block'.{{blockName}}.'Active', 'block'.{{blockName}}.'Active', {{block.checked}}, true)
                @endif
            </td>
            <td style="text-align:center;">
                    <a class="RL_getRow" id="{{blockName}}" href="#" >
                        <img src="themes/Restless/images/edit.png" title="{{*_EDIT}}" alt="{{*_EDIT}}" />
                    </a>
            </td>
        </tr>
        @endforeach
        <tr class="RL_alertForm">
            <td colspan="3">
                %printNotification({{*EDIT_NOT_SAVE}}, null, 'attention', false, false)
            </td>
        </tr>
        <tr class="RL_alertForm">
        </tr>
        <tr>
            <td colspan="3" style="text-align:center;">
                <input class="button" type="submit" value="{{*SUBMIT}}"/>
            </td>
        </tr>
    </table>
    <?php
        foreach($this->get('arrayBlocks') as $block => $value){
            $this->assign('currentAdminBlock', $block);
            $this->render('adminBlockForm');
        }
    ?>
</form>