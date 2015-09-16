<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_news`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_news` (
            `id` int(11) NOT NULL auto_increment,
            `cat` varchar(30) NOT NULL default \'\',
            `titre` text,
            `auteur` text,
            `auteur_id` varchar(20) NOT NULL default \'\',
            `texte` text,
            `suite` text,
            `date` varchar(30) NOT NULL default \'\',
            `bbcodeoff` int(1) NOT NULL default \'0\',
            `smileyoff` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('news');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1 (only texte) / 1.7.9 RC6 (texte & suite)
    if (version_compare($this->data['version'], '1.7.9', '<=')
        && array_key_exists('texte', $tableData)
        && array_key_exists('suite', $tableData)
    ) {
        include 'bbcode.php';

        $sql = 'SELECT id, texte, suite FROM `'. $db_prefix .'_news`';
        $dbsNews = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsNews)) {
            $data = array('texte = \''. mysql_real_escape_string(bbcode(stripslashes($row['texte']), $db_prefix, $this->_i18n)) .'\'');

            if ($row['suite'] != '')
                $data[] = 'suite = \''. mysql_real_escape_string(bbcode(stripslashes($row['suite']), $db_prefix, $this->_i18n)) .'\'';

            $sql = 'UPDATE `'. $db_prefix .'_news` SET '. implode(', ', $data) .' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>