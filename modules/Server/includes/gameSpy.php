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
 * @brief Uses the gameSpy protcol to communicate with the server
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: gameSpy.php 190 2004-09-25 15:48:06Z jr $
 * @bug some games does not escape the backslash, so we have a problem when somebody has a backlsash in its name
 *
 * The following games have been tested with this class:
 *
 *   - Unreal Tournamnet (and most mods)
 *   - Unreal Tournamnet 200x (and most mods)
 *   - Battlefield 1942 (and most mods)
 */
class gameSpy extends gsQuery
{

  var $infoCommand = '\basic\\\\info\\';
  var $playerCommand = '\\players\\';
  var $ruleCommand = '\\rules\\';

  function getGameJoinerURI()
  {
    switch($this->gamename) {
    case 'bfield1942':
      return 'gamejoin://bf1942@'. $this->address .':'. $this->hostport .'/';
      break;
    case 'ut2':
      return 'gamejoin://ut2003@'. $this->address .":". $this->hostport .'/';
      break;
    default:
      return 'gamejoin://'. $this->gamename .'@'. $this->address .':'. $this->hostport .'/';
    }
  }

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    if(!($response=$this->_sendCommand($this->address, $this->queryport, $this->infoCommand))) {
      $this->errstr='No reply received';
      return FALSE;
    }
    $this->_processServerInfo($response);

    $this->online=TRUE;

    // get players
    if($this->numplayers && $getPlayers) {
      if(!($response=$this->_sendCommand($this->address, $this->queryport, $this->playerCommand))) {
	return FALSE;
      }

      $this->_processPlayers($response);
    }


    // get rules
    if($getRules) {
      if(!($response=$this->_sendCommand($this->address, $this->queryport, $this->ruleCommand))) {
	return FALSE;
      }
      $this->_processRules($response);
    }

    return TRUE;
  }

  function getDebugDumps($html=FALSE, $dumper=NULL) {
    require_once(GSQUERY_DIR . 'includes/HexDumper.class.php');

    if(!isset($dumper)) {
      $dumper = new HexDumper();
      $dumper->setDelimiter(ord('\\'));
    }

    return parent::getDebugDumps($html, $dumper);
  }

  /**
   * @internal @brief Process the given raw data and stores everything
   *
   * @param rawdata data that has the basic server infos
   * @return TRUE on success
   */
  function _processServerInfo($rawdata)
  {

    $temp=explode("\\",$rawdata);
    $count=count($temp);
    for($i=1;$i<$count;$i++) {
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
	$this->rules[$temp[$i]] = $temp[++$i];
      }
    }

    if(!$this->gamename) {
      $this->gamename='unknown';
    }

    return TRUE;
  }

  /**
   * @internal @brief Extracts the players out of the given data
   *
   * @param rawPlayerData data with players
   * @return TRUE on success
   */
  function _processPlayers($rawPlayerData)
  {
    $temp=explode("\\", $rawPlayerData);
    $this->playerkeys['name']=TRUE;
    $count=count($temp);
    for($i=1;$i<$count;$i++) {
      list($var, $playerid)=explode('_', $temp[$i]);
      switch($var) {
      case 'player':
      case 'playername':
	$players[$playerid]['name']=$temp[++$i];
	break;
      case 'teamname':
	$this->playerteams[$playerid]=$temp[++$i];
	break;
      default:
	$players[$playerid][$var]=$temp[++$i];
	$this->playerkeys[$var]=TRUE;
      }
    }
    $this->players=$players;
    return TRUE;
  }

  /**
   * @internal @brief Extracts the rules out of the given data
   *
   * @param rawData data with rules
   * @return TRUE on success
   */
  function _processRules($rawData)
  {
    $temp=explode("\\",$rawData);
    $count=count($temp);
    for($i=1;$i<$count;$i++) {
      if($temp[$i]!='queryid' && $temp[$i]!='final' && $temp[$i]!='password') {
	$this->rules[$temp[$i]]=$temp[++$i];
      } else {
	if($temp[$i++]=='password') {
	  $this->password=$temp[$i];
	}
      }
    }
    return TRUE;
  }

  /**
   * @internal @brief sorts the given gamespy data
   *
   * @param data raw data to sort
   * @return raw data sorted
   */
  function _sortByQueryId($data)
  {
    $result='';
    $data=preg_replace("/\\\final\\\/", '', $data);
    $exploded_data=explode("\\queryid\\", $data);
    $count=count($exploded_data);
    for($i=0;$i<$count-1;$i++) {
      preg_match("/^\d+\.(\d+)/", $exploded_data[$i+1], $id);
      $sorted_data[$id[1]]=$exploded_data[$i];
      $exploded_data[$i+1]=substr($exploded_data[$i+1],strlen($id[0]-1),strlen($exploded_data[$i+1]));
    }

    if(!$sorted_data) {
      // the request is probably incomplete
      return $data;
    }

    // sort the hash
    ksort($sorted_data);
    foreach($sorted_data as $key => $value) {
      $result.=isset($value) ? $value : '';
    }
    return($result);
  }

  function _sendCommand($address, $port, $command, $timeout=500000)
  {
    $data=parent::_sendCommand($address, $port, $command, $timeout);
    if(!$data) {
      return FALSE;
    }
    return $this->_sortByQueryId($data);
  }


  function _getClassName()
  {
    return 'gameSpy';
  }
}

?>