<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_news`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_news` (
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

    /*
     * Callback function for update row of news database table
     */
    function updateNewsRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList)) {
            $setFields['texte'] = $vars['bbcode']->apply(stripslashes($row['texte']));

            if ($row['suite'] != '')
                $setFields['suite'] = $vars['bbcode']->apply(stripslashes($row['suite']));
        }

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_news');

    // Update BBcode
    // update 1.7.9 RC1 (only texte) / 1.7.9 RC6 (texte & suite)
    if (version_compare($this->data['version'], '1.7.9', '<=')
        && $udt->fieldExist('texte')
        && $udt->fieldExist('suite')
    ) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', array('texte', 'suite'));
    }

    return $udt->execute('id', 'updateNewsRow');
}

?>