                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo _CHECKUSERADMIN ?></h2>
                    <form method="post" action="index.php?action=saveUserAdmin" id="form_user_admin">
                        <div id="config" >
                            <label for="pseudo"><strong><?php echo _PSEUDO ?></strong></label>
                            <input type="text" id="pseudo" name="pseudo" value="" onblur="checkUserAdminInput($(this));" />
                            <label for="pass"><strong><?php echo _PASS ?></strong></label>
                            <input type="password" id="pass" name="pass" value="" onblur="checkUserAdminInput($(this));" />
                            <label for="pass2"><strong><?php echo _PASS2 ?></strong></label>
                            <input type="password" id="pass2" name="pass2" value="" onblur="checkUserAdminInput($(this));" />
                            <label for="mail"><strong><?php echo _MAIL ?></strong></label>
                            <input type="text" id="mail" name="mail" value="" onblur="checkUserAdminInput($(this));" />
                            <input type="hidden" name="send" value="ok" />
                        </div>
                        <div id="infos" style="text-align: center;margin:30px auto;color:#FF4040;"></div>
                        
                        <div style="text-align: center;margin:30px auto;">
                            <a href="#" id="submit" class="button"><?php echo _SUBMIT ?></a>
                        </div>
                    </form>
                    <script type="text/javascript">
                    //<![CDATA[
                    $('#submit').click(function() {
                        return checkUserAdminForm(
                            'form_user_admin',
                            '<?php echo addslashes(_WAIT) ?>',
                            '<?php echo addslashes(_ERROR_PSEUDO) ?>',
                            '<?php echo addslashes(_ERROR_PASS) ?>',
                            '<?php echo addslashes(_ERROR_PASS2) ?>',
                            '<?php echo addslashes(_ERROR_MAIL) ?>'
                        );
                    });
                    //]]>
                    </script>
                </div>