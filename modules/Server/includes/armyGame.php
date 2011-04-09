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

require_once GSQUERY_DIR . 'gameSpy.php';

/**
 * @brief Extends the gameSpy protocol to support America's Army
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: armyGame.php 190 2004-09-25 15:48:06Z jr $
 *
 * This is a quick hack to support the changed America's Army protocol.
 * It is slow, incomplete and ugly. Does anyone have the protocol specs?
 * @todo Add rules & clean up
 */
class armyGame extends gameSpy
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    $command="\\status\\";
    if(!($result=$this->_sendCommand($this->address, $this->queryport, $command))) {
      $this->errstr='No reply received';
      return FALSE;
    }

    $this->online = TRUE;

    $cmd="\\basic\\\\info\\";
    if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr='No reply received';
      return FALSE;
    }

    // xxx: not a nice way
    preg_match("`^(.*)(\\\\leader_0.*)$`", $result, $matches);

    // get rid of the team scores
    $matches[2]=preg_replace("/\\\score_t\d.\d/e", '', $matches[2]);

    $this->_processServerInfo($response);
    $this->_processPlayers($matches[2]);

   if($matches[1] <> '') {
      $this->_processRules($matches[1]);
    } else {
      $this->_processRules($response);
    }

    return TRUE;
  }

  function _processPlayers($rawPlayerData)
  {
    $temp=explode("\\", $rawPlayerData);
    $this->playerkeys['name']=TRUE;
    $this->playerkeys['leader']=TRUE;
    $this->playerkeys['goal']=TRUE;
    $this->playerkeys['score']=TRUE;
    $this->playerkeys['ping']=TRUE;
    $this->playerkeys['roe']=TRUE;
    $this->playerkeys['kia']=TRUE;
    $this->playerkeys['enemy']=TRUE;

    $count=count($temp);
    for($i=1;$i<$count;$i++) {
      list($var, $playerid)=explode('_', $temp[$i]);
      switch($var) {
      case 'player':
      case 'playername':
	$players[$playerid]['name']=$temp[++$i];
	break;
      case 'honor':
	$players[$playerid]['score']=$temp[++$i];
	break;
      default:
	$players[$playerid][$var]=$temp[++$i];
	$this->playerkeys[$var]=TRUE;
      }
    }
    $this->players=$players;
    return TRUE;
  }

  function _processServerInfo($rawdata)
  {
    $temp=explode("\\",$rawdata);
    $count=count($temp);
    for($i=1;$i<$count;$i++) {
      $data[$temp[$i]]=$temp[++$i];
    }

    if ($data['gamename'] <> '') {$this->gamename = $data['gamename'];} {}
    if ($data['game_id'] <> '') {$this->gamename = $data['game_id'];} {}
    $this->hostport = $data['hostport'];
    $this->gameversion = $data['gamever'];
    $this->servertitle = $data['hostname'];
    $this->maptitle = isset($data['maptitle']) ? $data['maptitle'] : '';
    $this->mapname = $data['mapname'];
    $this->gametype = $data['gametype'];
    $this->numplayers = $data['numplayers'];
    $this->maxplayers = $data['maxplayers'];
    $this->rules['reservedslots'] = $data['reservedslots'];
    if(isset($data['password']) && ($data['password']==0 || $data['password']==1)) {
      $this->password=$data['password'];
    }

    if(!$this->gamename) {
      $this->gamename='unknown';
    }

    return TRUE;
  }

  function sortPlayers($players, $sortkey='name')
  {
    if(!sizeof($players)) {
      return array();
    }
    switch($sortkey) {
    case 'roe':
      uasort($players, array('armyGame', '_sortbyRoe'));
      break;
    case 'kia':
      uasort($players, array('armyGame', '_sortbyKia'));
      break;
    case 'enemy':
      uasort($players, array('armyGame', '_sortbyEnemy'));
      break;
    default:
      $players=parent::sortPlayers($players, $sortkey);
    }
    return ($players);
  }


  // private methods

  function _sortbyRoe($a, $b)
  {
    if($a['roe']==$b['roe']) { return 0; }
    elseif($a['roe']<$b['roe']) { return 1; }
    else { return -1; }
  }

  function _sortbyKia($a, $b)
  {
    if($a['kia']==$b['kia']) { return 0; }
    elseif($a['kia']<$b['kia']) { return 1; }
    else { return -1; }
  }

  function _sortbyEnemy($a, $b)
  {
    if($a['enemy']==$b['enemy']) { return 0; }
    elseif($a['enemy']<$b['enemy']) { return 1; }
    else { return -1; }
  }

  function _getClassName()
  {
    return 'armyGame';
  }
}

?>