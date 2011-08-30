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
 * @brief This class implements the protocol used by Neverwinter Nights
 * @author Michael Feld <gsquery@mftronic.de>
 * @version $Revision: 190 $
 * @todo Add player support
 */
class nwn extends gsQuery
{
  function query_server($getPlayers=TRUE, $getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    $cmd = "\xFE\xFD\x00\xE0\xEB\x2D\x0E\x14\x01\x0B\x01\x05\x08\x0A\x33\x34\x35\x13\x04\x36\x37\x38\x39\x14\x3A\x3B\x3C\x3D\x00\x00";
    if(!($response = $this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr = "No reply received";
      return FALSE;
    }
    $lines = explode("\x00", $response);
    $this->servertitle = $lines[3]; // Game Name
    $this->gametype = $lines[2]; // Play Type
    $this->gameversion = $lines[14]; // Version Number
    $this->mapname = $lines[4]; // Module Name
    $this->password = $lines[10];
    $this->numplayers = $lines[5];
    $this->maxplayers = $lines[6];
    //$this->maptitle = $lines[15]; // Server Description

    // Additional stuff
    if($getRules) {
      $this->rules['xp1'] = $lines[20]; // Shadows of Udrentide
      $this->rules['level_min'] = $lines[7];
      $this->rules['level_max'] = $lines[8];
      $this->rules['pvp'] = $lines[9]; // Player vs. Player
      $this->rules['local_chars'] = $lines[19];
      $this->rules['only_one_party'] = $lines[12];
      $this->rules['player_pause'] = $lines[13];
      $this->rules['item_level_restr'] = $lines[18];
      $this->rules['legal_chars'] = $lines[17];
    }

    $this->online = TRUE;
    return TRUE;
  }
}

?>