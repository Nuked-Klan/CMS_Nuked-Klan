                <div style="text-align: center;margin:30px auto;">
                    <h3 style="margin-bottom:30px;" ><?php echo $i18n['SELECT_STATS'] ?></h3>
                    <?php echo $i18n['STATS_INFO'] ?>
                    <form action="index.php?action=setSendStats" method="post" id="form_stats" >
                        <p style="margin-top:20px;"><input type="checkbox" id="conf_stats" name="conf_stats" <?php echo ($stats) ? 'checked="checked"' : '' ?> style="vertical-align:middle;" />&nbsp;<label for="conf_stats" style="vertical-align:middle;"><?php echo $i18n['CONFIRM_STATS'] ?></label></p>
                        <p><a href="#" style="margin-top:20px;" class="button" onclick="document.forms['form_stats'].submit();" ><?php echo $i18n['CONFIRM'] ?></a></p>
                    </form>
                </div>