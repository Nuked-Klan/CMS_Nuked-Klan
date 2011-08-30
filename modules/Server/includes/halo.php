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

require_once GSQUERY_DIR . 'gsQuery.php';

/**
 * @brief Implements the protocol used by halo
 * @version $Revision: 190 $
 * @author Curtis Brown <webmaster@2dementia.com>
 *
 */

class halo extends gsQuery
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    $cmd="þý".Chr(0)."wjÿÿÿÿ";
    if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr='No reply received';
      return FALSE;
    }

    $response=substr($response,5,strlen($response));

    $r = explode(chr(0), $response);

    if(!$r[2]) {
      return FALSE;
    }

    for($i=0;$i<32;$i+=2) {
      switch ($r[$i]) {
      case 'hostname':
	$this->servertitle=$r[$i+1];
	break;
      case 'numplayers':
	$this->numplayers=$r[$i+1];
	break;
      case 'maxplayers':
	$this->maxplayers=$r[$i+1];
	break;
      case 'gamever':
	$this->gameversion=$r[$i+1];
	break;
      case 'mapname':
	$this->mapname=$r[$i+1];
	break;
      case 'gametype':
	$this->gametype=$r[$i+1];
	break;
      case 'password':
	$this->password=$r[$i+1];
	break;
      case 'hostport':
	$this->hostport=$r[$i+1];
	if (!$this->hostport) $this->hostport=$this->queryport;
	break;
      default:
	$this->rules["$r[$i]"] = $r[$i+1];
      }

    }
    $this->gamename = 'halo';
    $this->rules['numberoflives'] = $this->player_flag('numberoflives',$this->rules['player_flags'] & 3);
    $this->rules['maximumhealth'] = $this->player_flag('maximumhealth',($this->rules['player_flags'] >> 2) & 7);
    $this->rules['shields'] = $this->player_flag('shields',($this->rules['player_flags'] >> 5) & 1);
    $this->rules['respawntime'] = $this->player_flag('respawntime',($this->rules['player_flags'] >> 6) & 3);
    $this->rules['respawngrowth'] = $this->player_flag('respawngrowth',($this->rules['player_flags'] >> 8) & 3);
    $this->rules['oddmanout'] = $this->player_flag('oddmanout',($this->rules['player_flags'] >> 10) & 1);
    $this->rules['invisibleplayers'] = $this->player_flag('invisibleplayers',($this->rules['player_flags'] >> 11) & 1);
    $this->rules['suicidepenalty'] = $this->player_flag('suicidepenalty',($this->rules['player_flags'] >> 12) & 3);
    $this->rules['infinitegrenades'] = $this->player_flag('infinitegrenades',($this->rules['player_flags'] >> 14) & 1);
    $this->rules['startingequip'] = $this->player_flag('startingequip',($this->rules['player_flags'] >> 19) & 1);
    $this->rules['indicator'] = $this->player_flag('indicator',($this->rules['player_flags'] >> 20) & 3);
    $this->rules['otherplayersonradar'] = $this->player_flag('otherplayersonradar',($this->rules['player_flags'] >> 22) & 3);
    $this->rules['friendindicators'] = $this->player_flag('friendindicators',($this->rules['player_flags'] >> 24) & 1);
    $this->rules['friendlyfire'] = $this->player_flag('friendlyfire',($this->rules['player_flags'] >> 25) & 3);
    $this->rules['friendlyfirepenalty'] = $this->player_flag('friendlyfirepenalty',($this->rules['player_flags'] >> 27) & 3);
    $this->rules['autoteambalance'] = $this->player_flag('autoteambalance',($this->rules['player_flags'] >> 29) & 1);
    $this->rules['weaponset'] = $this->player_flag('weaponset',($this->rules['player_flags'] >> 15) & 15);

    $this->playerkeys['name']=TRUE;
    $this->playerkeys['score']=TRUE;
    $this->playerkeys['team']=TRUE;
    $xc = 39;
    for($i=0;$i<$this->numplayers;$i++) {
      $this->players[$i]['name'] = $r[$xc];
      $xc++;
      $this->players[$i]['score'] = $r[$xc]; $xc++;
      $this->players[$i]['ping'] = $r[$xc]; $xc++;  // there is a placeholder for ping, but the game doesn't use it
      $this->playerteams[$i] = $r[$xc]+1;
      $this->players[$i]['team'] = $r[$xc]+1; $xc++;
    }
    return TRUE;
  }

  function player_flag($flag, $n) {
    switch ($flag) {
    case 'numberoflives':
      switch ($n) {
      case 0:
	return 'Infinite';
      case 1:
	return '1 Life';
      case 2:
	return '3 Lives';
      case 3:
	return '5 Lives';
      }
    case 'maximumhealth':
      switch ($n) {
      case 0:
	return '50%';
      case 1:
	return '100%';
      case 2:
	return '150%';
      case 3:
	return '200%';
      case 4:
	return '300%';
      case 5:
	return '400%';
      }
    case 'shields':
      return ($n == 0 ? 'Yes' : 'No');
    case 'respawntime':
      switch ($n) {
      case 0:
	return 'Instant';
      case 1:
	return '5 sec';
      case 2:
	return '10 sec';
      case 3:
	return '15 sec';
      }
    case 'respawngrowth':
      switch ($n) {
      case 0:
	return 'Instant';
      case 1:
	return '5 sec';
      case 2:
	return '10 sec';
      case 3:
	return '15 sec';
      }
    case 'oddmanout':
      return ($n == 0 ? 'No' : 'Yes');
    case 'invisibleplayers':
      return ($n == 0 ? 'No' : 'Yes');
    case 'suicidepenalty':
      switch ($n) {
      case 0:
	return 'None';
      case 1:
	return '5 sec';
      case 2:
	return '10 sec';
      case 3:
	return '15 sec';
      }
    case 'infinitegrenades':
      return ($n == 0 ? 'No' : 'Yes');
    case 'weaponset':
      switch ($n) {
      case 0:
	return 'Normal';
      case 1:
	return 'Pistols';
      case 2:
	return 'Rifles';
      case 3:
	return 'Plasma';
      case 4:
	return 'Sniper';
      case 5:
	return 'No Sniping';
      case 6:
	return 'Rocket Launchers';
      case 7:
	return 'Shotguns';
      case 8:
	return 'Short Range';
      case 9:
	return 'Human';
      case 10:
	return 'Covenant';
      case 11:
	return 'Classic';
      case 12:
	return 'Heavy Weapons';
      }
    case 'startingequip':
      return ($n == 0 ? 'Custom' : 'Generic');
    case 'indicator':
      switch ($n) {
      case 0:
	return 'Motion Tracker';
      case 1:
	return 'Nav Points';
      case 2:
	return 'None';
      }
    case 'otherplayersonradar':
      switch ($n) {
      case 0:
	return 'No';
      case 1:
	return 'All';
      case 2:
	return 'Friends';
      }
    case 'friendindicators':
      return ($n == 0 ? 'No' : 'Yes');
    case 'friendlyfire':
      switch ($n) {
      case 0:
	return 'Off';
      case 1:
	return 'On';
      case 2:
	return 'Shields Only';
      case 3:
	return 'Explosives Only';
      }
    case 'friendlyfirepenalty':
      switch ($n) {
      case 0:
	return 'None';
      case 1:
	return '5 sec';
      case 2:
	return '10 sec';
      case 3:
	return '15 sec';
      }
    case 'autoteambalance':
      return ($n == 0 ? 'No' : 'Yes');
    }
  }


  function _getClassName()
  {
    return 'halo';
  }
}

?>