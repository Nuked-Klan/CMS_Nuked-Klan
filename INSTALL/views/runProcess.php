                <h1><?php echo ($process == 'install') ? $i18n['CREATE_DB'] : $i18n['UPDATE_DB'] ?></h1>
                <div id="log"><?php echo $i18n['WAITING'] ?></div>
                <div class="progress"><span class="progressBar"></span></div>
                <script type="text/javascript">
                //<![CDATA[
                var process = '<?php echo $process ?>',
                    dbPrefix = '<?php echo $db_prefix ?>',
                    processTableList = new Array('<?php echo implode('\',\'', $processDataList['processList']) ?>'),
                    nbProcessTable = processTableList.length,
                    tableWithForeignKeyList = new Array('<?php echo implode('\',\'', $processDataList['tableWithForeignKey']) ?>'),
                    nbTableWithForeignKey = tableWithForeignKeyList.length;
<?php
    if ($process == 'install') :
?>
                var processProgress = 100 / (nbProcessTable * 2 + nbTableWithForeignKey);
<?php
    elseif ($process == 'update') :
?>
                var checkIntegrityTableList = new Array('<?php echo implode('\',\'', $processDataList['checkIntegrity']) ?>'),
                    nbCheckIntegrityTable = checkIntegrityTableList.length,

                    checkAndConvertCharsetAndCollationTableList = new Array('<?php echo implode('\',\'', $processDataList['checkAndConvertCharsetAndCollation']) ?>'),
                    nbCheckAndConvertCharsetAndCollationTable = checkAndConvertCharsetAndCollationTableList.length,
                    processProgress = 100 / (nbProcessTable + nbCheckIntegrityTable + nbCheckAndConvertCharsetAndCollationTable + nbTableWithForeignKey);
<?php
    endif
?>
                //]]>
                </script>
                <div id="links">
                    <a href="#" id="startProcess"><?php echo $i18n['START'] ?></a>
                </div>