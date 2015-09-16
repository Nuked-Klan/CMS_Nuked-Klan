                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo $i18n['CREATE_USER_ADMIN'] ?></h2>
                    <form method="post" action="index.php?action=saveUserAdmin" id="form_user_admin">
                        <div id="config" >
                            <label for="pseudo"><strong><?php echo $i18n['NICKNAME'] ?></strong></label>
                            <input type="text" id="pseudo" name="pseudo" value="" onblur="checkUserAdminInput($(this));" />
                            <label for="pass"><strong><?php echo $i18n['PASSWORD'] ?></strong></label>
                            <input type="password" id="pass" name="pass" value="" onblur="checkUserAdminInput($(this));" />
                            <label for="pass2"><strong><?php echo $i18n['PASSWORD_CONFIRM'] ?></strong></label>
                            <input type="password" id="pass2" name="pass2" value="" onblur="checkUserAdminInput($(this));" />
                            <label for="mail"><strong><?php echo $i18n['EMAIL'] ?></strong></label>
                            <input type="text" id="mail" name="mail" value="" onblur="checkUserAdminInput($(this));" />
                            <input type="hidden" name="send" value="ok" />
                        </div>
                        <div id="infos" style="text-align: center;margin:30px auto;color:#FF4040;"></div>
                        
                        <div style="text-align: center;margin:30px auto;">
                            <a href="#" id="submit" class="button"><?php echo $i18n['SUBMIT'] ?></a>
                        </div>
                    </form>
                    <script type="text/javascript">
                    //<![CDATA[
                    $('#submit').click(function() {
                        return checkUserAdminForm(
                            'form_user_admin',
                            '<?php echo addslashes($i18n['WAIT']) ?>',
                            '<?php echo addslashes($i18n['ERROR_NICKNAME']) ?>',
                            '<?php echo addslashes($i18n['ERROR_PASSWORD']) ?>',
                            '<?php echo addslashes($i18n['ERROR_PASSWORD_CONFIRM']) ?>',
                            '<?php echo addslashes($i18n['ERROR_EMAIL']) ?>'
                        );
                    });
                    //]]>
                    </script>
                </div>