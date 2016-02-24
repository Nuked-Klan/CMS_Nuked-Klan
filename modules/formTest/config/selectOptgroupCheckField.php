<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=selectOptgroupCheckField',
    'method' => 'POST',
    'dataName' => 'selectOptgroupCheckFieldTest',
    'items' => array(
        'champ1' => array(
            'label'             => 'Champ <em>Select</em>',
            'type'              => 'select',
            'options'           => array(
                'start-optgroup1' => 'Visiteur',
                0 => 0,
                'end-optgroup1' => true,
                'start-optgroup2' => 'Membre',
                1 => 1,
                'end-optgroup2' => true,
                'start-optgroup3' => 'Administrateur',
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9,
                'end-optgroup3' => true
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