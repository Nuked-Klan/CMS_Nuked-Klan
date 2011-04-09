<?php
if (!defined("INDEX_CHECK"))
{
	exit('You can\'t run this file alone.');
}

/*
 *  gsQuery - Querys various game servers
 *  Copyright (c) 2002-2004 Jeremias Reith <jr@terragate.net>
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

require_once GSQUERY_DIR . 'gsQuery.php';

/**
 * @brief Uses the Spearhead protcol to communicate with the server
 * @author Robert Limoges (robert@mega-rl.ca)
 * @version $Id: mohs.php 190 2004-09-25 15:48:06Z jr $
 *
 * This class can communicate with Medal of Honor Spearhead and Breakthrough
 */
class mohs extends gsQuery
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    $command="\\status\\";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr='No reply received';
      return FALSE;
    }

    $temp=explode("\x0a",$result);
    $rawdata=explode("\\",substr($temp[0],1,strlen($temp[0])));

    // get rules and basic infos
    $numData = count($rawdata);
    for($i=0;$i<$numData;$i++) {
      if(strpos('player_',$rawdata[$i]))
	break;
      if(strpos('final',$rawdata[$i]))
	break;
      switch ($rawdata[$i++]) {
      case 'gametype':
	$this->gametype=$rawdata[$i];
	break;
      case 'gamename':
	$this->gamename=preg_replace('/[ ]/', '_', strtolower($rawdata[$i]));
	break;
      case 'gamever':
	$this->gameversion=$rawdata[$i];
	break;
      case 'hostname':
	$this->servertitle=$rawdata[$i];
	break;
      case 'mapname':
	$this->mapname=$rawdata[$i];
	break;
      case 'hostport':
	$this->hostport=$rawdata[$i];
	break;
      case 'maxplayers':
	$this->maxplayers=$rawdata[$i];
	break;
      case 'numplayers':
	$this->numplayers=$rawdata[$i];
	break;
      default:
	$this->rules[$rawdata[$i-1]] = $rawdata[$i];
      }
    }

    if(!$this->maxplayers) {
      return FALSE;
    }

    $this->online = TRUE;

    if(!$getPlayers) {
      return TRUE;
    }

    // get playerdata
    for($j=0;$j<$this->numplayers;$j++) {
      $d=explode('_',$rawdata[$i]);
      $this->players[$j]['client']=$d[1];
      $this->players[$j]['name']=$rawdata[++$i];
      $i++;
      $this->players[$j]['frags']=$rawdata[++$i];
      $i++;
      $this->players[$j]['deaths']=$rawdata[++$i];
      $i++;
      $this->players[$j]['ping']=$rawdata[++$i];
      $i++;
      $this->players[$j]['score']=$this->players[$j]['frags']-$this->players[$j]['deaths'];
    }

    $this->playerkeys['name']=TRUE;
    $this->playerkeys['score']=TRUE;
    $this->playerkeys['frags']=TRUE;
    $this->playerkeys['deaths']=TRUE;
    $this->playerkeys['ping']=TRUE;

    return TRUE;
  }
}

?>