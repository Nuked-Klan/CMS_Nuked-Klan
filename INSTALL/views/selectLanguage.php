                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo _SELECTLANG ?> : </h2>
                    <form id="form_lang" method="post" action="index.php?action=setLanguage">
                        <p>
                            <select id="language" name="language">
<?php
    foreach ($languageList as $k => $v) :
?>
                                <option value="<?php echo $k ?>"<?php echo ($language == $k) ? ' selected="selected"' : '' ?>><?php echo constant($v) ?></option>
<?php
    endforeach
?>
                            </select><br/>
                            <a href="#" style="display:inline-block;margin-top:30px;" class="button" id="button" onclick="document.forms['form_lang'].submit();" ><?php echo _SUBMIT ?></a>
                        </p>
                    </form>
                </div>
                <script type="text/javascript">
                $('#language').change(function() {
                    window.location = 'index.php?language=' + $(this).val();
                });
                </script>