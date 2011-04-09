<?php
if (!defined("INDEX_CHECK"))
{
	exit('You can\'t run this file alone.');
}

/*
 *  gsQuery - Querys various game servers
 *  Copyright (c) 2002-2004 Jeremias Reith <jr@gsquery.org>
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

require_once GSQUERY_DIR . 'q3a.php';

/**
 * @brief Uses the Doom 3 protcol to communicate with the server
 * @author Jeremias Reith (jr@gsquery.org)
 * @version $Revision: 190 $
 *
 * Uses color code routines from q3a
 */
class d3 extends q3a
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    $command="\xFF\xFFgetInfo\x00\x00\x00\x00";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr='No reply received';
      return FALSE;
    }

    // strip header
    $noHeader = substr($result, strpos($result, "\x00\x00", 20)+2);
    // find rules/players separator
    $seperatorPos = strpos($noHeader, "\x00\x00");

    // extract rule data
    $ruleData = substr($noHeader, 0, $seperatorPos);
    $rawdata=explode("\x00", $ruleData);

    // get rules and basic infos
    for($i=0;$i< count($rawdata);$i++) {
      switch ($rawdata[$i++]) {
      case 'si_gameType':
	$this->gametype=$rawdata[$i];
      case 'gamename':
	$this->gamename=$rawdata[$i];
	break;
      case 'si_version':
	$this->gameversion=$rawdata[$i];
	break;
      case 'si_name':
	$this->servertitle=$rawdata[$i];
	break;
      case 'si_map':
	$this->mapname=$rawdata[$i];
	break;
      case 'si_usepass':
	$this->password=$rawdata[$i];
	break;
      case 'si_maxPlayers':
	$this->maxplayers=$rawdata[$i];
	break;
      default:
	$this->rules[$rawdata[$i-1]] = $rawdata[$i];
      }
    }

    // game port is identical to query port
    $this->hostport = $this->queryport;
    $this->online =TRUE;

    if(!$getPlayers) {
      return TRUE;
    }

    // getting player data
    $playerData = substr($noHeader, $seperatorPos+2);

    // length of player data
    $len = strlen($playerData)-8;

    for($i=0;$i<$len;$i=$posNextPlayer) {
      // unpacking ping and client rate
      $curPlayer = unpack('@'.$i.'/x/nping/nrate', $playerData);
      // finding start offset of next player
      $posNextPlayer = strpos($playerData, "\x00", $i+8);
      if($posNextPlayer == FALSE) { break; } // abort on bogus data
      // extract player name
      $curPlayer['name'] = substr($playerData, $i+8, $posNextPlayer-$i-8);
      // add player to the list of players
      $this->players[$this->numplayers++] = $curPlayer;
    }

    $this->playerkeys = array('name' => TRUE, 'ping' => TRUE, 'rate' => TRUE);

    return TRUE;
  }
}
?>