                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo $i18n['CREATE_USER_ADMIN'] ?></h2>
                    <form method="post" action="index.php?action=saveUserAdmin" id="form_user_admin">
                        <div id="config" >
                            <label for="nickname"><strong><?php echo $i18n['NICKNAME'] ?></strong></label>
                            <input type="text" id="nickname" name="nickname" value="" onblur="checkUserAdminNickname($(this));" />
                            <label for="password"><strong><?php echo $i18n['PASSWORD'] ?></strong></label>
                            <input type="password" id="password" name="password" value="" onblur="checkUserAdminPassword($(this));" />
                            <label for="passwordConfirm"><strong><?php echo $i18n['PASSWORD_CONFIRM'] ?></strong></label>
                            <input type="password" id="passwordConfirm" name="passwordConfirm" value="" onblur="checkUserAdminPassword($(this));" />
                            <label for="mail"><strong><?php echo $i18n['EMAIL'] ?></strong></label>
                            <input type="text" id="email" name="email" value="" onblur="checkUserAdminEmail($(this));" />
                        </div>
                        <div id="infos" style="text-align: center;margin:30px auto;color:#FF4040;"></div>
                        <div style="text-align: center;margin:30px auto;">
                            <a href="#" id="submit" class="button"><?php echo $i18n['SUBMIT'] ?></a>
                        </div>
                    </form>
                    <script type="text/javascript">
                    //<![CDATA[
                    $('#submit').click(function() {
                        return checkUserAdminForm();
                    });
                    //]]>
                    </script>
                </div>