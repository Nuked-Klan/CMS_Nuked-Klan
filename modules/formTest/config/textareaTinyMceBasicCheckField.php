<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=textareaTinyMceBasicCheckField',
    'method' => 'POST',
    'dataName' => 'textareaTinyMceBasicCheckFieldTest',
    'items' => array(
        'comment' => array(
            'label'             => 'Champ <em>Textarea</em>',
            'type'              => 'textarea',
            'subType'           => 'basic',
            'cols'              => 69,
            'rows'              => 10,
            'dataType'          => 'html'
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