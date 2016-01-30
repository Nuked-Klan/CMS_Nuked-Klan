                <h1><?php echo $i18n['SELECT_STATS'] ?></h1>
                <?php echo $i18n['STATS_INFO'] ?>
                <form action="index.php?action=setSendStats" method="post" id="statsForm">
                    <p><input type="checkbox" id="stats" name="stats"<?php checked($stats) ?> />&nbsp;<label for="stats"><?php echo $i18n['CONFIRM_STATS'] ?></label></p>
                    <div id="links">
                        <input type="submit" name="submit" value="<?php echo $i18n['CONFIRM'] ?>" />
                    </div>
                </form>
