<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=selectMultipleCheckField',
    'method' => 'POST',
    'dataName' => 'selectMultipleCheckFieldTest',
    'items' => array(
        'champ1' => array(
            'label'             => 'Champ <em>Select</em>',
            'type'              => 'select',
            'multiple'          => true,
            'size'              => 4,
            'options'           => array(
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9
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