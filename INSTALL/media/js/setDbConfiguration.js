var currentDbPort,
    waitMsg = '<span id="waitMsg">' + i18n.wait + '</span><img id="loadingImg" src="media/images/loading.gif" alt="" />';

/**
 * Check database configuration form before submit it.
 */
function checkConfigForm(event) {
    var notification = $('#notification'),
        dbHost       = $('#dbHost'),
        dbUser       = $('#dbUser'),
        dbPassword   = $('#dbPassword'),
        dbName       = $('#dbName'),
        dbPrefix     = $('#dbPrefix'),
        dbType       = $('#dbType').val();

    var dbHostError        = i18n.db_host_error.replace(/%s/, dbType),
        dbHostConnectError = i18n.db_host_connect_error.replace(/%s/, dbType);

    try {
        if (! checkConfigDbHost(dbHost)) throw dbHostError;
        if (! checkConfigDbUser(dbUser)) throw i18n.db_user_error;
        if (! checkConfigDbPassword(dbPassword)) throw i18n.db_password_error;
        if (! checkConfigDbPrefix(dbName)) throw i18n.db_prefix_error;
        if (! checkConfigDbName(dbPrefix)) throw i18n.db_name_error;

        notification.removeClass('errorNotification')
            .html(waitMsg);

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
                'db_type' :   dbType
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
    }
    catch (errorMsg) {
        notification.html(errorMsg)
            .addClass('errorNotification');

        event.preventDefault();
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
        passwordInput = $('#dbPassword');

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
    if ($('#dbUser').val() != 'root' && $.trim(input.val()) == '') {
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
 * Check database port value.
 */
function checkConfigDbport(input) {
    var cleanedValue = $.trim(input.val());

    if (cleanedValue != '' && (! cleanedValue.match(/^\d+$/) || cleanedValue > 65535)) {
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
    //currentDbPort = $('#dbPort').val();

    $('#dbConfigurationForm').submit(checkConfigForm);
    $('#dbHost').blur(function() { checkConfigDbHost($(this)); });
    $('#dbUser').blur(function() { checkConfigDbUser($(this)); });
    $('#dbPassword').blur(function() { checkConfigDbPassword($(this)); });
    $('#dbPrefix').blur(function() { checkConfigDbPrefix($(this)); });
    $('#dbName').blur(function() { checkConfigDbName($(this)); });

    $('#dbType').change(function() {
        var dbType = $(this).val()
        infoImg = '<img class="infoLogo" src="media/images/info.png" alt="" />';

        $('#dbHostBox label').html(i18n.db_host.replace(/%s/, dbType));

        if (document.getElementById('dbHostInfo'))
            $('#dbHostInfo').html(infoImg + i18n.install_db_host.replace(/%s/, dbType));

        if (document.getElementById('dbUserInfo'))
            $('#dbUserInfo').html(infoImg + i18n.install_db_user.replace(/%s/, dbType));

        if (document.getElementById('dbPasswordInfo'))
            $('#dbPasswordInfo').html(infoImg + i18n.install_db_password.replace(/%s/, dbType));

        if (document.getElementById('dbPrefixInfo'))
            $('#dbPrefixInfo').html(infoImg + i18n.install_db_prefix.replace(/%s/, dbType));

        if (document.getElementById('dbNameInfo'))
            $('#dbNameInfo').html(infoImg + i18n.install_db_name.replace(/%s/, dbType));
    });

    $('#dbPort').blur(function() { checkConfigDbport($(this)); });


    $('#advanced').on('click', function() {
        if ($(this).attr('data-click-state') == 1) {
            $(this).attr('data-click-state', 0);
            $('#advancedBox').css('display', 'none');
        }
        else {
            $(this).attr('data-click-state', 1);
            $('#advancedBox').css('display', 'block');
        }
    });

});
