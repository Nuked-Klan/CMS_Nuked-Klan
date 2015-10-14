                <div style="text-align: center;">
                    <h2><?php echo ($process == 'install') ? $i18n['CREATE_DB'] : $i18n['UPDATE_DB'] ?></h2>
                    <div id="log_install"><?php echo $i18n['WAITING'] ?></div>
                    <div id="progress" class="progress-bar-bg">
                    <span class="progress-bar" ></span></div>
                    <script type="text/javascript">
                    //<![CDATA[
                    var process = '<?php echo $process ?>',
                        dbPrefix = '<?php echo $db_prefix ?>',
                        processTableList = new Array('<?php echo implode('\',\'', $processDataList['processList']) ?>'),
                        nbProcessTable = processTableList.length;
<?php
    if ($process == 'install') :
?>
                    var processProgress = 100 / nbProcessTable;
<?php
    elseif ($process == 'update') :
?>
                    var checkIntegrityTableList = new Array('<?php echo implode('\',\'', $processDataList['checkIntegrity']) ?>'),
                        nbCheckIntegrityTable = checkIntegrityTableList.length,

                        checkAndConvertCharsetAndCollationTableList = new Array('<?php echo implode('\',\'', $processDataList['checkAndConvertCharsetAndCollation']) ?>'),
                        nbCheckAndConvertCharsetAndCollationTable = checkAndConvertCharsetAndCollationTableList.length,
                        processProgress = 100 / (nbProcessTable + nbCheckIntegrityTable + nbCheckAndConvertCharsetAndCollationTable);
<?php
    endif
?>
                    //]]>
                    </script>
                    <a href="#" class="button" id="startProcess" onclick="submit(); return false;"><?php echo $i18n['START'] ?></a>
                </div>