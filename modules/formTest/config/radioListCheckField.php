<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=radioListCheckField',
    'method' => 'POST',
    'dataName' => 'radioListCheckFieldTest',
    'items' => array(
        'champ1' => array(
            'fakeLabel'         => 'Champ <em>radio</em>',
            'type'              => 'radio',
            'subType'           => 'list',
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