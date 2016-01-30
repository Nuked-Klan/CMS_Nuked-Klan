                <h1><?php echo $i18n['SELECT_LANGUAGE'] ?> : </h1>
                <form id="languageForm" method="post" action="index.php?action=setLanguage">
                    <p>
                        <select id="language" name="language">
<?php
    foreach ($languageList as $languageName) :
?>
                            <option value="<?php echo $languageName ?>"<?php selected($language, $languageName) ?>>
                                <?php echo $i18n[strtoupper($languageName)] ?>
                            </option>
<?php
    endforeach
?>
                        </select>
                    </p>
                    <div id="links">
                        <input type="submit" name="submit" value="<?php echo $i18n['SUBMIT'] ?>" />
                    </div>
                </form>