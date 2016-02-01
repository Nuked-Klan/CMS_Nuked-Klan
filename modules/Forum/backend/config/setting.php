<?php

/* nkForm configuration */

// List of fields to update

function getForumSettingFields() {
    return array(
        'forum_title',
        'forum_desc',
        'forum_rank_team',
        'forum_image',
        'forum_birthday',
        'forum_gamer_details',
        'forum_user_details',
        'forum_labels_active',
        'forum_display_modos',
        'thread_forum_page',
        'mess_forum_page',
        'hot_topic',
        'post_flood',
        'forum_field_max',
        'forum_file',
        'forum_file_level',
        'forum_file_maxsize'
    );
}

// Definition of editing forum setting form

function getForumSettingFormCfg() {
    return array(
        'checkform' => true,
        'items' => array(
            'forum_title' => array(
                'label'             => __('FORUM_TITLE'),
                'type'              => 'text',
                'size'              => 40
            ),
            'forum_desc' => array(
                'label'             => __('FORUM_DESCRIPTION'),
                'type'              => 'textarea',
                'cols'              => 55,
                'rows'              => 5
            ),
            'forum_rank_team' => array(
                'label'             => __('USE_RANK_TEAM'),
                'type'              => 'checkbox',
                'inputValue'        => 'on',
                'defaultValue'      => 'off'
            ),
            'forum_image' => array(
                'label'             => __('DISPLAY_FORUM_IMAGE'),
                'type'              => 'checkbox',
                'inputValue'        => 'on',
                'defaultValue'      => 'off'
            ),
            'forum_cat_image' => array(
                'label'             => __('DISPLAY_CATEGORY_IMAGE'),
                'type'              => 'checkbox',
                'inputValue'        => 'on',
                'defaultValue'      => 'off'
            ),
            'forum_birthday' => array(
                'label'             => __('DISPLAY_BIRTHDAY'),
                'type'              => 'checkbox',
                'inputValue'        => 'on',
                'defaultValue'      => 'off'
            ),
            'forum_gamer_details' => array(
                'label'             => __('DISPLAY_GAMER_DETAILS'),
                'type'              => 'checkbox',
                'inputValue'        => 'on',
                'defaultValue'      => 'off'
            ),
            'forum_user_details' => array(
                'label'             => __('DISPLAY_USER_DETAILS'),
                'type'              => 'checkbox',
                'inputValue'        => 'on',
                'defaultValue'      => 'off'
            ),
            'forum_labels_active' => array(
                'label'             => __('DISPLAY_LABELS'),
                'type'              => 'checkbox',
                'inputValue'        => 'on',
                'defaultValue'      => 'off'
            ),
            'forum_display_modos' => array(
                'label'             => __('DISPLAY_MODERATORS'),
                'type'              => 'checkbox',
                'inputValue'        => 'on',
                'defaultValue'      => 'off'
            ),
            'thread_forum_page' => array(
                'label'             => __('NUMBER_THREAD'),
                'type'              => 'text',
                'size'              => 2,
                'dataType'          => 'numeric',
                'required'          => true,
                'noempty'           => true
            ),
            'mess_forum_page' => array(
                'label'             => __('NUMBER_POST'),
                'type'              => 'text',
                'size'              => 2,
                'dataType'          => 'numeric',
                'required'          => true,
                'noempty'           => true
            ),
            'hot_topic' => array(
                'label'             => __('TOPIC_HOT'),
                'type'              => 'text',
                'size'              => 2,
                'dataType'          => 'numeric',
                'required'          => true,
                'noempty'           => true
            ),
            'post_flood' => array(
                'label'             => __('POST_FLOOD'),
                'type'              => 'text',
                'size'              => 2,
                'dataType'          => 'numeric',
                'required'          => true,
                'noempty'           => true
            ),
            'forum_field_max' => array(
                'label'             => __('MAX_SURVEY_FIELD'),
                'type'              => 'text',
                'size'              => 2,
                'dataType'          => 'numeric',
                'required'          => true,
                'noempty'           => true
            ),
            'forum_file' => array(
                'label'             => __('JOINED_FILES'),
                'type'              => 'checkbox',
                'inputValue'        => 'on',
                'defaultValue'      => 'off'
            ),
            'forum_file_level' => array(
                'label'             => __('FILE_LEVEL'),
                'type'              => 'select',
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
            'forum_file_maxsize' => array(
                'label'             => __('MAX_SIZE_FILE'),
                'type'              => 'text',
                'size'              => 6,
                'dataType'          => 'numeric',
                'required'          => true,
                'noempty'           => true
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => __('SEND'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>