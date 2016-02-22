<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=emailCheckField',
    'method' => 'POST',
    'dataName' => 'emailCheckFieldTest',
    'items' => array(
        'champ1Notification' => array(
            'html'              => printNotification('Le champ suivant doit contenir un <em>email</em>', 'information', array(), true)
        ),
        'champ1' => array(
            'label'             => 'Champ',
            'type'              => 'text',
            'size'              => 30,
            'dataType'          => 'email',
            'required'          => true
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