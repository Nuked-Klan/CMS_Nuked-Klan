<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_forums_options`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_forums_options` (
            `id` int(11) NOT NULL default \'0\',
            `poll_id` int(11) NOT NULL default \'0\',
            `option_text` varchar(255) NOT NULL default \'\',
            `option_vote` int(11) NOT NULL default \'0\',
            KEY `poll_id` (`poll_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>