/**
 * Check Administrator form before submit it.
 */
function checkAdministratorForm(event) {
    var notification = $('#notification');

    try {
        notification.empty();

        if (! checkAdministratorNickname($('#nickname'))) throw i18n.error_nickname;
        if (! checkAdministratorPassword($('#password'))) throw i18n.error_password;
        if (! checkAdministratorPassword($('#passwordConfirm'))) throw i18n.error_password_confirm;
        if (! checkAdministratorEmail($('#email'))) throw i18n.error_email;
    }
    catch (errorMsg) {
        notification.html(errorMsg);
        event.preventDefault();
    }
}

/**
 * Check Administrator nickname.
 */
function checkAdministratorNickname(input) {
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
 * Check Administrator password.
 */
function checkAdministratorPassword(input) {
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
 * Check Administrator email.
 */
function checkAdministratorEmail(input) {
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

/**
 * Link input event with form functions.
 */
$(document).ready(function() {
    $('#administratorForm').submit(checkAdministratorForm);
    $('#nickname').blur(function() { checkAdministratorNickname($(this)); });
    $('#password').blur(function() { checkAdministratorPassword($(this)); });
    $('#passwordConfirm').blur(function() { checkAdministratorPassword($(this)); });
    $('#email').blur(function() { checkAdministratorEmail($(this)); });
});