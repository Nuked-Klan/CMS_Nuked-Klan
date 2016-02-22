<?php

$form = array(
    'action' => 'index.php?file=formTest&amp;op=doCheckField&amp;form=textareaCheckField',
    'method' => 'POST',
    'dataName' => 'allFieldTest',
    'items' => array(
        'comment' => array(
            'label'             => 'Champ <em>Textarea</em>',
            'type'              => 'textarea',
            'cols'              => 69,
            'rows'              => 10,
            'inputClass'        => array('editor'),
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