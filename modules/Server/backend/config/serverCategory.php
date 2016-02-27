<?php

/* nkList configuration */

// Define the list of Server category

function getServerCategoryListCfg() {
    return array(
        'sqlQuery' => 'SELECT cid, titre FROM '. SERVER_CAT_TABLE,
        'defaultSortables' => array(
            'order'     => array('titre')
        ),
        'fields' => array(
            'titre'       => array('label' => __('CATEGORY'))
        ),
        'edit'     => array(),
        'delete'   => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getServerCategoryFields() {
    return array(
        'titre',
        'description'
    );
}

// Definition of editing Server category form

function getServerCategoryFormCfg() {
    return array(
        'items' => array(
            'titre' => array(
                'label'             => __('TITLE'),
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'description' => array(
                'label'             => __('DESCRIPTION'),
                'type'              => 'textarea',
                'subType'           => 'advanced',
                'cols'              => 60,
                'rows'              => 10,
                'dataType'          => 'html'
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('CREATE_CATEGORY', 'MODIFY_THIS_CATEGORY'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>