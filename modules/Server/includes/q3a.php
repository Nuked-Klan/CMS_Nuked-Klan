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

require_once GSQUERY_DIR . 'quake.php';

/**
 * @brief Uses the Quake 3 protcol to communicate with the server
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Rev: 198 $
 *
 * This class can communicate with most games based on the Quake 3
 * engine.
 */
class q3a extends quake
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    $command="\xFF\xFF\xFF\xFF\x02getstatus\x0a\x00";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr='No reply received';
      return FALSE;
    }

    $temp=explode("\x0a",$result);
    $rawdata=explode("\\",substr($temp[1],1,strlen($temp[1])));

    // get rules and basic infos
    for($i=0;$i< count($rawdata);$i++) {
      switch ($rawdata[$i++]) {
      case 'g_gametypestring':
	$this->gametype=$rawdata[$i];
	break;
      case 'gamename':
	$this->gametype=$rawdata[$i];

	$this->gamename='q3a_' . preg_replace('/[ :]/', '_', strtolower($rawdata[$i]));
	break;
      case 'version':
	$this->gameversion=$rawdata[$i];
	break;
      // for CoD
      case 'shortversion':
        $this->gameversion=$rawdata[$i];
        break;
      case 'sv_hostname':
	$this->servertitle=$rawdata[$i];
	break;
      case 'mapname':
	$this->mapname=$rawdata[$i];
	break;
      case 'g_needpass':
	$this->password=$rawdata[$i];
	break;
      // for CoD
      case 'pswrd':
        $this->password=$rawdata[$i];
        break;
      case 'sv_maplist':
	$this->maplist=preg_split('#( )+#', $rawdata[$i]);
	break;
      case 'sv_privateclients':
	$this->rules['sv_privateClients']=$rawdata[$i];
	break;
      default:
	$this->rules[$rawdata[$i-1]] = $rawdata[$i];
      }
    }

    // for MoHAA
    if(!$this->gamename && preg_match('`Medal of Honor`i', $this->gameversion)) {
      $this->gamename='mohaa';
    }

    if(!empty($this->maplist)) {
      $i=0;
      while($this->mapname!=$this->maplist[$i++] && $i<count($this->maplist));
      $this->nextmap=$this->maplist[$i % count($this->maplist)];
    }

    //for MoHAA
    $this->mapname=preg_replace('/.*\//', '', $this->mapname);

    $this->hostport = $this->queryport;
    $this->maxplayers = $this->rules['sv_maxclients']-$this->rules['sv_privateClients'];

    //get playerdata
    $temp=substr($result,strlen($temp[0])+strlen($temp[1])+1,strlen($result));
    $allplayers=explode("\n", $temp);
    $this->numplayers=count($allplayers)-2;

    // get players
    if(count($allplayers)-2 && $getPlayers) {
      for($i=1;$i< count($allplayers)-1;$i++) {
	// match with team info
	if(preg_match("/(\d+)[^0-9](\d+)[^0-9](\d+)[^0-9]\"(.*)\"/", $allplayers[$i], $curplayer)) {
	  if($curplayer[3]>2) {
	    next; // ignore spectators
	  }
	  $players[$i-1]['name']=$curplayer[4];
	  $players[$i-1]['score']=$curplayer[1];
	  $players[$i-1]['ping']=$curplayer[2];
	  $players[$i-1]['team']=$curplayer[3];
	  $teamInfo=TRUE;
	  $pingOnly=FALSE;
	} elseif(preg_match("/(\d+)[^0-9](\d+)[^0-9]\"(.*)\"/", $allplayers[$i], $curplayer)) {
	  $players[$i-1]['name']=$curplayer[3];
	  $players[$i-1]['score']=$curplayer[1];
	  $players[$i-1]['ping']=$curplayer[2];
	  $pingOnly=FALSE;
	  $teamInfo=FALSE;
	}
	else {
	  if(preg_match("/(\d+).\"(.*)\"/", $allplayers[$i], $curplayer)) {
	    $players[$i-1]['name']=$curplayer[2];
	    $players[$i-1]['ping']=$curplayer[1];
	    $pingOnly=TRUE; // for MoHAA
	  }
	  else {
	    $this->errstr='Could not extract player infos!';
	    return FALSE;
	  }
	}
      }
      $this->playerkeys['name']=TRUE;
      if(!$pingOnly) {
	$this->playerkeys['score']=TRUE;
	if($teamInfo) {
	  $this->playerkeys['team']=TRUE;
	}
      }
      $this->playerkeys['ping']=TRUE;
      $this->players=$players;
    }

    $this->online = TRUE;
    return TRUE;
  }


  /**
   * @brief htmlizes the given raw string
   *
   * @param var a raw string from the gameserver that might contain special chars
   * @return a html version of the given string
   */
  function htmlize($var)
  {
    $len = strlen($var);
    $numTags = 0;
    $result = '';
    $var .= '  '; // padding
    $colortag = '<span class="gsquery-%s-%s">';

    switch($this->gamename) {
    case 'q3a_Call_of_Duty':
    case 'q3a_sof2':
      $csstype = 'q3a_exdended';
      break;
    default:
      $csstype = 'q3a';
    }

    for($i=0;$i<$len;$i++) {
      // checking for a color code
      if($var[$i] == '^') {
	$numTags++; // count tags
	switch($var[++$i]) {
	// a lot of special chars that can't be used for css names
	case '<':
	  $result .= sprintf($colortag, $csstype, 'less');
	  break;
	case '>':
	  $result .= sprintf($colortag, $csstype, 'greater');
	  break;
	case '&':
	  $result .= sprintf($colortag, $csstype, 'and');
	  break;
	case '\'':
	  $result .= sprintf($colortag, $csstype, 'tick');
	  break;
	case '=':
	  $result .= sprintf($colortag, $csstype, 'equal');
	  break;
	case '?':
	  $result .= sprintf($colortag, $csstype, 'questionmark');
	  break;
	case '.':
	  $result .= sprintf($colortag, $csstype, 'point');
	  break;
	case ',':
	  $result .= sprintf($colortag, $csstype, 'comma');
	  break;
	case '!':
	  $result .= sprintf($colortag, $csstype, 'exc');
	  break;
	case '*':
	  $result .= sprintf($colortag, $csstype, 'star');
	  break;
	case '$':
	  $result .= sprintf($colortag, $csstype, 'dollar');
	  break;
	case '#':
	  $result .= sprintf($colortag, $csstype, 'pound');
	  break;
	case '(':
	  $result .= sprintf($colortag, $csstype, 'lparen');
	  break;
	case ')':
	  $result .= sprintf($colortag, $csstype, 'rparen');
	  break;
	case '@':
	  $result .= sprintf($colortag, $csstype, 'at');
	  break;
	case '%':
	  $result .= sprintf($colortag, $csstype, 'percent');
	  break;
	case '+':
	  $result .= sprintf($colortag, $csstype, 'plus');
	  break;
	case '|':
	  $result .= sprintf($colortag, $csstype, 'bar');
	  break;
	case '{':
	  $result .= sprintf($colortag, $csstype, 'lbracket');
	  break;
	case '}':
	  $result .= sprintf($colortag, $csstype, 'rbracket');
	  break;
	case '"':
	  $result .= sprintf($colortag, $csstype, 'quote');
	  break;
	case ':':
	  $result .= sprintf($colortag, $csstype, 'colon');
	  break;
	case '[':
	  $result .= sprintf($colortag, $csstype, 'lsqr');
	  break;
	case ']':
	  $result .= sprintf($colortag, $csstype, 'rsqr');
	  break;
	case '\\':
	  $result .= sprintf($colortag, $csstype, 'lslash');
	  break;
	case '/':
	  $result .= sprintf($colortag, $csstype, 'rslash');
	  break;
	case ';':
	  $result .= sprintf($colortag, $csstype, 'semic');
	  break;
	case '^': // double color code
	  $result .= '^<span class="gsquery-'. $csstype .'-'. $var[++$i] .'">';
	  break;
	default:
	  // normal color code
	  $result .= sprintf($colortag, $csstype, $var[$i]);
	}
      } else {
	// normal char
	$result .= htmlentities($var[$i]);
      }
    }

    // appending numTags spans
    return $result . str_repeat('</span>', $numTags);
  }
}
?>