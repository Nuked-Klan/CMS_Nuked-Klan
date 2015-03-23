@if({{makeForm}} === true)
    <div class="RL_form{{currentAdminBlock}} RL_formContent">
        <h3>
            {{*EDIT_BLOCK}} "{{blockTitle}}"
        </h3>

        <div>
            <label>{{*BLOCK_TITLE}} :</label>
            <input class="RL_input" type="text" name="block{{currentAdminBlock}}Title" value="{{blockTitle}}" data-check="{{blockTitle}}"/>
        </div>
        @if({{makeSelect}} === true)
            <div>
                <label>{{*NB_ELEMENTS_BLOCK}} :</label>
                <select class="RL_select" name="block{{currentAdminBlock}}NbItems" data-check="{{blockNbItems}}">
                    @foreach(selectBlock as key => selected)
                    <option value="{{key}}"
                    {{selected}}>{{key}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if({{makeSelectCat}} === true)
            <div>
                <label>{{*SELECT_CAT}} :</label>
                <select class="RL_select" name="block{{currentAdminBlock}}Cat" data-check="{{selectedCat}}">
                    @foreach(selectCat as id => name)
                        <?php
                            $selected = null;
                            if($id == $this->get('selectedCat')){
                                $selected = 'selected="selected"';
                            }
                        ?>
                    <option value="{{id}}"
                    {{selected}}>{{name}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if({{makeCheckbox}} === true)
            <div>
                <label class="RL_inline">{{*ACTIVE_LIGHTBOX}} :</label>
                <?php checkboxButton($this->get('lightboxInputName'), $this->get('lightboxInputName'), $this->get('lightboxChecked'), true); ?>
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
        <p style="text-align:center;">
            <a id="RL_close" class="button" href="#">{{*CLOSE}}</a>
        </p>
    </div>
@endif