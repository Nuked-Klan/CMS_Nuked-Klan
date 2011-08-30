<?php
if (!defined("INDEX_CHECK"))
{
	exit('You can\'t run this file alone.');
}

/*
 *  gsQuery - Querys game servers
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

require_once GSQUERY_DIR . 'gameSpy.php';

/**
 * @brief Extends the gameSpy protocol to support Vietkong
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Id: vietkong.php 190 2004-09-25 15:48:06Z jr $
 * @todo process rules
 *
 * Vietkong's default query port seems to be 15426.
 * Vietkong does not provide a ganename.
 * This class takes note of the changed vietkong query commands.
 * Rules are currently not processed.
 */
class vietkong extends gameSpy
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // flushing old data if necessary
    if($this->online) {
      $this->_init();
    }

    $cmd="\\status\\";
    if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
      $this->errstr='No reply received';
      return FALSE;
    }
    $this->_processServerInfo($response);

    $this->online=TRUE;

    // get players
    if($this->numplayers && $getPlayers) {
      $cmd="\\players\\";
      if(!($response=$this->_sendCommand($this->address, $this->queryport, $cmd))) {
	return FALSE;
      }

      $this->_processPlayers($response);
    }

    $this->gamename='vietkong';
    return TRUE;
  }

  function _getClassName()
  {
    return get_class($this);
  }
}

?>