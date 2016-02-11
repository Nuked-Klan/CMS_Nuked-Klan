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
function addAuthorIdForeignKey($name, $authorId = 'authorId') {
    global $dbTable;

    $dbTable->addForeignKey(
        'FK_'. $name .'_authorId', $authorId,
        USER_TABLE, 'id',
        array('ON DELETE SET NULL')
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