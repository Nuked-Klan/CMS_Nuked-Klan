<?php
/**
 * selectForumOptions.php
 *
 * Configuration for generate Forum list of input select options.
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

/**
 * Get Forum list options.
 *
 * @param array $params : The input data.
 * @return array : The Forum list for input select option.
 */
if (! function_exists('getForumOptions')) {
    function getForumOptions($params = array()) {
        $options = array();

        $dbrForumCat = nkDB_selectMany(
            'SELECT id, nom
            FROM '. FORUM_CAT_TABLE,
            array('ordre', 'nom')
        );

        if (array_key_exists('categoryOptgroup', $params) && $params['categoryOptgroup']) {
            foreach ($dbrForumCat as $forumCat) {
                $options['start-optgroup-cat_'. $forumCat['id']] = printSecuTags($forumCat['nom']);

                $dbrForum = nkDB_selectMany(
                    'SELECT id, nom
                    FROM '. FORUM_TABLE .'
                    WHERE cat = '. nkDB_quote($forumCat['id']),
                    array('ordre', 'nom')
                );

                foreach ($dbrForum as $forum)
                    $options[$forum['id']] = printSecuTags($forum['nom']);

                $options['end-optgroup-cat_'. $forumCat['id']] = true;
            }
        }
        else {
            $options[''] = __('ALL');

            foreach ($dbrForumCat as $forumCat) {
                $options['cat_'. $forumCat['id']] = '* '. printSecuTags($forumCat['nom']);

                $dbrForum = nkDB_selectMany(
                    'SELECT id, nom
                    FROM '. FORUM_TABLE .'
                    WHERE cat = '. nkDB_quote($forumCat['id']),
                    array('ordre', 'nom')
                );

                foreach ($dbrForum as $forum)
                    $options[$forum['id']] = '&nbsp;&nbsp;&nbsp;'. printSecuTags($forum['nom']);
            }
        }

        return $options;
    }
}

return array(
    'functionName' => 'getForumOptions'
);

?>
