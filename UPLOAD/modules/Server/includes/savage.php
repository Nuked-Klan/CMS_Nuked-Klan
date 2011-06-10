<?php
if (!defined("INDEX_CHECK"))
{
	exit('You can\'t run this file alone.');
}

/*
 *  gsQuery - Querys game servers
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

include_once GSQUERY_DIR . 'gsQuery.php';

/**
 * @brief This class implements the protocol used by Savage
 * @author Curtis Brown <webmaster@2dementia.com>
 * @version $Revision: 190 $
 */
class savage extends gsQuery
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    $command="\x9E\x4C\x23\x00\x00\xCE";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr="No reply received";
      return FALSE;
    }
    $this->hostport=$this->queryport;

    /* process data */
    /* cut string into pieces */
    $pieces = explode("\xFF", $result);
    $cnt = count($pieces, COUNT_RECURSIVE);
    $j=0;
    for($i=1; $i<$cnt; $i++) {
      $smpieces = explode("\xFE",$pieces[$i]);
      $output[$j++] = $smpieces[0];
      $output[$j++] = $smpieces[1];
    }

    // Do Rules:
    $total=count($output);
    $j=0;
    while ($j<$total) {
      switch($output[$j++]) {
      case "name":
	$this->servertitle=$output[$j++];
	break;
      case "cnum":
	$this->numplayers=$output[$j++];
	break;
      case "cmax":
	$this->maxplayers=$output[$j++];
	break;
      case "world":
	$this->mapname=$output[$j++];
	break;
      case "gametype":
	$this->gametype=$output[$j++];
	break;
      case "pass":
	$this->password=$output[$j++];
	break;
      case "players":
	$playerstring=$output[$j++];
	break;
      default:
	$this->rules[$output[$j-1]]=$output[$j++];
	break;
      }
    }

    $this->gamename = 'savage';
    $this->online = TRUE;
    if(!$getPlayers) {
      return TRUE;
    }

    /* sort players */
    if(isset($playerstring)) {
      /* get lines, remove last (empty) line */
      $lines = preg_split("/\n/", $playerstring);
      $cnt = count($lines)-1;
      unset($lines[$cnt]);

      /* go through lines */
      $team_name = 'unknown';
      $team_id = -1;
      $player_cnt = 0;
      $j=0;
      for($i=0; $i!=$cnt; $i++) {
	/* get team name & number */
	if(preg_match("/^Team (\d) \((.+)\):$/", $lines[$i], $match)) {
	  $team_id = $match[1];
	  $team_name = $match[2];
	} elseif ($lines[$i] != '--empty team--') {
	  /* set player */
	  $this->players[$j]["name"] = $lines[$i];
	  $this->players[$j++]["team"]= $team_id;
	}
      }
      $this->playerkeys["name"]=TRUE;
      $this->playerkeys["team"]=TRUE;
      if(array_key_exists('race1', $this->rules) && array_key_exists('race2', $this->rules)) {
	$this->playerteams[] = $this->rules['race1'];
	$this->playerteams[] = $this->rules['race2'];
      }
    }


    return TRUE;
  }

  //strips color codes
  function htmlize($var)
  {
    $var = htmlspecialchars($var);
    while(preg_match('`\^([0-9][0-9][0-9])`', $var)) {
      $var = preg_replace("#\^([0-9][0-9][0-9])(.*)$#Usi", "$2", $var);
    }
    while(preg_match('`\^([a-z])`', $var)) {
      $var = preg_replace("#\^([a-z])(.*)$#Usi", "$2", $var);
    }
    return $var;
  }

}
?>