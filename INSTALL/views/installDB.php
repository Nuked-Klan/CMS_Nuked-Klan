                <div style="text-align: center;">
                    <h2><?php echo ($process == 'install') ? $i18n['CREATE_DB'] : $i18n['UPDATE_DB'] ?></h2>
                    <div id="log_install"><?php echo $i18n['WAITING'] ?></div>
                    <div id="progress" class="progress-bar-bg">
                    <span class="progress-bar" ></span></div>
                    <script type="text/javascript">
                    var process = '<?php echo $process ?>';
                    var dbPrefix = '<?php echo $db_prefix ?>';
                    var createdSuccess = '<?php echo $i18n['CREATED_TABLE_SUCCESS'] ?>';
                    var updatedSuccess = '<?php echo $i18n['UPDATE_TABLE_SUCCESS'] ?>';
                    var removedSuccess = '<?php echo $i18n['REMOVE_TABLE_SUCCESS'] ?>';
                    var nothingToDo = '<?php echo $i18n['NOTHING_TO_DO'] ?>';
                    var step = '<?php echo $i18n['STEP'] ?>';
                    var start_process_txt = '<?php echo addslashes($i18n['STARTING_'. strtoupper($process)]) ?>';
                    var complete = '<?php echo addslashes($i18n[strtoupper($process) .'_SUCCESS']) ?>';
                    var complete_error = '<?php echo addslashes($i18n[strtoupper($process) .'_FAILED']) ?>';
                    var print_error = '<?php echo $i18n['PRINT_ERROR'] ?>';
                    var continue_txt = '<?php echo $i18n['NEXT'] ?>';
                    var retry = '<?php echo $i18n['RETRY'] ?>';
                    var error = '<?php echo ($process == 'install') ? $i18n['CREATED_TABLE_ERROR'] : $i18n['UPDATE_TABLE_ERROR'] ?>';
                    var processTableList    = new Array('<?php echo implode('\',\'', $processTableList) ?>');
                    var nbProcessTable      = processTableList.length;
                    var processProgress     = 100 / nbProcessTable;
                    </script>
                    <a href="#" class="button" id="continue_install" onclick="submit('<?php echo $process ?>')"><?php echo $i18n['START'] ?></a>
                </div>