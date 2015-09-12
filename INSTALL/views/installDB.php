                <div style="text-align: center;">
                    <h2><?php echo ($process == 'install') ? _CREATEDB : _UPDATEDB ?></h2>
                    <div id="log_install"><?php echo _WAITING ?></div>
                    <div id="progress" class="progress-bar-bg">
                    <span class="progress-bar" ></span></div>
                    <script type="text/javascript">
                    var process = '<?php echo $process ?>';
                    var dbPrefix = '<?php echo $db_prefix ?>';

                    var createdSuccess = '<?php echo _LOGITXTSUCCESS ?>';
                    var updatedSuccess = '<?php echo _LOGUTXTUPDATE2 ?>';
                    var removedSuccess = '<?php echo _LOGUTXTREMOVE ?>';
                    var nothingToDo = '<?php echo _LOGUTXTNOTHINGTODO ?>';

                    var step = 'Etape';

                    var start_process_txt = '<?php echo addslashes(constant('_STARTING'. strtoupper($process))) ?>';
                    var complete = '<?php echo addslashes($complete) ?>';
                    var complete_error_start = '<?php echo addslashes($complete_error_start) ?>';
                    var complete_error_end = '<?php echo addslashes($complete_error_end) ?>';
                    var print_error = '<?php echo _PRINTERROR ?>';
                    var continue_txt = '<?php echo _CONTINUE ?>';
                    var retry = '<?php echo _RETRY ?>';
                    var error = '<?php echo $error ?>';

                    var processTableList    = new Array('<?php echo implode('\',\'', $processTableList) ?>');
                    var nbProcessTable      = processTableList.length;
                    var processProgress     = 100 / nbProcessTable;
                    </script>
                    <a href="#" class="button" id="continue_install" onclick="submit('<?php echo $process ?>')"><?php echo _START ?></a>
                </div>