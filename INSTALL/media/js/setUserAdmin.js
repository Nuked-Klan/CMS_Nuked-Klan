var formError;

function addUserAdminInputError(errorMsg) {
    $('#infos').html(errorMsg);
    formError++;
}

function checkUserAdminForm() {
    var nickname        = $('input[name="nickname"]'),
        password        = $('input[name="password"]'),
        passwordConfirm = $('input[name="passwordConfirm"]'),
        email           = $('input[name="email"]'),
        nicknameVal         = $.trim(nickname.val()),
        passwordVal         = $.trim(password.val()),
        passwordConfirmVal  = $.trim(passwordConfirm.val()),
        emailVal            = $.trim(email.val());

    $('#infos').empty();

    formError = 0;

    if (! checkUserAdminNickname()) {
        addUserAdminInputError(i18n.error_nickname);
    }
    else if (checkUserAdminPassword('password')) {
        addUserAdminInputError(i18n.error_password);
    }
    else if (checkUserAdminPassword('passwordConfirm')) {
        addUserAdminInputError(i18n.error_password_confirm);
    }
    else if (! checkUserAdminEmail()) {
        addUserAdminInputError(i18n.error_email);
    }

    if (formError > 0)
        return false;
    else
        $('#form_user_admin').submit();
}

function checkUserAdminNickname(input) {
    if (input === undefined)
        input = $('#nickname');

    var nickname    = $.trim($(input).val()),
        regNickname = new RegExp('[\$\^\(\)\'"?%#<>,;:]');

    if (nickname.length < 3 || regNickname.test(nickname)) {
        $(input).addClass('error');
        return false;
    }
    else {
        $(input).removeClass('error');
        return true;
    }
}

function checkUserAdminPassword(input) {
    var checkPasswordConfirm = true;

    if (input === 'password') {
        input = $('#password');
        checkPasswordConfirm = false;
    }
    else if (input === 'passwordConfirm')
        input = $('#passwordConfirm');

    var inputName = $(input).attr('name');

    if ($.trim($(input).val()) == '' || (checkPasswordConfirm && $('#password').val() != $('#passwordConfirm').val())) {
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

function checkUserAdminEmail(input) {
    if (input === undefined)
        input = $('#email');

    var email    = $.trim($(input).val()),
        regEmail = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');

    if (email == '' || ! regEmail.test(email)) {
        $(input).addClass('error');
        return false;
    }
    else {
        $(input).removeClass('error');
        return true;
    }
}