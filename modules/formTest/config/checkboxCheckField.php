<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=checkboxCheckField',
    'method' => 'POST',
    'dataName' => 'checkboxCheckFieldTest',
    'items' => array(
        'champ1' => array(
            'label'             => 'Champ <em>checkbox</em>',
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