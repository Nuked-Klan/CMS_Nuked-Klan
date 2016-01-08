var busy, errors, processEnd, ajaxBusy, tableNumber, currentSubProcess, title, step, progress,
    runSubProcessResult = 'CONTINUE',
    loadingImg          = '<img src="media/images/loading.gif" alt="" id="loading_img" />';

currentSubProcess = '';
busy = ajaxBusy = processEnd = false;
errors = tableNumber = 0;
step = progress = 1;

function addLogMessage(msg) {
    $('#loading_img').remove();

    $('#log_install')
        .append(msg)
        .scrollTop($('#log_install')[0].scrollHeight);
}

function writeInfo(table, txt, status) {
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
    else if (status == 'FOREIGN_KEY_ADDED_TO_TABLE') {
        msg = i18n.foreign_key_add_to_table_success.replace(/%s/, dbPrefix + '_' + table);
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
        if (currentSubProcess == 'dropTable')
            msg = i18n.no_table_to_drop.replace(/%s/, dbPrefix + '_' + table);
        else if (currentSubProcess == 'checkIntegrity')
            msg = i18n.nothing_to_check.replace(/%s/, dbPrefix + '_' + table);
        else if (currentSubProcess == 'checkAndConvertCharsetAndCollation')
            msg = i18n.no_convert_table.replace(/%s/, dbPrefix + '_' + table);
        else
            msg = i18n.nothing_to_do.replace(/%s/, dbPrefix + '_' + table);
    }
    else if (status == 'STEP') {
        msg = i18n.update_table_step.replace(/%1\$s/, dbPrefix + '_' + table);
        msg = msg.replace(/%2\$s/, txt);
    }
    else {
        msg = txt + ' <b>' + dbPrefix + '_' + table + '</b>';
    }

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

function setNextCurrentProcess() {
    if (currentSubProcess == 'dropTable') {
        currentSubProcess = 'createTable';
    }
    else if (currentSubProcess == 'createTable') {
        currentSubProcess = 'addForeignKeyOfTable';
    }
    else if (currentSubProcess == 'checkIntegrity') {
        currentSubProcess = 'checkAndConvertCharsetAndCollation';
    }
    else if (currentSubProcess == 'checkAndConvertCharsetAndCollation') {
        currentSubProcess = 'updateTable';
    }
}

function writeSubProcessTitle(title) {
    addLogMessage('<br /><b><i>' + i18n.step + ' ' + step + ' : ' + title + '</i></b><br />');
    step++;
}

function writeSubProcessComplete(subProcess, subProcessFail) {
    if (errors == 0) {
        addLogMessage('<br /><b>' + subProcess + ' : </b><strong style="color:green;">'
            + i18n.success + '</strong>' + loadingImg + '<br />');

        if (currentSubProcess != '')
            setTimeout('queueSubProcess()', 300);

        tableNumber = 0;
    }
    else {
        addLogMessage('<br /><b><strong>' + subProcess + ' : </b><strong style="color:red;">'
            + i18n.failure + '</strong>' + '<br />' + subProcessFail.replace(/%d/, errors) + '<br />');
    }
}

function queueSubProcess() {
    var nbTable;

    if (process == 'install') {
        if (currentSubProcess == 'dropTable' || currentSubProcess == 'createTable') {
            nbTable = nbProcessTable;

            if (tableNumber == 0) {
                if (currentSubProcess == 'dropTable')
                    writeSubProcessTitle(i18n.drop_all_table);
                else
                    writeSubProcessTitle(i18n.create_all_table);
            }
        }
        else if (currentSubProcess == 'addForeignKeyOfTable') {
            nbTable = nbTableWithForeignKey;

            if (tableNumber == 0)
                writeSubProcessTitle(i18n.add_foreign_key_all_table);
        }
    }
    else if (process == 'update') {
        if (currentSubProcess == 'checkIntegrity') {
            nbTable = nbCheckIntegrityTable;

            if (tableNumber == 0)
                writeSubProcessTitle(i18n.check_all_table_integrity);
        }
        else {
            if (currentSubProcess == 'updateTable') {
                nbTable = nbProcessTable;

                if (tableNumber == 0)
                    writeSubProcessTitle(i18n.update_all_table);
            }
            else {
                nbTable = nbCheckAndConvertCharsetAndCollationTable;

                if (tableNumber == 0)
                    writeSubProcessTitle(i18n.check_table_charset);
            }
        }
    }

    if (tableNumber < nbTable) {
        if (ajaxBusy === false) {
            ajaxBusy = true;

            if (process == 'install') {
                if (currentSubProcess == 'dropTable' || currentSubProcess == 'createTable') {
                    result = runSubProcess(processTableList[tableNumber]);
                }
                else if (currentSubProcess == 'addForeignKeyOfTable') {
                    result = runSubProcess(tableWithForeignKeyList[tableNumber]);
                }
            }
            else if (process == 'update') {
                if (currentSubProcess == 'checkIntegrity') {
                    result = runSubProcess(checkIntegrityTableList[tableNumber]);
                }
                else {
                    if (currentSubProcess == 'checkAndConvertCharsetAndCollation')
                        result = runSubProcess(checkAndConvertCharsetAndCollationTableList[tableNumber]);
                    else
                        result = runSubProcess(processTableList[tableNumber]);
                }
            }

            if (result == 'CONTINUE')
                tableNumber++;
        }

        setTimeout('queueSubProcess()', 300);
    }
    else {
        if (ajaxBusy === false) {
            if (process == 'install' && currentSubProcess == 'dropTable') {
                setNextCurrentProcess();
                writeSubProcessComplete(i18n.drop_all_table, i18n.drop_all_table_failed);
            }
            else if (process == 'install' && currentSubProcess == 'createTable') {
                setNextCurrentProcess();
                writeSubProcessComplete(i18n.create_all_table, i18n.create_all_table_failed);
            }
            else if (process == 'update' && currentSubProcess == 'checkIntegrity') {
                setNextCurrentProcess();
                writeSubProcessComplete(i18n.check_all_table_integrity, i18n.check_integrity_failed);
            }
            else if (process == 'update' && currentSubProcess == 'checkAndConvertCharsetAndCollation') {
                setNextCurrentProcess();
                writeSubProcessComplete(i18n.table_convertion, i18n.converted_table_failed);
            }
            else {
                if (process == 'install' && currentSubProcess == 'addForeignKeyOfTable') {
                    currentSubProcess = '';
                    writeSubProcessComplete(i18n.add_foreign_key_all_table, i18n.add_foreign_key_all_table_failed);
                }

                mainProcessEnd();
            }
        }
        else
            setTimeout('queueSubProcess()', 300);
    }
}

function mainProcessEnd() {
    if (errors == 0) {
        if (process == 'install')
            endText = i18n.install_process_success;
        else
            endText = i18n.update_process_success;

        $('#startProcess').text(i18n.next);
        processEnd = true;
    }
    else {
        if (process == 'install')
            endText = i18n.install_failed.replace(/%d/, errors);
        else {
            endText = i18n.update_failed.replace(/%d/, errors);
            processProgress = 100 / (nbProcessTable);
        }

        $('#startProcess').text(i18n.retry);
    }

    writeComplete(endText);

    $('#startProcess')
        .removeClass('button_disabled')
        .addClass('button');
}

function runSubProcess(tableFile) {
    runSubProcessResult = 'CONTINUE';
    var data = 'tableFile=' + tableFile;

    if (process == 'install') {
        if (currentSubProcess == 'dropTable') {
            data += '&dropTable=true';
        }
        else if (currentSubProcess == 'addForeignKeyOfTable') {
            data += '&addForeignKeyOfTable=true';
        }
    }
    else if (process == 'update') {
        if (currentSubProcess == 'checkIntegrity') {
            data += '&checkIntegrity=true';
        }
        else if (currentSubProcess == 'checkAndConvertCharsetAndCollation') {
            data += '&checkAndConvertCharsetAndCollation=true';
        }
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

            txt        = data[1];
            actionList = data[2];
        }

        if (txt == 'INTEGRITY_ACCEPTED'
            || txt == 'TABLE_CONVERTED'
            || txt == 'CREATED'
            || txt == 'FOREIGN_KEY_ADDED_TO_TABLE'
            || txt == 'UPDATED'
            || txt == 'REMOVED'
            || txt == 'NOTHING_TO_DO'
        ) {
            if (! (currentSubProcess == 'dropTable' || currentSubProcess == 'addForeignKeyOfTable')
                && txt != 'CREATED' && actionList != ''
            )
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
        else {
            var regStep = new RegExp('^STEP_[0-9]+_TOTAL_STEP_[0-9]+$');

            if (actionList != '')
                addLogMessage(actionList + loadingImg);

            if (regStep.test(txt)) {
                var data = txt.match(/^STEP_([0-9]+)_TOTAL_STEP_([0-9]+)$/);

                writeInfo(table, data[1] + ' / ' + data[2], 'STEP');
                runSubProcessResult = 'STEP';
            }
            else {
                if (process == 'update' && currentSubProcess == 'checkIntegrity')
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

        ajaxBusy = false;
    });

    return runSubProcessResult;
}

function submit() {
    if (processEnd === true && process == 'install') {
        window.location = 'index.php?action=setUserAdmin';
    }
    else if (processEnd === true && process == 'update') {
        window.location = 'index.php?action=updateConfig';
    }
    else {
        mainProcessStart();
    }
}

function mainProcessStart() {
    if (busy == false) {
        busy = true;

        if (process == 'install') {
            text              = i18n.starting_install;
            currentSubProcess = 'dropTable';
        }
        else {
            text              = i18n.starting_update;
            currentSubProcess = 'checkIntegrity';
        }

        $('#log_install')
            .html(text + loadingImg + '<br />');

        $('#startProcess')
            .removeClass('button')
            .addClass('button_disabled');

        queueSubProcess();
    }
}