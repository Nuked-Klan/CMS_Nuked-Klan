<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=fileCheckField',
    'method' => 'POST',
    'dataName' => 'fileCheckFieldTest',
    'items' => array(
        'champ1' => array(
            'label'             => 'Upload',
            'type'              => 'file',
            'allowedExtension'  => array('jpg', 'jpeg', 'png', 'gif'),
            'uploadDir'         => 'modules/formTest/upload'
        )
    ),
    'itemsFooter' => array(
        'submit' => array(
            'type'              => 'submit',
            'value'             => 'Envoyer',
        )
    )
);

?>
