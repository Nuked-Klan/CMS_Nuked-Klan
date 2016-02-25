<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=radioInlineCheckField',
    'method' => 'POST',
    'dataName' => 'radioInlineCheckFieldTest',
    'items' => array(
        'champ1' => array(
            'fakeLabel'         => 'Champ <em>radio</em>',
            'type'              => 'radio',
            'subType'           => 'inline',
            'data'              => array(
                'choix1'            => 'Choix 1',
                'choix2'            => 'Choix 2'
            )
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