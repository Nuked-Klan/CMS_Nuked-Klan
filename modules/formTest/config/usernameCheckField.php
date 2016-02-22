<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=usernameCheckField',
    'method' => 'POST',
    'dataName' => 'usernameCheckFieldTest',
    'items' => array(
        'champ1Notification' => array(
            'html'              => printNotification('Le champ suivant doit contenir un <em>pseudo</em>', 'information', array(), true)
        ),
        'champ1' => array(
            'label'             => 'Champ',
            'type'              => 'text',
            'size'              => 30,
            'dataType'          => 'username',
            'required'          => true
        ),
        'champ2Notification' => array(
            'html'              => printNotification('Idem mais avec un ancien pseudo', 'information', array(), true)
        ),
        'champ2' => array(
            'label'             => 'Champ',
            'type'              => 'text',
            'size'              => 30,
            'value'             => 'Zebulon',
            'dataType'          => 'username',
            'oldUsername'       => true,
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