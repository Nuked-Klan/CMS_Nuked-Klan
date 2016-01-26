/**
 * Check database configuration form before submit it.
 */
//function checkConfigForm(form, event) {
function checkConfigForm() {
    var infos      = $('#infos'),
        dbHost     = $('#db_host'),
        dbUser     = $('#db_user'),
        dbPassword = $('#db_pass'),
        dbName     = $('#db_name'),
        dbPrefix   = $('#db_prefix'),
        dbType     = $('#db_type').val();

    var dbHostError        = i18n.db_host_error.replace(/%s/, dbType),
        dbHostConnectError = i18n.db_host_connect_error.replace(/%s/, dbType);

    try {
        if (! checkConfigDbHost(dbHost)) throw dbHostError;
        if (! checkConfigDbUser(dbUser)) throw i18n.db_user_error;
        if (! checkConfigDbPassword(dbPassword)) throw i18n.db_password_error;
        if (! checkConfigDbPrefix(dbName)) throw i18n.db_prefix_error;
        if (! checkConfigDbName(dbPrefix)) throw i18n.db_name_error;

        infos.html('<span style="color:#000;">' + i18n.wait + '</span><img src="media/images/loading.gif" alt="" />');

        $.ajax({
            async: false,
            type: 'POST',
            url: 'index.php?action=dbConnectTest',
            data: {
                'db_host' :   dbHost.val(),
                'db_user' :   dbUser.val(),
                'db_pass' :   dbPassword.val(),
                'db_name' :   dbName.val(),
                'db_prefix' : dbPrefix.val(),
                'db_type' :   dbType.val()
            }
        }).done(function(txt) {
            if (txt != 'OK') {
                if (txt == 'DB_HOST_ERROR') {
                    dbHost.addClass('error');
                    throw i18n.db_connect_fail + '<br />' + dbHostConnectError;
                }
                else if (txt == 'DB_LOGIN_ERROR') {
                    dbUser.addClass('error');
                    dbPassword.addClass('error');
                    throw i18n.db_connect_fail + '<br />' + i18n.db_login_connect_error;
                }
                else if (txt == 'DB_NAME_ERROR') {
                    dbName.addClass('error');
                    throw i18n.db_connect_fail + '<br />' + i18n.db_name_connect_error;
                }
                else if (txt == 'DB_PREFIX_ERROR') {
                    dbPrefix.addClass('error');
                    throw i18n.db_connect_fail + '<br />' + i18n.db_prefix_connect_error;
                }
                else if (txt == 'DB_CHARSET_ERROR') {
                    throw i18n.db_connect_fail + '<br />' + i18n.db_charset_connect_error;
                }
                else {
                    throw i18n.db_connect_fail + '<br />' + txt;
                }
            }
        });

        $('#form_config').submit();
        //form.submit();
    }
    catch (errorMsg) {
        //infos.empty();
        infos.html(errorMsg);
        return false;
        //event.preventDefault();
    }
}

/**
 * Check database host value.
 */
function checkConfigDbHost(input) {
    if ($.trim(input.val()) == '') {
        input.addClass('error');
        return false;
    }
    else {
        input.removeClass('error');
        return true;
    }
}

/**
 * Check database user value.
 */
function checkConfigDbUser(input) {
    var user = input.val(),
        passwordInput = $('#db_pass');

    if ($.trim(user) == '') {
        input.addClass('error');
        return false;
    }
    else if (user == 'root' && passwordInput.val() == '' && passwordInput.hasClass('error')) {
        input.removeClass('error');
        passwordInput.removeClass('error');
        return true;
    }
    else {
        input.removeClass('error');
        return true;
    }
}

/**
 * Check database password value.
 */
function checkConfigDbPassword(input) {
    if ($('#db_user').val() != 'root' && $.trim(input.val()) == '') {
        input.addClass('error');
        return false;
    }
    else {
        input.removeClass('error');
        return true;
    }
}

/**
 * Check database prefix value.
 */
function checkConfigDbPrefix(input) {
    if ($.trim(input.val()) == '') {
        input.addClass('error');
        return false;
    }
    else {
        input.removeClass('error');
        return true;
    }
}

/**
 * Check database name value.
 */
function checkConfigDbName(input) {
    if ($.trim(input.val()) == '') {
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
    //$('#form_config').submit(function(event) { checkConfigForm($(this), event); });
    $('#submit').click(function() { return checkConfigForm(); });
    $('#db_host').blur(function() { checkConfigDbHost($(this)); });
    $('#db_user').blur(function() { checkConfigDbUser($(this)); });
    $('#db_pass').blur(function() { checkConfigDbPassword($(this)); });

    /*
    $('#db_type').change(function() {
        var dbType = $(this).val();

        $('#db_host_label strong').html(i18n.db_host.replace(/%s/, dbType));

        if (document.getElementById('db_host_info'))
            $('#db_host_info').html(i18n.install_db_host.replace(/%s/, dbType));

        if (document.getElementById('db_user_info'))
            $('#db_user_info').html(i18n.install_db_user.replace(/%s/, dbType));

        if (document.getElementById('db_password_info'))
            $('#db_password_info').html(i18n.install_db_password.replace(/%s/, dbType));

        if (document.getElementById('db_prefix_info'))
            $('#db_prefix_info').html(i18n.install_db_prefix.replace(/%s/, dbType));

        if (document.getElementById('db_name_info'))
            $('#db_name_info').html(i18n.install_db_name.replace(/%s/, dbType));
    });
    */

    $('#db_prefix').blur(function() { checkConfigDbPrefix($(this)); });
    $('#db_name').blur(function() { checkConfigDbName($(this)); });
});