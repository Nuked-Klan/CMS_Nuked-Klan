<?php

if ($this->data['process'] == 'update') {
    $sql = 'DROP TABLE IF EXISTS  `'. $db_prefix .'_style`';
    mysql_query($sql) or die(mysql_error());

    return 'REMOVED';
}

return 'NOTHING_TO_DO';

?>