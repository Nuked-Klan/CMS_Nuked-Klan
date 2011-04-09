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
define ("INDEX_CHECK", 1);
include ("globals.php");
include("nuked.php");
include("Includes/constants.php");
$sitename = utf8_encode($nuked['name']);
$sitedesc = utf8_encode($nuked['slogan']);
$sitename = @html_entity_decode($nuked['name']);
$sitedesc = @html_entity_decode($nuked['slogan']);
$sitename = str_replace("&amp;", "&", $sitename);
$sitedesc = str_replace("&amp;", "&", $sitedesc);
$sitename = htmlspecialchars($sitename);
$sitedesc = htmlspecialchars($sitedesc);
echo "<?xml version=\"1.0\" ?>n"
. "<OpenSearchDescription xmlns=\"http://a9.com/-/spec/opensearch/1.1/\">n"
. "<ShortName>" . $sitename . "</ShortName>n"
. "<Description>" . $sitedesc . "</Description>n"
. "<SyndicationRight>open</SyndicationRight>n"
. "<Language>fr</Language>n"
. "<OutputEncoding>ISO-8859-1</OutputEncoding>n"
. "<InputEncoding>ISO-8859-1</InputEncoding>n"
. "<Image height=\"16\" width=\"16\" type=\"image/x-icon\">" . $nuked['url'] . "/images/favicon.ico</Image>n"
. "<Url type=\"text/html\" method=\"get\" template=\"" . $nuked['url'] . "/index.php?file=Search&amp;op=mod_search&amp;main={searchTerms}\" />n"
. "</OpenSearchDescription>n";
?>