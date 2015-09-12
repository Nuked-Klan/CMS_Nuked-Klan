<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_sections`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_sections` (
        `artid` int(11) NOT NULL auto_increment,
        `secid` int(11) NOT NULL default \'0\',
        `title` text NOT NULL,
        `content` text NOT NULL,
        `autor` text NOT NULL,
        `autor_id` varchar(20) NOT NULL default \'\',
        `counter` int(11) NOT NULL default \'0\',
        `bbcodeoff` int(1) NOT NULL default \'0\',
        `smileyoff` int(1) NOT NULL default \'0\',
        `date` varchar(12) NOT NULL default \'\',
        PRIMARY KEY  (`artid`),
        KEY `secid` (`secid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
        $sql = 'SELECT artid, content FROM `'. $db_prefix .'_sections`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_row($req)) {
            $row['content'] = mysql_real_escape_string(bbcode(stripslashes($row['content']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_sections` SET content = \''. $row['content'] .'\' WHERE artid = \''. $row['artid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>