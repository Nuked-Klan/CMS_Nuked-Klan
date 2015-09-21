var busy        = false;
var errors      = 0;
var install     = false;
var ajaxBusy    = false;
var i           = 1;
var j           = 0;


function writeInfo(table, txt, status) {
    var msg;

    if (status == 'CREATED' || status == 'UPDATED' || status == 'REMOVED' || status == 'NOTHING_TO_DO') {
        msg = 'Table ' + dbPrefix + '_' + table + ' ';

        if (status == 'CREATED') {
            msg += createdSuccess;
        }
        else if (status == 'UPDATED') {
            msg += updatedSuccess;
        }
        else if (status == 'REMOVED') {
            msg += removedSuccess;
        }
        else if (status == 'NOTHING_TO_DO') {
            msg += nothingToDo;
        }
    }
    else {
        msg = txt + ' ' + dbPrefix + '_' + table;
    }

    $('#loading_img').remove();
    $('#log_install').append('<br />' + msg
        + '<img src="media/images/loading.gif" alt="" id="loading_img" />');  
}

function writeError(text, errorMsg) {
    $('#loading_img').remove();

    $('#log_install').append('<br /><strong>' + text + ' ' + errorMsg +
        '</strong><img src="media/images/loading.gif" alt="" id="loading_img" />');  
}

function writeComplete(txt_end) {
    $('#loading_img').remove();

    $('#log_install')
        .append('<br />' + txt_end)
        .scrollTop(1000);

    busy    = false;
    i       = 1;
    j       = 0;
    errors  = 0;
}

function queueProcess() {
    if (j < nbProcessTable) {
        if (ajaxBusy === false) {
            ajaxBusy = true;
            nbStep = createTable(processTableList[j]);

            if (nbStep == 1)
                j++;
        }

        setTimeout('queueProcess()', 300);

    } else {
        if (ajaxBusy === false)
            viewEnd();
        else
            setTimeout('queueProcess()', 300);
    }
}

function viewEnd() {
    if (errors == 0) {
        txt_end = complete;
        $('#continue_install').text(continue_txt);
        install = true;
    }
    else {
        //txt_end = complete_error_start + errors + complete_error_end;
        txt_end = complete_error.replace(/%d/, errors);
        $('#continue_install').text(retry);
    }

    writeComplete(txt_end);

    $('#continue_install')
        .removeClass('button_disabled')
        .addClass('button');
}

function createTable(tableFile) {
    var remainingStep = 1;

    $.ajax({
        async: true,
        type: 'POST',
        url: 'index.php?action=creatingDB',
        data: 'tableFile=' + tableFile
    }).done(function(txt) {
        var tableFileData = tableFile.split('.');
        var table = tableFileData[1];

        if (txt == 'CREATED' || txt == 'UPDATED' || txt == 'REMOVED' || txt == 'NOTHING_TO_DO') {
            writeInfo(table, 'ok', txt);
            $('.progress-bar').css('width', processProgress * i + '%');
            i++;
        }
        else {
            var regStep = new RegExp('^STEP-[0-9]+-TOTAL-STEP-[0-9]+$');

            if (regStep.test(txt)) {
                var data        = msg.match(/^STEP-([0-9]+)-TOTAL-STEP-([0-9]+)$/), currentStep;
                currentStep     = data[0];
                nbStep          = data[1];
                remainingStep   = nbStep - currentStep + 1
                writeInfo(table, step, 'OK');
            }
            else {
                writeInfo(table, error, 'NO');
                errors++;
                writeError(print_error, txt);
            }
        }

        $('#log_install').scrollTop(1000);
        ajaxBusy = false;
    });

    return remainingStep;
}

function submit(type) {
    if (install === true && type == 'install') {
        window.location = 'index.php?action=setUserAdmin';
    }
    else if (install === true && type == 'update') {
        window.location = 'index.php?action=updateConfig';
    }
    else {
        startProcess();
    }
}

function startProcess() {
    if (busy == false) {
        busy = true;

        $('#log_install')
            .text(start_process_txt)
            .append('<img src="media/images/loading.gif" alt="" id="loading_img" />');

        $('#continue_install')
            .removeClass('button')
            .addClass('button_disabled');

        queueProcess();
    }
}

// Check config form

function addConfigInputError(input, dbConnectFail, errorMsg) {
    $('#infos').html(dbConnectFail + '<br/>' + errorMsg);
    $('#loading_img').remove();
    if (input !== undefined) input.addClass('error');
}

function checkConfigForm(type, form, wait, dbConnectFail, hostError, loginError, dbError, prefixError, charsetError) {
    var host        = $('input[name="db_host"]');
    var user        = $('input[name="db_user"]');
    var pass        = $('input[name="db_pass"]');
    var dbname      = $('input[name="db_name"]');
    var prefix      = $('input[name="db_prefix"]');
    var bddError    = null;
    var formErrors  = 0;

    $('input[id]').each(function() {
        if ($(this).attr('type') == 'text' || $(this).attr('type') == 'password') {
            if (($(this).val() == '' && $(this).attr('name') != 'db_pass' && $(this).attr('name') != 'db_prefix')
                || ($(this).attr('name') == 'db_pass' && user.val() != 'root' && $(this).val() == '')
            ) {
                $(this).addClass('error');
                formErrors++;
            }
        }
    });

    if (formErrors != 0) {
        return false;
    }
    else {
        $('#infos').text(wait + '  ');
        $('#infos').append('<img src="media/images/loading.gif" alt="" id="loading_img" />');

        if (type == 'update')
            var typeurl = 'index.php?action=dbConnectTest&type=update';
        else
            var typeurl = 'index.php?action=dbConnectTest';

        //password = encodeURIComponent(pass.val());

        $.ajax({
            async: false,
            type: 'POST',
            url: typeurl,
            data: {
                'db_host' : host.val(),
                'db_user' : user.val(),
                'db_pass' : pass.val(),
                'db_name' : dbname.val(),
                'db_prefix' : prefix.val()
            }
        }).done(function(txt) {
            host.removeClass('error');
            user.removeClass('error');
            pass.removeClass('error');
            dbname.removeClass('error');
            prefix.removeClass('error');

            if (txt == 'OK') {
                bddError = false;
            }
            else {
                bddError = true;

                if (txt == 'DB_HOST_ERROR') {
                    addConfigInputError(host, dbConnectFail, hostError);
                }
                else if (txt == 'DB_LOGIN_ERROR') {
                    addConfigInputError(pass, dbConnectFail, loginError);

                    if (type == 'install')
                        user.addClass('error');
                }
                else if (txt == 'DB_NAME_ERROR') {
                    addConfigInputError(dbname, dbConnectFail, dbError);
                }
                else if(txt == 'DB_PREFIX_ERROR') {
                    addConfigInputError(prefix, dbConnectFail, prefixError);
                }
                else if(txt == 'DB_CHARSET_ERROR') {
                    addConfigInputError(undefined, dbConnectFail, charsetError);
                }
                else {
                    $('#infos').html(txt);
                }
            }
        });

        if (bddError == false)
            $('#'+form).submit();
    }
}

function checkConfigInput(input) {
    if (($(input).val() == '' && $(input).attr('name') != 'db_pass')
        || ($(input).attr('name') == 'db_pass' && $('input[name="db_user"]').val() != 'root' && $(input).val() == '')
    )
        $(input).addClass('error');
    else
        $(input).removeClass('error');
}

// Check user admin form

function addUserAdminInputError(input, errorMsg) {
    input.addClass('error');
    $('#infos').html(errorMsg);
}

function checkUserAdminForm(formId, wait, nicknameError, passwordError, passwordConfirmError, emailError) {
    var nickname    = $('input[name="pseudo"]');
    var password    = $('input[name="pass"]');
    var pass2       = $('input[name="pass2"]');
    var mail        = $('input[name="mail"]');
    var formError   = true;
    var regpseudo   = new RegExp('[\$\^\(\)\'"?%#<>,;:]');
    var regmail     = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');

    $('#infos').html('&nbsp;');

    if (nickname.val().length < 3 || nickname.val() == '' || regpseudo.test(nickname.val())) {
        addUserAdminInputError(nickname, nicknameError);
    }
    else if (password.val() == '') {
        addUserAdminInputError(password, passwordError);
    }
    else if (pass2.val() == '' || password.val() != pass2.val()) {
        addUserAdminInputError(pass2, passwordConfirmError);
    }
    else if (!regmail.test(mail.val()) || mail.val() == '') {
        addUserAdminInputError(mail, emailError);
    }
    else {
        formError = false;
    }

    if (! formError)
        $('#' + formId).submit();
    else
        return false;
}

function checkUserAdminInput(input) {
    if ($(input).val() == '' || $(input).attr('name') == 'pass2'
        && ($('input[name="pass"]').val() != $('input[name="pass2"]').val())
    )
        $(input).addClass('error');
    else
        $(input).removeClass('error');
}