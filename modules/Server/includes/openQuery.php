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
 * @brief Uses the openQuery protcol to communicate with the server
 * @author Curtis Brown <webmaster@2dementia.com>
 * @version $Revision: 203 $
 *
 * The openQuery protocol comes from UDP Soft (creators of 'The All Seeing Eye')
 */
class openQuery extends gsQuery
{


  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }
    $this->playerteams = array('red', 'blue');

    $cmd='s';
    if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr='No reply received';
      return FALSE;
    }


    $gamearray = array();
    $pos = 4;
    $gamearray[0] = substr($response,0,4);
    for($i = 1;$i < 10;$i++) {
      $gamearray[$i] = substr($response,$pos+1,ord(substr($response,$pos,1))-1);
      $pos = $pos + ord(substr($response,$pos,1));
    }

    $this->numplayers=$gamearray[8];
    $this->maxplayers=$gamearray[9];
    $this->gametype=$gamearray[1];
    $this->gamename=$gamearray[1];
    $this->gameversion=$gamearray[6];
    $this->servertitle=$gamearray[3];
    $this->mapname=$gamearray[5];
    $this->hostport=$gamearray[2];
    $this->gametype=$gamearray[4];
    $this->password=$gamearray[7];

    // get rules and basic infos
    $endrules=0;
    if(ord(substr($response,$pos,1))!=1) { //skip rules
      do {
	$rulename= substr($response,$pos+1,ord(substr($response,$pos,1))-1);
	$pos = $pos + ord(substr($response,$pos,1));
	$rulevalue=substr($response,$pos+1,ord(substr($response,$pos,1))-1);
	$pos = $pos + ord(substr($response,$pos,1));

	switch ($rulename) {
	case 'gr_ScoreLimit':
	  $this->scorelimit=$rulevalue;
	  break;
	case 'gr_NextMap':
	  $this->nextmap=$rulevalue;
	  break;
	default:
	  //save the rule
	  $this->rules[$rulename] = $rulevalue;
	  break;
	}

      } while(ord(substr($response,$pos,1))!=1); // the \x01 at the end indicates transfer to player list.
    }
    $pos++;  //align to the beginning of player data

    $playerdata=substr($response,$pos,strlen($response));
    if($playerdata!=NULL) $this->_processPlayers($playerdata);
    $this->online=TRUE;
    return TRUE;
  }


  /**
   * @internal
   * @brief Extracts the players out of the given data
   *
   * @param rawchunk data with players
   * @return TRUE on success
   * @todo Add spectators
   */
  function _processPlayers($rawchunk)
  {

    $pos=0;$endplayers=0;$i=0;$skipread=0;

    do {
      $delimiter=ord($rawchunk{$pos++}); // this is a flag byte
      /*
         the flag byte is broken down the following way:

         XX111111
         ||||||||
         |||||||-----   Name is present
         ||||||------   Team Info is present
         |||||-------   Skin Info is present
         ||||--------   Score Info is present
         |||---------   Ping Info is present
         ||----------   Time Info is present
         |-----------   Undefined
         ------------   Undefined

      */

      // there are 6 possible data types, cycle through and grab each if present
      for($j=0;$j<6;$j++) {
        $flag=($delimiter & (1<<$j));

        switch($flag) {
	case 1: // name
          $datname='name';
	  break;
	case 2: // team info
          $datname='team';
	  break;
	case 4: // skin
          $datname='skin';
	  break;
	case 8: // score
          $datname='score';
	  break;
	case 16:// ping
          $datname='ping';
	  break;
	case 32:// time
          $datname='time';
	  break;
	default:// item not supported
          $skipread=1;
	  break;
	}
	// read the data
        if(!$skipread) {
          $this->playerkeys[$datname]=TRUE;
          $this->players[$i][$datname] = substr($rawchunk,$pos+1,ord(substr($rawchunk,$pos,1))-1);
          $pos = $pos + ord(substr($rawchunk,$pos,1));
	}
        $skipread=0;

      } // end for
      $i++; //next player

      if($i==$this->numplayers) $endplayers++;   // we have reached the max # of players, stop looping.

    } while(!$endplayers);

    return TRUE;
  }


  function htmlize($string)
  {
    $colors = array('black', 'white', 'blue', 'green', 'red', 'light-blue', 'yellow', 'pink', 'orange', 'grey');

    $string = htmlentities($string);
    $num_tags = preg_match_all("/\\$(\d)/", $string, $matches);
    $string = preg_replace("/\\$(\d)/e", "'<span class=\"gsquery-'. \$colors[\$1] .'\">'", $string);

    return $string . str_repeat('</span>', $num_tags);
  }


  function _getClassName()
  {
      return 'openQuery';
  }
}

?>