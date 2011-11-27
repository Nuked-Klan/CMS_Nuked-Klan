<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal                                                //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//
header('Content-type: text/html; charset=iso-8859-1');
define ('INDEX_CHECK', 1);

include('globals.php');
include('conf.inc.php');
include('nuked.php');
include('Includes/constants.php');

$sitename = utf8_encode($nuked['name']);
$sitedesc = utf8_encode($nuked['slogan']);
$sitename = html_entity_decode($nuked['name']);
$sitedesc = html_entity_decode($nuked['slogan']);
$sitename = str_replace('&amp;', '&', $sitename);
$sitedesc = str_replace('&amp;', '&', $sitedesc);
$sitename = htmlspecialchars($sitename);
$sitedesc = htmlspecialchars($sitedesc);

echo '<?xml version="1.0" ?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
<ShortName>' . $sitename . '</ShortName>
<Description>' . $sitedesc . '</Description>
<Url type="text/html" method="get" template="' . $nuked['url'] . '/index.php?file=Search&amp;op=mod_search&amp;main={searchTerms}"/>
<Image width="16" height="16">' . $nuked['url'] . '/images/favicon.ico</Image>
<InputEncoding>ISO-8859-1</InputEncoding>
<moz:SearchForm></moz:SearchForm>
<Url type="application/opensearchdescription+xml" rel="self" template="http://mycroft.mozdev.org/updateos.php/id0/.xml"/>
<moz:UpdateUrl>http://mycroft.mozdev.org/updateos.php/id0/.xml</moz:UpdateUrl>
</OpenSearchDescription>';
?>