<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_forums`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_forums` (
            `id` int(5) NOT NULL auto_increment,
            `cat` int(11) NOT NULL default \'0\',
            `nom` text NOT NULL,
            `comment` text NOT NULL,
            `moderateurs` text NOT NULL,
            `niveau` int(1) NOT NULL default \'0\',
            `level` int(1) NOT NULL default \'0\',
            `ordre` int(5) NOT NULL default \'0\',
            `level_poll` int(1) NOT NULL default \'0\',
            `level_vote` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_forums` VALUES
        (1, 1, \''. mysql_real_escape_string($this->_i18n['FORUM']) .'\', \''. mysql_real_escape_string($this->_i18n['TEST_FORUM']) .'\', \'\', 0, 0, 0, 1 ,1);';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of _forums database table
     */
    function updateForumsRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('REMOVE_EDITOR', $updateList))
            $setFields['comment'] = str_replace(array('<p>', '</p>'), '', $row['comment']);

        if (in_array('BBCODE', $updateList))
            $setFields['comment'] = $vars['bbcode']->apply(stripslashes($row['comment']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_forums');

    // TODO : Version ???
    if (version_compare($this->data['version'], '1.7.9', '=') && $udt->fieldExist('comment'))
        $udt->updateFieldData('REMOVE_EDITOR', 'comment');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('comment')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'comment');
    }

    return $udt->execute('id', 'updateForumsRow');
}

?>