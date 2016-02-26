<?php

/* nkList configuration */

// Define the list of game

function getGameListCfg() {
    return array(
        'sqlQuery' => 'SELECT id, name FROM '. GAMES_TABLE,
        'defaultSortables' => array(
            'order'     => array('name')
        ),
        'fields' => array(
            'name'      => array('label' => __('NAME'))
        ),
        'edit'   => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getGameFields() {
    return array(
        'name',
        'titre',
        'icon',
        'pref_1',
        'pref_2',
        'pref_3',
        'pref_4',
        'pref_5',
        'map'
    );
}

// Definition of editing game form

function getGameFormCfg() {
    return array(
        'items' => array(
            'name' => array(
                'label'             => __('NAME'),
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'icon' => array(
                'label'             => __('ICON'),
                'type'              => 'text',
                'size'              => 49,
                'html'              => '&nbsp<a class="buttonLink" href="#" onclick="javascript:window.open(\'index.php?admin=Game&amp;op=showIcon\',\''. __('ICON') .'\',\'toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=300,height=125,top=30,left=0\');return(false)">'. __('SEE_ICON') .'</a>',
                'dataType'          => 'text',
                'required'          => true
            ),
            'titre' => array(
                'label'             => __('TITLE'),
                'type'              => 'text',
                'size'              => 50,
                'dataType'          => 'text',
                'required'          => true
            ),
            'pref_1' => array(
                'label'             => __('PREFERENCE') .' 1',
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'pref_2' => array(
                'label'             => __('PREFERENCE') .' 2',
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'pref_3' => array(
                'label'             => __('PREFERENCE') .' 3',
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'pref_4' => array(
                'label'             => __('PREFERENCE') .' 4',
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'pref_5' => array(
                'label'             => __('PREFERENCE') .' 5',
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'mapInput' => array(
                'label'             => __('MAP'),
                'type'              => 'text',
                'size'              => 30,
                'html'              => '<input id="addMap" class="button" type="button" value="'. __('ADD_MAP') .'" />',
            ),
            'htmlMapList' => '',
            'htmlMapListButtons' => '<div id="resetMapListButton" class="nkFormRow"><input id="resetMapList" class="button" type="button" value="'. __('DELETE_ALL_MAP') .'" /></div>',
            'map' => array(
                'type'              => 'hidden',
                'id'                => 'map'
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('ADD_THIS_GAME', 'MODIFY_THIS_GAME'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>