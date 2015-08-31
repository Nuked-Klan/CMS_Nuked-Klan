<form id="RL_formModules" action="index.php?file=Admin&amp;page=theme&amp;op=saveModules" method="post">
    <table>
        <thead>
        <th style="width:55%;">{{*MODULE_NAME}}</th>
        <th style="width:15%;text-align:center;">{{*DISPLAY_FULL_PAGE}}</th>
        <th style="width:15%;text-align:center;">{{*DISPLAY_SLIDER}}</th>
        <th style="width:15%;text-align:center;">{{*DISPLAY_ARTICLE}}</th>
        </thead>
        @foreach(arrayModules as name => module)
            <tr>
                <td>
                    <strong>{{module.name}} : </strong>
                </td>
                <td style="text-align:center;">
                   %checkboxButton('module'.{{name}}.'FullPage', 'module'.{{name}}.'FullPage', {{module.fullPage}}, false)
                </td>
                <td style="text-align:center;">
                    %checkboxButton('module'.{{name}}.'Slider', 'module'.{{name}}.'Slider', {{module.slider}}, false)
                </td>
                <td style="text-align:center;">
                    %checkboxButton('module'.{{name}}.'Article', 'module'.{{name}}.'Article', {{module.article}}, false)
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align:center;">
                <input class="button" type="submit" value="{{*SUBMIT}}"/>
            </td>
        </tr>
    </table>
</form>