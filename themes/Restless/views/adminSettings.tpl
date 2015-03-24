<form action="index.php?file=Admin&amp;page=theme&amp;op=saveSettings" method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <td style="width:20%;"><label>{{*TEMPLATE_COLOR}}</label></td>
            <td>
                <select id="RL_selectColor" class="RL_select RL_small {{currentColor}}" name="styleColor">
                    @foreach(selectColor as color)
                        <option value="{{color.value}}" {{color.selected}}> {{color.text}}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td style="width:20%;"><label>{{*MAIN_TITLE}}</label></td>
            <td>
                <input class="RL_input RL_block" type="text" name="mainTitle" value="{{mainTitle}}"/>
                <small>{{*ADVERT_MAIN_LOGO}}</small>
            </td>
        </tr>
        <tr>
            <td><label>{{*MAIN_LOGO}}</label></td>
            <td>
                <input class="RL_input RL_block" type="text" name="mainLogoUrl" value="{{mainLogo}}"/>
                <input class="RL_inputFile" type="file" name="mainLogoFile" />
                <button class="RL_buttonFile button">Parcourir</button>
                <small>Fichier selectionné : <span>N/A</span></small>
            </td>
        </tr>
        <tr>
            <td><label>{{*MAIN_LOGO_POSITION}}</label></td>
            <td>
                <select class="RL_select RL_small" name="mainLogoPosition">
                    @foreach(mainLogoPosition as position)
                        <option value="{{position.value}}" {{position.selected}} >{{position.text}}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td><label>{{*MAIN_LOGO_MARGIN}}</label></td>
            <td>
                <input id="RL_mainLogoMarginInput" name="mainLogoMargin" type="range" value="{{mainLogoMargin}}" max="150" min="0" step="5">
                <small>{{mainLogoMargin}} px</small>
            </td>
        </tr>
        <tr>
            <td><label>{{*BACKGROUND_IMAGE}}</label></td>
            <td>
                <input class="RL_input RL_block" type="text" name="backgroundImageUrl" value="{{backgroundImage}}"/>
                <input class="RL_inputFile" type="file" name="backgroundImageFile" />
                <button class="RL_buttonFile button">Parcourir</button>
                <small>Fichier selectionné : <span>N/A</span></small>
            </td>
        </tr>
        <tr>
            <td><label>{{*BACKGROUND_POSITION}}</label></td>
            <td>
                <select class="RL_select RL_small" name="backgroundPosition">
                    @foreach(backgroundPosition as position)
                        <option value="{{position.value}}" {{position.selected}} >{{position.text}}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center;">
                <input class="button" type="submit" value="{{*SUBMIT}}"/>
            </td>
        </tr>
    </table>
</form>