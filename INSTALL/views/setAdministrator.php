                <h1><?php echo $i18n['CREATE_ADMINISTRATOR'] ?></h1>
                <form method="post" action="index.php?action=saveAdministrator" id="administratorForm" class="form">
                    <div id="nicknameBox">
                        <label for="nickname"><?php echo $i18n['NICKNAME'] ?></label>
                        <input type="text" id="nickname" name="nickname" value="" />
                    </div>
                    <div id="passwordBox">
                        <label for="password"><?php echo $i18n['PASSWORD'] ?></label>
                        <input type="password" id="password" name="password" value="" />
                    </div>
                    <div id="passwordConfirmBox">
                        <label for="passwordConfirm"><?php echo $i18n['PASSWORD_CONFIRM'] ?></label>
                        <input type="password" id="passwordConfirm" name="passwordConfirm" value="" />
                    </div>
                    <div id="emailBox">
                        <label for="email"><?php echo $i18n['EMAIL'] ?></label>
                        <input type="text" id="email" name="email" value="" />
                    </div>
                    <div id="notification"></div>
                    <div id="links">
                        <input type="submit" name="submit" value="<?php echo $i18n['SUBMIT'] ?>" />
                    </div>
                </form>