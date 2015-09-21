<?php

// install 1.7.9 RC1 (created)
// install 1.7.9 RC6 (removed)

if ($this->data['process'] == 'update') {
    $sql = 'DROP TABLE IF EXISTS  `'. $this->data['db_prefix'] .'_editeur`';
    mysql_query($sql) or die(mysql_error());

    return 'REMOVED';
}

return 'NOTHING_TO_DO';

?>