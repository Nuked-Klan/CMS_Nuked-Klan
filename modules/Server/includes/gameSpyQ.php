<?php
if (!defined("INDEX_CHECK"))
{
	exit('You can\'t run this file alone.');
}

/*
 *  gsQuery - Querys game servers
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
 * @brief Uses the new gameSpy query protcol to communicate with the server
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: gameSpyQ.php 197 2004-10-22 05:15:22Z jr $
 */
class gameSpyQ extends gsQuery
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    // enabling/disabling the request for player data
    $playerByte = $getPlayers ? "\xFF" : "\x00";

    // the last 2 bytes are for player and team data
    $cmd="\xFE\xFD\x00\x04\x05\x06\x07\xFF". $playerByte . $playerByte;
    if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr='No reply received';
      return FALSE;
    }

    // stripping header
    $response = substr($response, 5);

    $data = explode("\x00\x00\x00", $response);
    $serverData = explode("\x00\x00", $data[0]);
    $playerData = explode("\x00\x00", $data[1]);

    $this->_processServerData($serverData);

    // get players
    if($this->numplayers && $getPlayers) {
      $keys = explode("\x00", $playerData[0]);

      // manual currying
      $removeLastChar = create_function('$x', 'return substr($x, 0, -1);');

      // removing last char from all keys (last char is an underscore)
      $keys = array_map($removeLastChar, $keys);

      // first key is the player name
      $keys[0] = 'name';

      $this->_processPlayerData(explode("\x00", $playerData[1]), $keys);
    }

    $this->online=TRUE;
    return TRUE;
  }

  function sortPlayers($players, $sortkey='name')
  {
    if(!sizeof($players)) {
      return array();
    }

    if($sortkey == 'kills') {
      uasort($players, array('gsQuery', '_sortbyKills'));
    } else {
      $players=parent::sortPlayers($players, $sortkey);
    }
    return $players;
  }

  function _sortbyKills($a, $b)
  {
    if($a['kills']==$b['kills']) { return 0; }
    elseif($a['kills']<$b['kills']) { return 1; }
    else { return -1; }
  }


  /**
   * @internal @brief Process the given raw data and stores everything
   *
   * @param serverData data that has the basic server infos and rules
   * @return TRUE on success
   */
  function _processServerData($serverData)
  {
    foreach($serverData as $rawdata) {
      $temp=explode("\x00",$rawdata);
      $count=count($temp);
      for($i=0;$i<$count;$i++) {
	switch($temp[$i]) {
	case 'gamename':
	case 'game_id':
	  $this->gamename = $temp[++$i];
	  break;
	case 'hostport':
	  $this->hostport = $temp[++$i];
	  break;
	case 'gamever':
	  $this->gameversion = $temp[++$i];
	  break;
	case 'hostname':
	  $this->servertitle = $temp[++$i];
	  break;
	case 'mapname':
	  $this->mapname = $temp[++$i];
	  break;
	case 'maptitle':
	  $this->maptitle = $temp[++$i];
	  break;
	case 'gametype':
	  $this->gametype = $temp[++$i];
	  break;
	case 'numplayers':
	  $this->numplayers = $temp[++$i];
	  break;
	case 'maxplayers':
	  $this->maxplayers = $temp[++$i];
	  break;
	case 'password':
	  if($temp[++$i] == 0 || $temp[$i] == 1) {
	    $this->password = $temp[$i];
	  }
	  break;
	default:
	  if(array_key_exists($i+1, $temp)) {
	    $this->rules[$temp[$i]] = $temp[++$i];
	  }
	}
      }

    }

    if(!$this->gamename) {
      $this->gamename='unknown';
    }

    return TRUE;
  }

  /**
   * @internal @brief Process raw player data
   *
   * @param playerData array containing player data
   * @param keys array of available keys
   * @return TRUE on success
   */
  function _processPlayerData($playerData, $keys)
  {
    // looping through player data
    for($i=0; array_key_exists($i, $playerData);) {
      $curPlayer = array();
      foreach($keys as $curKey) {
	$curPlayer[$curKey] = $playerData[$i++];
      }
      $this->players[] = $curPlayer;
    }

    // setting available keys
    foreach($keys as $curKey) {
      $this->playerkeys[$curKey] = TRUE;
    }

    return TRUE;
  }

  function _getClassName()
  {
    return 'gameSpyQ';
  }
}

?>