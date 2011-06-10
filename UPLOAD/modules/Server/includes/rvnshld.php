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
 * @brief Implements the properitary protocol used by Raven Shield
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: rvnshld.php 205 2004-12-21 13:30:55Z jr $
 * @todo Some variables are missing
 *
 * As far as I know this works with 'Rainbox Six: Raven Shield' only.
 */
class rvnshld extends gsQuery
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }
    $unknown_variables = 0;

    $command='REPORT';
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      return FALSE;
    }

    $this->online=TRUE;
    $this->gamename='rvnshld';

    $temp=explode("\x20\xb6",$result);

    foreach($temp as $curvalue) {
      switch(substr($curvalue, 0, 2)) {
      case 'L2':
	$this->gamename=substr($curvalue, 3);
	break;
      case 'P1':
	$this->hostport=substr($curvalue, 3);
	break;
      case 'I1':
	$this->servertitle=substr($curvalue, 3);
	break;
      case 'A1':
	$this->maxplayers=substr($curvalue, 3);
	break;
      case 'B1':
	$this->numplayers=substr($curvalue, 3);
	break;
      case 'D2':
	$this->gameversion=substr($curvalue, 3);
	break;
      case 'E1':
	$this->mapname=substr($curvalue, 3);
	break;
      case 'G1':
	$this->password=substr($curvalue, 3);
	break;
      case 'F1':
	$this->gametype=substr($curvalue, 3);
	break;
      case 'R1':
	$this->rules['Time per round']=substr($curvalue, 3);
	break;
      case 'Q1':
	$this->rules['Number of rounds']=substr($curvalue, 3);
	break;
      case 'T1':
	$this->rules['Bomb timer']=substr($curvalue, 3);
	break;
      case 'S1':
	$this->rules['Time between rounds']=substr($curvalue, 3);
	break;
      case 'G2':
	$this->rules['Query port']=substr($curvalue, 3);
	break;
      case 'K2':
	$this->rules['Force FPW']=substr($curvalue, 3);
	break;
      case 'I2':
	$this->rules['aiback']=substr($curvalue, 3);
	break;
      case 'H2':
	$this->rules['Number of Terrorists']=substr($curvalue, 3);
	break;
      case 'F2':
	$this->rules['gid']=substr($curvalue, 3);
	break;
      case 'E2':
	$this->rules['lid']=substr($curvalue, 3);
	break;
      case 'B2':
	$this->rules['Radar Enabled']=substr($curvalue, 3);
	break;
      case 'A2':
	$this->rules['TK Penalty']=substr($curvalue, 3);
	break;
      case 'Z1':
	$this->rules['Team Auto Balance']=substr($curvalue, 3);
	break;
      case 'Y1':
	$this->rules['Friendly Fire']=substr($curvalue, 3);
	break;
      case 'X1':
	$this->rules['iserver']=substr($curvalue, 3);
	break;
      case 'W1':
	$this->rules['snames']=substr($curvalue, 3);
	break;
      case 'H1':
	$this->rules['Dedicated Server']=substr($curvalue, 3);
	break;
      case 'G1':
	$this->rules['Server Locked']=substr($curvalue, 3);
	break;
      case 'K1':
	$this->maplist=explode('/', substr($curvalue, 4));
	break;
      case 'L1':
	$playernames=explode('/', substr($curvalue, 3));
	for($i=1;$i<count($playernames);$i++) {
	  $this->players[$i]['name']=$playernames[$i];
	}
	break;
      case 'O1':
	$playerscores=explode('/', substr($curvalue, 3));
	for($i=1;$i<count($playerscores);$i++) {
	  $this->players[$i]['score']=$playerscores[$i];
	}
	break;
      case 'N1':
	$playerpings=explode('/', substr($curvalue, 3));
	for($i=1;$i<count($playerpings);$i++) {
	  $this->players[$i]['ping']=$playerpings[$i];
	}
	break;
      case 'M1':
	$playertimes=explode('/', substr($curvalue, 3));
	for($i=0;$i<count($playertimes);$i++) {
	  $this->players[$i]['time']=$playertimes[$i];
	}
	break;
      default:
	// Don't know this variable
	$this->debug['Unknown variable '. ++$unknown_variables. ':']=$curvalue;
      }
      if(!empty($this->maplist)) {
	$i=0;
	while($this->mapname != $this->maplist[$i++] && $i<count($this->maplist));
	$this->nextmap=$this->maplist[$i % count($this->maplist)];
      }
    }
    return TRUE;
  }
}
?>