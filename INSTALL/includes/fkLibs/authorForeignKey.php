<?php

/*
 * Read User data in database table
 */
function getUserData($userId) {
    global $db;

    return $db->selectOne(
        'SELECT `pseudo`
        FROM `'. USER_TABLE .'`
        WHERE id = \''. $userId .'\''
    );
}

/*
 * Add author Id foreign key of action database table
 */
function addAuthorIdForeignKey($name, $authorId = 'authorId', $keepUserId = true) {
    global $dbTable;

    if ($keepUserId)
        $refOptions = array('ON DELETE SET NULL');
    else
        $refOptions = array('ON DELETE CASCADE');

    $dbTable->addForeignKey(
        'FK_'. $name .'_authorId', $authorId,
        USER_TABLE, 'id',
        $refOptions
    );
}

/*
 * Add author Id foreign key of action database table
 */
function addAuthorForeignKey($name, $author = 'author') {
    global $dbTable;

    $dbTable->addForeignKey(
        'FK_'. $name .'_author', $author,
        USER_TABLE, 'pseudo',
        array('ON UPDATE CASCADE')
    );
}

?>