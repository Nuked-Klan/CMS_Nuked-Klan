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
    $tableData  = $this->getTableData('sections');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('content', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT artid, content FROM `'. $db_prefix .'_sections`';
        $dbsSections = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsSections)) {
            $row['content'] = mysql_real_escape_string(bbcode(stripslashes($row['content']), $db_prefix, $this->_i18n));

            $sql = 'UPDATE `'. $db_prefix .'_sections` SET content = \''. $row['content'] .'\' WHERE artid = \''. $row['artid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>