<?php
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
if (!defined("INDEX_CHECK"))
{
	exit('You can\'t run this file alone.');
}

class steam extends gsQuery
{
  var $playerFormat = '/sscore/x2/ftime';

  function getGameJoinerURI()
  {
    return 'gamejoin://hlife@'. $this->address .':'. $this->hostport .'/'. $this->gametype;
  }

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }
    $command="\xFF\xFF\xFF\xFF\x54\x53\x6F\x75\x72\x63\x65\x20\x45\x6E\x67\x69\x6E\x65\x20\x51\x75\x65\x72\x79\x00";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      return FALSE;
    }

    $this->hostport = $this->queryport;

   $i=4;// start after header
    $this->rules['Type']=($result[$i++]=='I' ? 'Source' : 'HL1');
   if ($this->rules['Type']=='Source') {
    $this->rules['NetworkVersion']=ord(substr($result,$i++,1));
     while ($result[$i]!=chr(0)) $this->servertitle.=$result[$i++];
     $i++;
     while ($result[$i]!=chr(0)) $this->mapname.=$result[$i++];
    $i++;
    while ($result[$i]!=chr(0)) $this->rules['gamedir'].=$result[$i++];
     $i++;
     while ($result[$i]!=chr(0)) $this->gamename.=$result[$i++];
     $i++;
     $this->rules['appid']=ord(substr($result,$i,2));$i=$i+2;
     $this->numplayers=ord(substr($result,$i++,1));
     $this->maxplayers=ord(substr($result,$i++,1));
     $this->rules['botplayers']=ord(substr($result,$i++,1));
     $this->rules['dedicated']=($result[$i++]=='d' ? 'Yes' : 'No');
     $this->rules['server_os']=($result[$i++]=='l' ? 'Linux' : 'Windows');
     $this->password=ord(substr($result,$i++,1));
     $this->rules['secure']=($result[$i++]=='1' ? 'Yes' : 'No');
     while ($result[$i]!=chr(0)) $this->gameversion.=$result[$i++];
     $i++;
   } else { //For HL 1
    while ($result[$i]!=chr(0)) $this->rules['IP'].=$result[$i++];
     $i++;
     while ($result[$i]!=chr(0)) $this->servertitle.=$result[$i++];
     $i++;
     while ($result[$i]!=chr(0)) $this->mapname.=$result[$i++];
    $i++;
    while ($result[$i]!=chr(0)) $this->rules['gamedir'].=$result[$i++];
     $i++;
     while ($result[$i]!=chr(0)) $this->rules['gamename'].=$result[$i++];
    //while ($result[$i]!=chr(0)) $this->gamename.=$result[$i++];
     $i++;
     $this->numplayers=ord(substr($result,$i++,1));
     $this->maxplayers=ord(substr($result,$i++,1));
     $this->gameversion=ord(substr($result,$i++,1)); if ($this->gameversion==47) $this->gameversion.=' (1.6)';
     $this->rules['dedicated']=($result[$i++]=='d' ? 'Yes' : 'No');
     $this->rules['server_os']=($result[$i++]=='l' ? 'Linux' : 'Windows');
     $this->password=ord(substr($result,$i++,1));
     $this->rules['secure']=($result[$i++]=='1' ? 'Yes' : 'No');
     while ($result[$i]!=chr(0)) $this->rules['mod_url'].=$result[$i++];
     $i++;
   }

    // do rules
    //challange
   $command="\xFF\xFF\xFF\xFF\x57";
   if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
     return FALSE;
    }
    $challenge=substr($result,-4);
    //query
   $command="\xFF\xFF\xFF\xFF\x56";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command.$challenge))) {
      return FALSE;
    }

   if ($this->rules['Type']=='HL1') {
      // rules can be in multiple packets in 1.6, we have to sort it out
       // First packet has a 16 byte header, subsequent packet has an 8 byte header.
       $str="/\xFE\xFF\xFF\xFF/";// packet signature (both first and second start with this)

       $block=preg_split($str,$result,-1,PREG_SPLIT_NO_EMPTY);

       $str="/\xFF\xFF\xFF\xFF/"; // first packet signature (only first packet matches this)

      if(!empty($block[0]) && !empty($block[1])) {
       if(preg_match($str, $block[0])) {
       $result = substr($block[0], 12, strlen($block[0])).substr($block[1], 5, strlen($block[1]));
       } elseif(preg_match($str, $block[1])) {
       $result = substr($block[1], 12, strlen($block[1])).substr($block[0], 5, strlen($block[1])).substr($block[0], 5, strlen($block[0]));
        }
       } elseif (!empty($block[0])) {
        $result = substr($block[0], 5, strlen($block[0]));
       }
     $j=0; //beginning value off for
   } else {
     $j=1; //beginning value off for
    }

    $exploded_data = explode(chr(0), $result);
    $this->password = -1;
     $z=count($exploded_data);
     for($i=$j;$i<$z;$i++) {
       switch($exploded_data[$i++]) {
       case 'sv_password':
    $this->password=$exploded_data[$i];
    break;
       case 'deathmatch':
    if ($exploded_data[$i]=='1') $this->gametype='Deathmatch';
    break;
       case 'coop':
    if ($exploded_data[$i]=='1') $this->gametype='Cooperative';
    break;
       default:
       if(isset($exploded_data[$i-1]) && isset($exploded_data[$i])) {
         $this->rules[$exploded_data[$i-1]]=$exploded_data[$i];
       }
       }
     }


    if($getPlayers) {
      //challange
     $command="\xFF\xFF\xFF\xFF\x57";
     if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
       return FALSE;
      }
      $challenge=substr($result,-4);
      //query
     $command="\xFF\xFF\xFF\xFF\x55";
      if(!($result=$this->_sendCommand($this->address,$this->queryport,$command.$challenge))) {
       return FALSE;
      }
      $this->_processPlayers($result, $this->playerFormat, 8);

      $this->playerkeys['name']=TRUE;
      $this->playerkeys['score']=TRUE;
      $this->playerkeys['time']=TRUE;
    }

    $this->online = TRUE;
    return TRUE;
  }


  function getDebugDumps($html=FALSE, $dumper=NULL) {
    require_once(GSQUERY_DIR . 'includes/HexDumper.class.php');

    if(!isset($dumper)) {
      $dumper = new HexDumper();
      $dumper->setDelimiter(0x00);
      $dumper->setEndOfHeader(0x04);
    }

    return parent::getDebugDumps($html, $dumper);
  }


  function _processPlayers($data, $format, $formatLength)
  {
    $len = strlen($data);

    $posNextPlayer=$len;

    for($i=6;$i<$len;$i=$endPlayerName+$formatLength+1) {
      // finding end of player name
      $endPlayerName = strpos($data, "\x00", ++$i);
      if($endPlayerName == FALSE) { return FALSE; } // abort on bogus data
      // unpacking player's score and time
      $curPlayer = unpack('@'.($endPlayerName+1).$format, $data);
      // format time
      if(array_key_exists('time', $curPlayer)) {
   $curPlayer['time'] = date('H:i:s', mktime(0, 0, $curPlayer['time']));
      }
      // extract player name
      $curPlayer['name'] = substr($data, $i, $endPlayerName-$i);
      // add player to the list of players
      $this->players[] = $curPlayer;
    }
  }


}

?>