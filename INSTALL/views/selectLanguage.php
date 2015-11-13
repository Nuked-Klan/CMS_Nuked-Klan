                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo $i18n['SELECT_LANGUAGE'] ?> : </h2>
                    <form id="form_lang" method="post" action="index.php?action=setLanguage">
                        <p>
                            <select id="language" name="language">
<?php
    foreach ($languageList as $languageName) :
?>
                                <option value="<?php echo $languageName ?>"<?php echo ($language == $languageName) ? ' selected="selected"' : '' ?>><?php echo $i18n[strtoupper($languageName)] ?></option>
<?php
    endforeach
?>
                            </select><br/>
                            <a href="#" style="display:inline-block;margin-top:30px;" class="button" id="button" onclick="document.forms['form_lang'].submit();" ><?php echo $i18n['SUBMIT'] ?></a>
                        </p>
                    </form>
                </div>
                <script type="text/javascript">
                $('#language').change(function() {
                    window.location = 'index.php?language=' + $(this).val();
                });
                </script>