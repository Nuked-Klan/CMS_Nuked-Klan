var busy, errors, processEnd, ajaxBusy, tableNumber, checkIntegrity, checkAndConvertCharsetAndCollation,
    progress            = 1,
    runProcessResult    = 'CONTINUE',
    loadingImg          = '<img src="media/images/loading.gif" alt="" id="loading_img" />';

busy = ajaxBusy = processEnd = checkIntegrity = checkAndConvertCharsetAndCollation = false;
errors = tableNumber = 0;


function addLogMessage(msg) {
    $('#loading_img').remove();

    $('#log_install')
        .append(msg)
        .scrollTop($('#log_install')[0].scrollHeight);
}

function writeInfo(table, txt, status, newline) {
    var msg;

    if (status == 'INTEGRITY_ACCEPTED') {
        msg = i18n.check_table_integrity.replace(/%s/, dbPrefix + '_' + table)
            + ' : <span style="color:green;">' + i18n.success + '</span>';
    }
    else if (status == 'INTEGRITY_FAIL') {
        msg = i18n.check_table_integrity.replace(/%s/, dbPrefix + '_' + table)
            + ' : <span style="color:red;">' + i18n.failure + '</span>';
    }
    else if (status == 'TABLE_CONVERTED') {
        msg = i18n.converted_table_success.replace(/%s/, dbPrefix + '_' + table);
    }
    else if (status == 'CREATED') {
        msg = i18n.created_table_success.replace(/%s/, dbPrefix + '_' + table);
    }
    else if (status == 'UPDATED') {
        msg = i18n.update_table_success.replace(/%s/, dbPrefix + '_' + table);
    }
    else if (status == 'REMOVED') {
        msg = i18n.remove_table_success.replace(/%s/, dbPrefix + '_' + table);
    }
    else if (status == 'NOTHING_TO_DO') {
        if (! checkIntegrity)
            msg = i18n.nothing_to_check.replace(/%s/, dbPrefix + '_' + table);
        else if (checkIntegrity && ! checkAndConvertCharsetAndCollation)
            msg = i18n.no_convert_table.replace(/%s/, dbPrefix + '_' + table);
        else
            msg = i18n.nothing_to_do.replace(/%s/, dbPrefix + '_' + table);
    }
    else {
        msg = txt + ' <b>' + dbPrefix + '_' + table + '</b>';
    }

    //if (! (process == 'update' && checkIntegrity && checkAndConvertCharsetAndCollation))
    //    msg = '<br />' + msg


    addLogMessage(msg + loadingImg + '<br />');
}

function writeError(text, errorMsg) {
    addLogMessage('<b>' + text + ' ' + errorMsg + '</b>' + loadingImg + '<br />');
}

function writeComplete(endText) {
    addLogMessage('<br />' + endText);

    busy        = false;
    progress    = 1;
    tableNumber = errors = 0;
}

function queueProcess() {
    var nbTable;

    if (process == 'install' || (process == 'update' && checkIntegrity && checkAndConvertCharsetAndCollation)) {
        nbTable = nbProcessTable;

        if (tableNumber == 0)
            addLogMessage('<br />');
    }
    else if (process == 'update' && ! checkIntegrity) {
        nbTable = nbCheckIntegrityTable;

        if (tableNumber == 0)
            addLogMessage('<br />' + i18n.check_all_table_integrity + ' :<br />');
    }
    else if (process == 'update' && checkIntegrity && ! checkAndConvertCharsetAndCollation) {
        nbTable = nbCheckAndConvertCharsetAndCollationTable;

        if (tableNumber == 0)
            addLogMessage('<br />' + i18n.check_table_charset + ' :<br />');
    }

    if (tableNumber < nbTable) {
        if (ajaxBusy === false) {
            ajaxBusy = true;

            if (process == 'install' || (process == 'update' && checkIntegrity && checkAndConvertCharsetAndCollation))
                result = runProcess(processTableList[tableNumber]);
            else if (process == 'update' && ! checkIntegrity)
                result = runProcess(checkIntegrityTableList[tableNumber]);
            else if (process == 'update' && checkIntegrity && ! checkAndConvertCharsetAndCollation)
                result = runProcess(checkAndConvertCharsetAndCollationTableList[tableNumber]);

            if (result == 'CONTINUE')
                tableNumber++;
        }

        setTimeout('queueProcess()', 300);
    } else {
        if (ajaxBusy === false) {
            if (process == 'update' && ! checkIntegrity) {
                if (errors == 0) {
                    checkIntegrity = true;
                    addLogMessage('<br /><b>' + i18n.check_all_table_integrity + ' : </b><strong style="color:green;">'
                        + i18n.success + '</strong>' + loadingImg + '<br />');

                    setTimeout('queueProcess()', 300);
                    tableNumber = 0;
                }
                else {
                    addLogMessage('<br /><b><strong>' + i18n.check_all_table_integrity + ' : </b><strong style="color:red;">'
                        + i18n.failure + '</strong>' + '<br />' + i18n.check_integrity_failed.replace(/%d/, errors) + '<br />');
                }
            }
            else if (process == 'update' && checkIntegrity && ! checkAndConvertCharsetAndCollation) {
                if (errors == 0) {
                    checkAndConvertCharsetAndCollation = true;
                    addLogMessage('<br /><b>' + i18n.table_convertion + ' : </b><strong style="color:green;">'
                        + i18n.success + '</strong>' + loadingImg + '<br />');

                    setTimeout('queueProcess()', 300);
                    tableNumber = 0;
                }
                else {
                    addLogMessage('<br /><b>' + i18n.table_convertion + ' : </b><strong style="color:red;">'
                        + i18n.failure + '</strong>' + '<br />' + i18n.converted_table_failed.replace(/%d/, errors));
                }
            }
            else
                viewEnd();
        }
        else
            setTimeout('queueProcess()', 300);
    }
}

function viewEnd() {
    if (errors == 0) {
        if (process == 'install')
            endText = i18n.install_success;
        else
            endText = i18n.update_success;

        $('#continue_install').text(i18n.next);
        processEnd = true;
    }
    else {
        if (process == 'install')
            endText = i18n.install_failed.replace(/%d/, errors);
        else
            endText = i18n.update_failed.replace(/%d/, errors);

        $('#continue_install').text(i18n.retry);
    }

    writeComplete(endText);

    $('#continue_install')
        .removeClass('button_disabled')
        .addClass('button');
}

function runProcess(tableFile) {
    runProcessResult = 'CONTINUE';
    var data = 'tableFile=' + tableFile;

    if (process == 'update') {
        if ( ! checkIntegrity)
            data = 'tableFile=' + tableFile + '&checkIntegrity=true';
        else if (checkIntegrity && ! checkAndConvertCharsetAndCollation)
            data = 'tableFile=' + tableFile + '&checkAndConvertCharsetAndCollation=true';
    }

    $.ajax({
        async: false,
        type: 'POST',
        url: 'index.php?action=runTableProcessAction&language=' + language,
        data: data
    }).done(function(txt) {
        var tableFileData = tableFile.split('.');
        var table = tableFileData[1];

        var regProcessResult = new RegExp('^#[_0-9A-Z]+#.*$');

        actionList = '';

        if (regProcessResult.test(txt)) {
            var data = txt.match(/^#([_0-9A-Z]+)#(.*)$/);

            txt = data[1];
            actionList = data[2];
        }

        if (txt == 'INTEGRITY_ACCEPTED'
            || txt == 'TABLE_CONVERTED'
            || txt == 'CREATED'
            || txt == 'UPDATED'
            || txt == 'REMOVED'
            || txt == 'NOTHING_TO_DO'
        ) {
            if (txt != 'CREATED' && actionList != '')
                addLogMessage(actionList + loadingImg);

            writeInfo(table, 'ok', txt);
            $('.progress-bar').css('width', processProgress * progress + '%');
            progress++;
        }
        else if (txt == 'INTEGRITY_FAIL') {
            writeInfo(table, 'ok', txt);
            errors++;
            writeError(i18n.print_error, txt);
        }
        //else if (txt != 'NEXT') {
        else {
            var regStep = new RegExp('^STEP_[0-9]+_TOTAL_STEP_[0-9]+$');

            if (actionList != '')
                addLogMessage(actionList + loadingImg);

            if (regStep.test(txt)) {
                var data = txt.match(/^STEP_([0-9]+)_TOTAL_STEP_([0-9]+)$/);

                writeInfo(table, ' - ' + i18n.step + ' : ' + data[1] + ' / ' + data[2], 'STEP');
                runProcessResult = 'STEP';
            }
            else {
                if (process == 'update' && ! checkIntegrity)
                    writeInfo(table, i18n.check_table_integrity_error, 'NO');
                else {
                    if (process == 'install')
                        writeInfo(table, i18n.created_table_error, 'NO');
                    else
                        writeInfo(table, i18n.update_table_error, 'NO');
                }

                errors++;
                writeError(i18n.print_error, txt);
            }
        }

        //if (actionList != '')
        //    addLogMessage(actionList + loadingImg);

        //$('#log_install').scrollTop($('#log_install')[0].scrollHeight);
        ajaxBusy = false;
    });

    return runProcessResult;
}

function submit() {
    if (processEnd === true && process == 'install') {
        window.location = 'index.php?action=setUserAdmin';
    }
    else if (processEnd === true && process == 'update') {
        window.location = 'index.php?action=updateConfig';
    }
    else {
        startProcess();
    }
}

function startProcess() {
    if (busy == false) {
        busy = true;

        if (process == 'install')
            text = i18n.starting_install;
        else
            text = i18n.starting_update;

        $('#log_install')
            .html(text + loadingImg + '<br />');

        $('#continue_install')
            .removeClass('button')
            .addClass('button_disabled');

        queueProcess();
    }
}