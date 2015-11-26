<?php

/* nkList configuration */

// Define the list of survey
$surveyList = array(
    'classPrefix' => 'survey',
    'sqlQuery' => 'SELECT sid, titre, date, niveau FROM ' . SURVEY_TABLE,
    'defaultSortables' => array(
        'order'     => array('sid'),
        'dir'       => array('DESC')
    ),
    'fields' => array(
        'titre'     => array('label' => _TITLE),
        'date'      => array('label' => _DATE),
        'niveau'    => array('label' => _LEVEL)
    ),
    'edit' => array(
        'op'                => 'editSurvey',
        'text'              => _EDITTHISPOLL
    ),
    'delete' => array(
        'op'                => 'deleteSurvey',
        'text'              => _DELTHISPOLL,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'titre'
    ),
    'emptytable' => _NOPOOL,
    'callbackRowFunction' => array('functionName' => 'formatSurveyRow')
);

?>