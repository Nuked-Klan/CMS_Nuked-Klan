<?php

/* nkList configuration */

// Define the list of server

function getServerListCfg() {
    return array(
        'sqlQuery' => 'SELECT S.sid, S.ip, S.port, S.game, SC.titre FROM '. SERVER_TABLE .' AS S LEFT JOIN '. SERVER_CAT_TABLE .' AS SC ON SC.cid = S.cat',
        'defaultSortables' => array(
            'order'     => array('S.cat', 'S.ip')
        ),
        'fields' => array(
            'adress'    => array('label' => __('SERVER_IP')),
            'game'      => array('label' => __('SERVER_GAME')),
            'titre'     => array('label' => __('CATEGORY'))
        ),
        'edit' => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getServerFields() {
    return array(
        'game',
        'ip',
        'port',
        'pass',
        'cat'
    );
}

// Definition of editing Server category form

function getServerFormCfg() {
    return array(
        'items' => array(
            'ip' => array(
                'label'             => __('SERVER_IP'),
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'port' => array(
                'label'             => __('SERVER_PORT'),
                'type'              => 'text',
                'size'              => 5,
                'maxlength'         => 5,
                'dataType'          => 'text',
                //'required'          => true
            ),
            'game' => array(
                'label'             => __('SERVER_GAME'),
                'type'              => 'select',
                'options'           => array(
                    'CSS'     => 'CS:Source',
                    'HL2'     => 'Half-life 2',
                    'HL'      => 'Half-life',
                    'DOOM3'   => 'Doom 3',
                    'FARCRY'  => 'Far Cry',
                    'Q3'      => 'Quake 3',
                    'MOHAA'   => 'MOHAA',
                    'RTCW'    => 'RTCW',
                    'COD'     => 'COD',
                    'UT'      => 'UT',
                    'UT2003'  => 'UT2003',
                    'UT2004'  => 'UT2004',
                    'IGI2'    => 'IGI2',
                    'NWN'     => 'Neverwinter Nights',
                    'AA'      => 'America\'s Army',
                    'BTF1942' => 'Battlefield 1942'
                )
            ),
            'pass' => array(
                'label'             => __('SERVER_PASSWORD'),
                'type'              => 'text',
                'size'              => 10,
                'dataType'          => 'text',
                'required'          => true
            ),
            'cat' => array(
                'label'             => __('CATEGORY'),
                'type'              => 'select',
                'options'           => array()
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('ADD_THIS_SERVER', 'MODIFY_THIS_SERVER'),
                'inputClass'        => array('button')
            )
        )
    );
}


?>