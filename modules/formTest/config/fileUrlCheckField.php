<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=fileUrlCheckField',
    'method' => 'POST',
    'dataName' => 'fileUrlCheckFieldTest',
    'items' => array(
        'champ1' => array(
            'label'             => 'Upload',
            'type'              => 'file',
            'allowedExtension'  => array('jpg', 'jpeg', 'png', 'gif'),
            'uploadDir'         => 'modules/formTest/upload',
            'urlField'          => 'champ2'
        ),
        'champ2' => array(
            'label'             => 'Url',
            'type'              => 'text',
            'size'              => 30
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
