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

require_once GSQUERY_DIR . 'gsQuery.php';

/**
 * @brief Abstract class that implements quake related stuff
 * @author Jeremias Reith (jr@gsquery.org)
 * @author Narfight (narfight@lna.be)
 * @version $Rev: 195 $
 *
 * Implements everything that all quake protocols have in common
 */
class quake extends gsQuery
{

  /**
   * @brief Sends a rcon command to the game server
   *
   * @param command the command to send
   * @param rcon_pwd rcon password to authenticate with
   * @return the result of the command or FALSE on failure
   */
  function rcon_query_server($command, $rcon_pwd)
  {
    $command="\xFF\xFF\xFF\xFF\x02rcon ".$rcon_pwd." ".$command."\x0a\x00";
    if(!($result=$this->_sendCommand($this->address,$this->queryport,$command))) {
      $this->errstr="Error sending rcon command";
      $this->debug['Command send ' . $command]='No reply received';
      return FALSE;
    } else {
      return $result;
    }
  }
}
?>