<?php

$arrayNav = array(
    array(
        'link'   => 'index.php?file=Admin&page=theme',
        'icon'   => 'home.png',
        'text'   => HOME,
        'active' => null
    ),
    array(
        'link'   => 'index.php?file=Admin&page=theme&op=settings',
        'icon'   => 'settings.png',
        'text'   => GENERAL_PREFS,
        'active' => null
    ),
    array(
        'link'   => 'index.php?file=Admin&page=theme&op=blocks_management',
        'icon'   => 'blocks.png',
        'text'   => BLOCKS_MANAGEMENT,
        'active' => null
    ),
    array(
        'link'   => 'index.php?file=Admin&page=theme&op=modules_management',
        'icon'   => 'modules.png',
        'text'   => MODULES_MANAGEMENT,
        'active' => null
    ),
    array(
        'link'   => 'index.php?file=Admin&page=theme&op=sponsors_management',
        'icon'   => 'sponsors.png',
        'text'   => SPONSORS_MANAGEMENT,
        'active' => null
    ),
    array(
        'link'   => 'index.php?file=Admin&page=theme&op=nav_management',
        'icon'   => 'nav.png',
        'text'   => NAV_MANAGEMENT,
        'active' => null
    )
);


foreach ($arrayNav as $key => $item) {
    parse_str(substr($item['link'], 10), $uri);

    if ($_REQUEST['op'] == $uri['op']
        || ($_REQUEST['op'] == 'index' && $uri['op'] == null)
    ) {
        $arrayNav[$key]['active'] = ' class="nkClassActive" ';
    }
}

$this->assign('arrayNav', $arrayNav);