<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=commonCheckField',
    'method' => 'POST',
    'dataName' => 'commonCheckFieldTest',
    'items' => array(
        'champ1Notification' => array(
            'html'              => printNotification('Le champ suivant doit avoir 4 caractêre minimum', 'information', array(), true)
        ),
        'champ1' => array(
            'label'             => 'Champ',
            'type'              => 'text',
            'size'              => 30,
            'dataType'          => 'text',
            'minlength'         => 4,
            'required'          => true
        ),
        'champ2Notification' => array(
            'html'              => printNotification('Le champ suivant doit avoir 20 caractêre maximum', 'information', array(), true)
        ),
        'champ2' => array(
            'label'             => 'Champ',
            'type'              => 'textarea',
            'size'              => 30,
            'dataType'          => 'text',
            'maxlength'         => 20,
            'required'          => true
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