<?php

// install / update 1.7.9 RC1 (created)
// update 1.7.9 RC6 (removed)
if ($this->data['process'] == 'update') {
    $sql = 'DROP TABLE IF EXISTS  `'. $db_prefix .'_style`';
    mysql_query($sql) or die(mysql_error());

    return 'REMOVED';
}

return 'NOTHING_TO_DO';

?>