<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=passwordCheckField',
    'method' => 'POST',
    'dataName' => 'passwordCheckFieldTest',
    'items' => array(
        'champ1' => array(
            'label'             => 'Mot de passe',
            'type'              => 'password',
            'size'              => 30,
            'dataType'          => 'password',
            'required'          => true,
            'passwordCheck'     => true,
            'passwordConfirmId' => 'pcftChamp2'
        ),
        'champ2' => array(
            'label'             => 'Mot de passe (confirmation)',
            'type'              => 'password',
            'size'              => 30
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