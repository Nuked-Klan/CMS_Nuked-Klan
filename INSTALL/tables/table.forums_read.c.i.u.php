<?php
/**
 * table.forums_read.c.i.u.php
 *
 * `[PREFIX]_forums_read` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_forums_read');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$forumReadTableCfg = array(
    'fields' => array(
        'user_id'   => array('type' => 'varchar(20)', 'null' => false, 'default' => '\'\''),
        'thread_id' => array('type' => 'text',        'null' => false),
        'forum_id'  => array('type' => 'text',        'null' => false),
    ),
    'primaryKey' => array('user_id'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity(array(null, 'id'), 'user_id', 'thread_id', 'forum_id');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop')
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install')
    $dbTable->createTable($forumReadTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // update 1.7.9 RC6
    if ($dbTable->fieldExist('id')) {
        // Read forum read data and store it in PHP session
        if (! isset($this->_session['step'])) {
            $sql = 'SELECT *
                FROM `'. $this->_session['db_prefix'] .'_forums_read`';

            $dbsForumRead = $this->_db->selectMany($sql);

            if (count($dbsForumRead) == 0) {
                $dbTable->createTable($forumReadTableCfg);
                $dbTable->setJqueryAjaxResponse('UPDATED');
                return;
            }

            $this->_session['forumReadData'] = array();

            foreach ($dbsForumRead as $row) {
                if (! array_key_exists($row['user_id'], $this->_session['forumReadData'])) {
                    $this->_session['forumReadData'][$row['user_id']] = array(
                        'thread_id' => ',',
                        'forum_id'  => ','
                    );
                }

                if (strrpos($this->_session['forumReadData'][$row['user_id']]['thread_id'], ','. $row['thread_id'] .',') === false)
                    $this->_session['forumReadData'][$row['user_id']]['thread_id'] .= $row['thread_id'] .',';

                if (strrpos($this->_session['forumReadData'][$row['user_id']]['forum_id'], ','. $row['forum_id'] .',') === false)
                    $this->_session['forumReadData'][$row['user_id']]['forum_id'] .= $row['forum_id'] .',';
            }

            $this->_session['nbTableEntries'] = count($this->_session['forumReadData']);
            $this->_session['step']           = 2;

            // TODO ADD ACTION LIST

            $dbTable->setJqueryAjaxResponse('STEP_1_TOTAL_STEP_4');
        }
        // Create temporary table
        else if ($this->_session['step'] == 2) {
            $dbTable->setTable($this->_session['db_prefix'] .'_forums_read_tmp');
            $dbTable->createTable($forumReadTableCfg);
            $this->_session['step'] = 3;

            $dbTable->setJqueryAjaxResponse('STEP_2_TOTAL_STEP_4');
        }
        // Insert forum read data in temporary table
        else if ($this->_session['step'] == 3) {
            if (! isset($this->_session['offset']))
                $this->_session['offset'] = 0;
            else
                $this->_session['offset'] = $this->_session['offset'] + 400;

            for ($c = 0; $c < 2; $c++) {
                $i      = 0;
                $insert = array();

                foreach ($this->_session['forumReadData'] as $userId => $userData) {
                    if ($i < $this->_session['offset']) {
                        $insert[] = '(\''. $userId .'\', \''. $userData['forum_id'] .'\', \''. $userData['thread_id'] .'\')';

                        if ($i == $this->_session['offset'] + 200) break;
                    }

                    $i++;
                }

                $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_forums_read_tmp`
                    (user_id, forum_id, thread_id) VALUES '. implode(', ', $insert);

                $dbTable->insertData(array('ADD_FORUM_READ_DATA', array()), $sql);

                // TODO ADD ACTION LIST
            }

            if ($this->_session['offset'] + 400 < $this->_session['nbTableEntries']) {
                $dbTable->setJqueryAjaxResponse('STEP_'. ($this->_session['offset'] + 400) .'_TOTAL_STEP_'. $this->_session['nbTableEntries']);
            }
            else {
                unset($this->_session['offset']);
                unset($this->_session['nbTableEntries']);
                unset($this->_session['forumReadData']);

                $dbTable->setJqueryAjaxResponse('STEP_3_TOTAL_STEP_4');
            }
        }
        // Drop old forum read table and rename temporary table
        else if ($this->_session['step'] == 4) {
            $dbTable->dropTable($this->_session['db_prefix'] .'_forums_read');

            $dbTable->setTable($this->_session['db_prefix'] .'_forums_read_tmp');
            $dbTable->renameTable($this->_session['db_prefix'] .'_forums_read');

            unset($this->_session['step']);

            $dbTable->setJqueryAjaxResponse('UPDATED');
        }
    }
}

?>