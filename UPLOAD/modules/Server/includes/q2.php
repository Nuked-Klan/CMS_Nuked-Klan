<?php
if (!defined("INDEX_CHECK"))
{
	exit('You can\'t run this file alone.');
}

/*
 *  gsQuery - Querys various game servers
 *  Copyright (c) 2004 Jeremias Reith <jr@terragate.net>
 *  http://www.gsquery.org
 *
 *  This file is part of the gsQuery library.
 *
 *  The gsQuery library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  The gsQuery library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public
 *  License along with the gsQuery library; if not, write to the
 *  Free Software Foundation, Inc.,
 *  59 Temple Place, Suite 330, Boston,
 *  MA  02111-1307  USA
 *
 */

require_once GSQUERY_DIR . 'quake.php';

/**
 * @brief Uses the Quake 2 protcol to communicate with the server
 * @author Curtis Brown <volfin1@earthlink.net>
 * @version $Revision: 190 $
 *
 * This class can communicate with most games based on the Quake 2
 * engine.
 */
class q2 extends quake
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    $command="\xFF\xFF\xFF\xFFstatus\x0a\x00";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr='No reply received';
      return FALSE;
    }

    $temp=explode("\x0a",$result);
    $rawdata=explode("\\",substr($temp[1],1,strlen($temp[1])));

    // get rules and basic infos
    for($i=0;$i< count($rawdata);$i++) {
      switch ($rawdata[$i++]) {
      case 'game':
	$this->gametype=$rawdata[$i];
	break;
      case 'gamename':
	$this->gamename=q2a;
	break;
      case 'version':
	$this->gameversion=$rawdata[$i];
	break;
      case 'hostname':
	$this->servertitle=$rawdata[$i];
	break;
      case 'mapname':
	$this->mapname=$rawdata[$i];
	break;
      case 'capturelimit':
      case 'scorelimit':
	$this->scorelimit=$rawdata[$i];
	break;
      case 'needpass':
	$this->password=$rawdata[$i];
	break;
      case 'maxclients':
	$this->rules['sv_maxclients']=$rawdata[$i];
	break;
      default:
	$this->rules[$rawdata[$i-1]] = $rawdata[$i];
      }
    }

    $this->hostport = $this->queryport;
    $this->maxplayers = $this->rules['sv_maxclients']-$this->rules['sv_privateClients'];

    //get playerdata
    $temp=substr($result,strlen($temp[0])+strlen($temp[1])+1,strlen($result));
    $allplayers=explode("\n", $temp);
    $this->numplayers=count($allplayers)-2;

    // get players
    if(count($allplayers)-2 && $getPlayers) {
      $this->_processPlayers($allplayers);
    }

    $this->online = TRUE;
    return TRUE;
  }

  function htmlize($string)
  {
    return htmlentities($this->textify($string));
  }

  function textify($string)
  {
    $len = strlen($string);
    for($i=0;$i<$len;$i++) {
      if($string[$i]>"\x1F") {
	$retstr=$retstr.$string[$i];
      }
    }
    return $retstr;
  }

  function _processPlayers($allplayers)
  {
    $numPlayers = count($allplayers);
    for($i=1;$i<$numPlayers-1;$i++) {
      if(preg_match("/(\d+)[^0-9](\d+)[^0-9]\"(.*)\"/", $allplayers[$i], $curplayer)) {
	$players[$i-1]['name']=$curplayer[3];
	$players[$i-1]['score']=$curplayer[1];
     $players[$i-1]['ping']=$curplayer[2];

      }
    }
    $this->playerkeys['name']=TRUE;
    $this->playerkeys['score']=TRUE;
    $this->playerkeys['ping']=TRUE;
    $this->players=$players;
  }
}

?>