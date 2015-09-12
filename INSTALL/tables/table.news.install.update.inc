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
    $update = false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
        $sql = 'SELECT id, texte, suite FROM `'. $db_prefix .'_news`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_row($req)) {
            $row['texte'] = mysql_real_escape_string(bbcode(stripslashes($row['texte']), $db_prefix));

            $sqlSuite = '';

            if ($row['suite'] != '') {
                $row['suite'] = mysql_real_escape_string(bbcode(stripslashes($row['suite']), $db_prefix));
                $sqlSuite = ', suite = \''. $row['suite'] .'\'';
            }

            $sql = 'UPDATE `'. $db_prefix .'_news` SET texte = \''. $row['texte'] .'\''. $sqlSuite .' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>