<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=dateCheckField',
    'method' => 'POST',
    'dataName' => 'dateCheckFieldTest',
    'items' => array(
        'champ1Notification' => array(
            'html'              => printNotification('Le champ suivant doit contenir un <em>date</em>', 'information', array(), true)
        ),
        'champ1' => array(
            'label'             => 'Champ',
            'type'              => 'date',
            'dataType'          => 'date',
            'required'          => true,
            'options'           => array(
                'buttonText'        => 'Afficher jQuery-UI Datepicker'
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