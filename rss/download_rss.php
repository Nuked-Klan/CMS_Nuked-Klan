<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal                                                //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//
define('INDEX_CHECK', 1);
include('../globals.php');
include('../conf.inc.php');
include('../nuked.php');
include('../Includes/constants.php');
include('../lang/' . $nuked['langue'] . '.lang.php');

$rsslang = ($nuked['langue'] == 'french') ? 'fr' : 'en-us';

$sitename = @html_entity_decode($nuked['name']);
$sitedesc = @html_entity_decode($nuked['slogan']);
$sitename = str_replace('&amp;', '&', $sitename);
$sitedesc = str_replace('&amp;', '&', $sitedesc);
$sitename = htmlspecialchars($sitename);
$sitedesc = htmlspecialchars($sitedesc);

header('Content-Type: text/xml');

echo '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n"
    . '<rss version="2.0">'."\n"
    . '<channel>'."\n"
    . '<title>' . $sitename . '</title>'."\n"
    . '<link>' . $nuked['url'] . '</link>'."\n"
    . '<image>'."\n"
    . '<url>' . $nuked['url'] . '/images/ban.gif</url>'."\n"
    . '<title>' . $sitename . '</title>'."\n"
    . '<link>' . $nuked['url'] . '</link>'."\n"
    . '<width>88</width>'."\n"
    . '<height>31</height>'."\n"
    . '</image>'."\n"
    . '<description>' . $sitedesc . '</description>'."\n"
    . '<language>' . $rsslang . '</language>'."\n"
    . '<webMaster>' . $nuked['mail'] . '</webMaster>'."\n";

$result = mysql_query('SELECT id, titre, description, date FROM ' . DOWNLOAD_TABLE . ' WHERE level = 0 ORDER BY date DESC LIMIT 0, 20');
while (list($did, $titre, $texte, $date) = mysql_fetch_array($result)){
    $pubdate = date("r", $date);
    $titre = htmlspecialchars($titre);

    if ($texte != ''){
        $description = strip_tags($texte);

        if (strlen($description) > 300){
            $description = substr($description, 0, 300) . '...';
        }
        
        $description = htmlspecialchars($description);
    }
    else{
        $description = '';
    }

    echo '<item>'."\n"
    . '<title>' . $titre . '</title>'."\n"
    . '<link>' . $nuked['url'] . '/index.php?file=Download&amp;op=description&amp;dl_id=' . $did . '</link>'."\n"
    . '<pubDate>' . $pubdate . '</pubDate>'."\n"
    . '<description>' . $description . '</description>'."\n"
    . '</item>'."\n";
}

echo '</channel>'."\n"
. '</rss>';
?>