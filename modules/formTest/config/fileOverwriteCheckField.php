<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=fileOverwriteCheckField',
    'method' => 'POST',
    'dataName' => 'fileOverwriteCheckFieldTest',
    'items' => array(
        'champ1' => array(
            'label'             => 'Upload',
            'type'              => 'file',
            'allowedExtension'  => array('jpg', 'jpeg', 'png', 'gif'),
            'uploadDir'         => 'modules/formTest/upload',
            'overwrite'         => false,
            'overwriteField'    => 'champ2',
            'required'          => true
        ),
        'champ2' => array(
            'label'             => 'Remplacer ?',
            'type'              => 'checkbox',
            'inputValue'        => 'on',
            'defaultValue'      => 'off'
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
