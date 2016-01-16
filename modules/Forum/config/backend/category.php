<?php

/* nkList configuration */

// Define the list of forum category
$forumCatList = array(
    'css' => array('tablePrefix' => 'forumCat', 'fieldsPrefix' => 'c'),
    'sqlQuery' => 'SELECT id, nom, ordre FROM '. FORUM_CAT_TABLE,
    'defaultSortables' => array(
        'order'     => array('ordre', 'nom')
    ),
    'fields' => array(
        'nom'       => array('label' => _CAT),
        'ordre'     => array('label' => _ORDER)
    ),
    'edit' => array(
        'op'                => 'editCat',
        'imgTitle'          => _EDITTHISCAT
    ),
    'delete' => array(
        'op'                => 'deleteCat',
        'imgTitle'          => _DELTHISCAT,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'nom'
    ),
    'emptytable' => _NOCATINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatForumCatRow'
    )
);

/* nkForm configuration */

// List of fields to update
$forumCatField = array(
    'nom',
    'image',
    'niveau',
    'ordre'
);

// Definition of editing forum category form
$forumCatForm = array(
    'checkform' => true,
    'id'        => 'editForumCatForm',
    'action'    => 'index.php?file=Forum&amp;page=admin&amp;op=saveCat',
    'method'    => 'post',
    'labelFormat' => '%s : ',
    'items' => array(
        'htmlCategoryImage' => '',
        'nom' => array(
            'label'             => _NAME,
            'type'              => 'text',
            'name'              => 'nom',
            'size'              => 30,
            'dataType'          => 'text',
            'required'          => true,
            'noempty'           => true
        ),
        'image' => array(
            'label'             => _IMAGE,
            'type'              => 'text',
            'name'              => 'urlImageCat',
            'size'              => 42
        ),
        'upImageCat' => array(
            'label'             => _UPLOADIMAGE,
            'type'              => 'file',
            'name'              => 'upImageCat'
        ),
        'niveau' => array(
            'label'             => _NIVEAU,
            'type'              => 'select',
            'name'              => 'niveau',
            'options'           => array(
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9
            )
        ),
        'ordre' => array(
            'label'             => _ORDER,
            'type'              => 'text',
            'name'              => 'ordre',
            'value'             => '0',
            'size'              => 2,
            'dataType'          => 'numeric',
            'required'          => true,
            'noempty'           => true
        )
    ),
    'itemsFooter' => array(
        'submit' => array(
            'type'              => 'submit',
            'name'              => 'submit',
            'value'             => _CREATECAT,
            'inputClass'        => array('button')
        ),
        'backlink' => array(
            'html'              => '<a class="buttonLink" href="index.php?file=Forum&amp;page=admin&amp;op=main_cat">'. __('BACK') .'</a>'
        )
    )
);

?>