<?php

$dbTable->setTable($this->_session['db_prefix'] .'_editeur');

// install 1.7.9 RC1 (created)
// install 1.7.9 RC6 (removed)

if ($process == 'update' && $dbTable->tableExist())
    $dbTable->dropTable();

?>