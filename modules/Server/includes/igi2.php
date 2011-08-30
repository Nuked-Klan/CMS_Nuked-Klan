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

require_once GSQUERY_DIR . 'gameSpy.php';

/**
 * @brief Extends the gameSpy protocol to support IGI2
 * @author Curtis Brown (volfin1@earthlink.net)
 * @version $Revision: 190 $
 * @todo make teamnames available in teamnames array
 * @todo remove team scores from rules
 */
class igi2 extends gameSpy
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

    $pos=strpos($result,'\player_');
    $team=strpos($result,'\team_t');// find start of team info
    $rules=substr($result,0,$team);
    if($pos) {
      $players=substr($result,$pos);
      $teaminfo=substr($result,$team,$pos-$team);
      $this->_processServerInfo($rules);
      $this->_processPlayers($players);
    } else {
      // here if no players on server
      $teaminfo=substr($result,$team);
      $this->_processServerInfo($rules);
    }

    // save team information
    $temp=explode("\\", $teaminfo);
    $this->playerteams[0]=$temp[2];
    $this->playerteams[1]=$temp[4];

    return TRUE;
  }


  function _getClassName()
  {
    return 'igi2';
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
    $this->playerkeys['score']=TRUE;
    $this->playerkeys['ping']=TRUE;
    $this->playerkeys['deaths']=TRUE;
    $this->playerkeys['team']=TRUE;
    $count=count($temp);

    // use $l as the playerid because the game does not number players consecutively! first player might be player 20 or who knows!
    // we just look for changes in this number and increment $l accordingly.
    $l=-1;
    for($i=1;$i<$count;$i++) {
      list($var, $playerid)=explode('_', $temp[$i]);
      if($curid<>$playerid) {
	$l++;
	$curid=$playerid;
      }

      switch($var) {
      case 'player':
	$players[$l]['name']=$temp[++$i];
	break;
      case 'team':
	// we add 1 to match other games team 0 is 1 team 1 is 2.
	$players[$l]['team']=$temp[++$i]+1;
	break;
      case 'frags':
	$players[$l]['score']=$temp[++$i];
	break;
      case 'ping':
	$players[$l]['ping']=$temp[++$i];
	break;
      case 'deaths':
	$players[$l]['deaths']=$temp[++$i];
	break;
      default:
	$players[$l][$var]=$temp[++$i];
	$this->playerkeys[$var]=TRUE;
      }
    }

    $this->players=$players;
    return TRUE;
  }
}

?>