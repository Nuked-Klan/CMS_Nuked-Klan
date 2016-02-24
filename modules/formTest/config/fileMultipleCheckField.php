<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=fileMultipleCheckField',
    'method' => 'POST',
    'dataName' => 'fileMultipleCheckFieldTest',
    'items' => array(
        'champ1' => array(
            'label'             => 'Upload 1',
            'name'              => 'fichier[]',
            'type'              => 'file',
            'fileType'          => 'no-html-php',
            'uploadDir'         => 'modules/formTest/upload',
            'multiple'          => true
        ),
        'champ2' => array(
            'label'             => 'Upload 2',
            'name'              => 'fichier[]',
            'type'              => 'file',
            'fileType'          => 'no-html-php',
            'uploadDir'         => 'modules/formTest/upload',
            'multiple'          => true
        ),
        'champ3' => array(
            'label'             => 'Upload 3',
            'name'              => 'fichier[]',
            'type'              => 'file',
            'fileType'          => 'no-html-php',
            'uploadDir'         => 'modules/formTest/upload',
            'multiple'          => true
        ),
    ),
    'itemsFooter' => array(
        'submit' => array(
            'type'              => 'submit',
            'value'             => 'Envoyer',
        )
    )
);

?>