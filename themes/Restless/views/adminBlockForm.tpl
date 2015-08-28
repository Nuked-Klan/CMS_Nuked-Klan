@if({{makeForm}} === true)
    <div class="RL_form{{currentAdminBlock}} RL_formContent">
        <h3>
            {{*EDIT_BLOCK}} "{{blockTitle}}"
        </h3>

        @if({{makeTitle}} === true)
        <div>
            <label>{{*BLOCK_TITLE}} :</label>
            <input class="RL_input" type="text" name="block{{currentAdminBlock}}Title" value="{{blockTitle}}" data-check="{{blockTitle}}"/>
        </div>
        @endif

        @if({{makeSelect}} === true)
            <div>
                <label>{{*NB_ELEMENTS_BLOCK}} :</label>
                <select class="RL_select" name="block{{currentAdminBlock}}NbItems" data-check="{{blockNbItems}}">
                    @foreach(selectBlock as key => selected)
                        <option value="{{key}}" {{selected}} >{{key}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if({{makeSelectCat}} === true)
            <div>
                <label>{{*SELECT_CAT}} :</label>
                <select class="RL_select" name="block{{currentAdminBlock}}Cat" data-check="{{selectedCat}}">
                    @foreach(selectCat as id => item)
                        <option value="{{id}}" {{item.selected}}>{{item.name}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if({{makeSelectMatch}} === true)
            <div>
                <label>{{*SELECT_MATCH}} :</label>
                <select class="RL_select" name="block{{currentAdminBlock}}Id" data-check="{{selectedMatch}}">
                    @foreach(selectMatch as id => item)
                        <option value="{{id}}" {{item.selected}}>{{item.name}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if({{makeCheckbox}} === true)
            <div>
                <label class="RL_inline">{{checkboxLabel}} :</label>
                <?php checkboxButton($this->get('checkboxInputName'), $this->get('checkboxInputName'), $this->get('checkboxChecked'), true); ?>
            </div>
        @endif

        @if({{makeInputSocial}} === true)
            @foreach(arrayInputSocial as name => value)
                <div>
                    <label class="RL_socialLabel RL_{{name}}">{{*LINK}} {{name}} :</label>
                    <input class="RL_input RL_socialInput" type="text" name="social{{name}}" value="{{value}}" data-check="{{value}}"/>
                </div>
            @endforeach
        @endif

        @if({{makeTextarea}} === true)
            <div>
                <label class="RL_inline">{{textareaTitle}} :</label>
                <textarea class="editor RL_input RL_socialInput" name="blockAboutContent" rows="11">{{textareaContent}}</textarea>
            </div>
        @endif
        <p style="text-align:center;">
            <a id="RL_close" class="button" href="#">{{*CLOSE}}</a>
        </p>
    </div>
@endif