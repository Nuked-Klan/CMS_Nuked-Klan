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

require_once GSQUERY_DIR . 'gameSpy.php';

/**
 * @brief Extends the gameSpy protocol to Deus EX
 * @author Jeremias Reith (jr@gsquery.org)
 * @version $Result$
 *
 */
class deusEX extends gameSpy
{

  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    // doing a normal basic info query
    // only a game name is given
    if(!parent::query_server(FALSE, FALSE)) {
      return FALSE;
    }

    $debug = $this->debug;
    $this->infoCommand = '\\rules\\';

    // processing rules as basic info
    // basic infos are included if and only if a basic query has been issued before
    $result = parent::query_server($getPlayers, FALSE);

    $this->debug = array_merge($debug, $this->debug);

    // maptitle is 'Untitled'
    $this->maptitle = '';

    return $result;
  }

  function _getClassName()
  {
    return 'deusEX';
  }
}

?>