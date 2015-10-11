var formError;

function addUserAdminInputError(input, errorMsg) {
    input.addClass('error');
    $('#infos').html(errorMsg);
    formError++;
}

function checkUserAdminForm() {
    var nickname        = $('input[name="pseudo"]'),
        password        = $('input[name="pass"]'),
        passConfirm     = $('input[name="pass2"]'),
        mail            = $('input[name="mail"]'),
        regNickname     = new RegExp('[\$\^\(\)\'"?%#<>,;:]'),
        regMail         = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i'),
        nicknameVal     = $.trim(nickname.val()),
        passwordVal     = $.trim(password.val()),
        passConfirmVal  = $.trim(passwordConfirm.val()),
        mailVal         = $.trim(mail.val());

    $('#infos').html('&nbsp;');

    formError = 0;

    if (nicknameVal.length < 3 || nicknameVal == '' || regNickname.test(nicknameVal)) {
        addUserAdminInputError(nickname, i18n.error_nickname);
    }
    else if (passwordVal == '') {
        addUserAdminInputError(password, i18n.error_password);
    }
    else if (passConfirmVal == '' || passwordVal != passConfirmVal) {
        addUserAdminInputError(passConfirm, i18n.error_password_confirm);
    }
    else if (! regMail.test(mailVal) || mailVal == '') {
        addUserAdminInputError(mail, i18n.error_email);
    }

    if (formError > 0)
        return false;
    else
        $('#form_user_admin').submit();
}

function checkUserAdminInput(input) {
    if ($(input).val() == '' || $(input).attr('name') == 'pass2'
        && ($('input[name="pass"]').val() != $('input[name="pass2"]').val())
    )
        $(input).addClass('error');
    else
        $(input).removeClass('error');
}