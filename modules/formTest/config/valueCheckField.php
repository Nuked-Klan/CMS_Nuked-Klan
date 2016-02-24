<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=valueCheckField',
    'method' => 'POST',
    'dataName' => 'valueCheckFieldTest',
    'items' => array(
        'champ1Notification' => array(
            'html'              => printNotification('Le champ suivant doit contenir une chaîne <em>alphanumérique</em>', 'information', array(), true)
        ),
        'champ1' => array(
            'label'             => 'Champ',
            'type'              => 'text',
            'size'              => 30,
            'dataType'          => 'alphanumeric',
            'required'          => true
        ),
        'champ2Notification' => array(
            'html'              => printNotification('Le champ suivant doit contenir une chaîne <em>alphabétique</em>', 'information', array(), true)
        ),
        'champ2' => array(
            'label'             => 'Champ',
            'type'              => 'text',
            'size'              => 30,
            'dataType'          => 'alpha',
            'required'          => true
        ),
        'champ3Notification' => array(
            'html'              => printNotification('Le champ suivant doit contenir un <em>entier</em>', 'information', array(), true)
        ),
        'champ3' => array(
            'label'             => 'Champ',
            'type'              => 'text',
            'size'              => 30,
            'dataType'          => 'integer',
            'required'          => true
        ),
        'champ4Notification' => array(
            'html'              => printNotification('Le champ suivant doit contenir une <em>chaîne de caractère</em>', 'information', array(), true)
        ),
        'champ4' => array(
            'label'             => 'Champ',
            'type'              => 'text',
            'size'              => 30,
            'dataType'          => 'text',
            'required'          => true
        ),
        'champ5Notification' => array(
            'html'              => printNotification('Le champ suivant doit contenir du <em>code HTML</em>', 'information', array(), true)
        ),
        'champ5' => array(
            'label'             => 'Champ',
            'type'              => 'textarea',
            'cols'              => 69,
            'rows'              => 10,
            'id'                => 'e_advanced',
            'dataType'          => 'html',
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