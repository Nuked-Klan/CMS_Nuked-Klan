/**
 * Check Super Administrator form before submit it.
 */
function checkUserAdminForm() {
    var infos = $('#infos');

    try {
        infos.empty();

        if (! checkUserAdminNickname($('#nickname'))) throw i18n.error_nickname;
        if (! checkUserAdminPassword($('#password'))) throw i18n.error_password;
        if (! checkUserAdminPassword($('#passwordConfirm'))) throw i18n.error_password_confirm;
        if (! checkUserAdminEmail($('#email'))) throw i18n.error_email;

        $('#form_user_admin').submit();
    }
    catch(errorMsg) {
        infos.html(errorMsg);
        return false;
    }
}

/**
 * Check Super Administrator nickname.
 */
function checkUserAdminNickname(input) {
    var nickname    = $.trim(input.val()),
        regNickname = new RegExp('[\$\^\(\)\'"?%#<>,;:]');

    if (nickname.length < 3 || regNickname.test(nickname)) {
        input.addClass('error');
        return false;
    }
    else {
        input.removeClass('error');
        return true;
    }
}

/**
 * Check Super Administrator password.
 */
function checkUserAdminPassword(input) {
    var password = $.trim(input.val());

    if (password == '' || (input.selector != '#password' && $('#password').val() != $('#passwordConfirm').val())) {
        $('#password').addClass('error');
        $('#passwordConfirm').addClass('error');
        return false;
    }
    else {
        $('#password').removeClass('error');
        $('#passwordConfirm').removeClass('error');
        return true;
    }
}

/**
 * Check Super Administrator email.
 */
function checkUserAdminEmail(input) {
    var email    = $.trim(input.val()),
        regEmail = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');

    if (email == '' || ! regEmail.test(email)) {
        input.addClass('error');
        return false;
    }
    else {
        input.removeClass('error');
        return true;
    }
}