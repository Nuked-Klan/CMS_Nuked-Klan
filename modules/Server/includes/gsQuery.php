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

// define path to gsQuery if not done yet
if(!defined('GSQUERY_DIR')) {
  define('GSQUERY_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

/**
 * @mainpage gsQuery
 * @htmlinclude readme.html
 */

/**
 * @example small_example.php
 * This is a simple example of how to use gsQuery
 */

/**
 * @example example_usage.php
 * This is a detailed example of how to use gsQuery
 */

/**
 * @brief Abstract gsQuery base class
 * @author Jeremias Reith (jr@terragate.net)
 * @version $Rev: 204 $
 *
 * <p>The gsQuery package has one class for each protocol or game.
 * This class is abstract but due to the lack of real OO
 * capabilities it cannot be declared as abstract.
 * Use the static method createInstance to create a gsQuery object
 * that supports the specified protocol.</p>
 *
 * Generic usage:
 * <pre>
 *   // including gsQuery
 *   include_once('path/to/gsQuery/gsQuery.php');
 *
 *   // create a gsQuery instance
 *   $gameserver = gsQuery::createInstance('gameSpy', 'myserver.com', 1234)
 *
 *   // query the server
 *   $status = $gameserver->query_server();
 *
 *   // check for success
 *   if($status) {
 *     // process retrieved data
 *   } else {
 *     // create an error message
 *   }
 * </pre>
 */
class gsQuery
{

  // public members you can access

  /** @brief The version of the gsQuery package */
  var $version = '$$$SVN_VERSION$$$'; //= preg_replace("`[^0-9]`", '', '$Rev: 204 $');

  /** @brief ip or hostname of the server */
  var $address;

  /** @brief port to use for the query */
  var $queryport;

  /**
   * @brief status of the server
   *
   * TRUE: server online, FALSE: server offline
   */
  var $online;

  /** @brief the name of the game */
  var $gamename;

  /** @brief the port you have to connect to enter the game */
  var $hostport;

  /** @brief the version of the game */
  var $gameversion;

  /** @brief The title of the server */
  var $servertitle;

  /** @brief The name of the map (often corresponds with the filename of the map)*/
  var $mapname;

  /** @brief A more descriptive name of the map */
  var $maptitle;

  /** @brief The gametype */
  var $gametype;

  /** @brief current number of players on the server */
  var $numplayers;

  /** @brief maximum number of players allowed on the server */
  var $maxplayers;

  /**
   * @brief Wheather the game server is password protected
   *
   *  1: server is password protected<br>
   *  0: server is not password protected<br>
   * -1: unknown
   */
  var $password;

  /** @brief next map on the server */
  var $nextmap;

  /**
   * @brief players playing on the server
   * @see playerkeys
   *
   * Hash with player ids as key.
   * The containing value will be another hash with the infos of the player.
   * To access a player name use <code>players[$playerid]['name']</code>.
   * Check playerkeys to get the keys available
   */
  var $players;

  /**
   * @brief Hash of available player infos
   *
   * There is a key for each player info available (e.g. name, score, ping etc).
   * The value is TRUE if the info is available
   */
  var $playerkeys;

  /** @brief list of the team names */
  var $playerteams;

  /** @brief a list of all maps in cycle */
  var $maplist;

  /**
   * @brief Hash with all server rules
   *
   * key:   rulename<br>
   * value: rulevalue
   */
  var $rules;

  /** @brief Short errormessage if something goes wrong */
  var $errstr;

  /**
   * @brief Array with debug infos
   *
   * Stores all the send/received data
   * Format: send data => received data
   */
  var $debug;

  /**
   * @brief Standard constructor
   *
   * @param address address of the server to query
   * @param queryport the queryport of the server
   */
  function gsQuery($address, $queryport)
  {
    $this->version = '(SVN Rev '. preg_replace("`[^0-9\\.]`", '', '$Rev: 204 $') .')';
    $this->address = $address;
    $this->queryport = $queryport;

    $this->_init();
  }

  /**
   * @brief Creates a new gsQuery object that supports the given protocol
   *
   * This static method will create an instance of the appropriate subclass
   * for you.
   *
   * @param protocol the protocol you need
   * @param address the address of the game server
   * @param port the queryport of the game server
   * @return a gsQuery object that supports the specified protocol
   *
   */
  static function createInstance($protocol, $address, $port) {

    // including the required class and create an instance of it
    switch($protocol) {
    // some aliases might be useful
    case('gsqp'):
      require_once GSQUERY_DIR . 'gameSpy.php';
      return new gameSpy($address, $port);
    case 'halflife':
      require_once GSQUERY_DIR . 'hlife.php';
      return new hlife($address, $port);
     case 'ravenshield':
      require_once GSQUERY_DIR . 'rvnshld.php';
      return new rvnshld($address, $port);
    default:
      // we should be careful when using eval with supplied arguments
      // e.g.: $port="123); system(\"some nasty stuff\")";
      // normally this should be assured by the caller, but we are in reality
      if(preg_match("`^[A-Za-z0-9_-]+$`", $protocol) && preg_match("`^[A-Za-z0-9\\.-]+$`", $address) && is_numeric($port)) {
	require_once(GSQUERY_DIR . $protocol .'.php');
	return eval("return new $protocol(\"$address\", $port);");
      } else {
	return FALSE;
      }
    }
  }

  /**
   * @brief Creates an instance out of an previously serialized string
   *
   * Use this to restore a object that has been previously serialized with
   * serialize
   *
   * @param string serialized gsQuery object
   * @return the deserialized object
   */
  function unserialize($string)
  {
    // extracting class name
    $length = strlen($string);
    for($i=0;$i<$length;$i++) {
      if($string[$i] == ':') {
	break;
      }
    }

    $className = substr($string, 0, $i);

    // we should be careful when using eval with supplied arguments
    if(preg_match("`^[A-Za-z0-9_-]+$`", $className)) {
      include_once(GSQUERY_DIR. $className .'.php');
       return unserialize(base64_decode(substr($string, $i+1)));
     } else {
      return FALSE;
    }
  }

  /**
   * @brief Retrieves a serialized object via HTTP and deserializes it
   *
   * Useful if UDP traffic isn't allowed
   *
   * @param url the URL of the object
   * @return the deserialized object
   */
  function unserializeFromURL($url)
  {
    require_once(GSQUERY_DIR . 'includes/HttpClient.class.php');
    return gsQuery::unserialize(HttpClient::quickGet($url));
  }

  /**
   * @brief Returns all supported protocols / games
   *
   * This method is static.
   * There should be no other php files in the gsQuery directory
   *
   * @return An array with names of the supported protocols
   */
  function getSupportedProtocols()
  {
    if(!$handle=opendir(GSQUERY_DIR)) {
       return FALSE;
    }

    $result=array();

    while(false!==($curfile=readdir($handle))) {
      if($curfile!='gsQuery.php' && $curfile!='index.php' && preg_match("`^(.*)\.php$`", $curfile, $matches)) {
	array_push($result, $matches[1]);
      }
    }
    closedir($handle);
    return $result;
  }


  /**
   * @brief Returns a GameJoiner URI
   * @see http://gamejoiner.terragate.net
   *
   * The server has to be queried before in some cases
   * (to find out the game port).
   * The client needs GameJoiner to join the game with this URI
   *
   * @return A GameJoiner URI
   */
  function getGameJoinerURI()
  {
    return 'gamejoin://'. $this->gamename .'@'. $this->address .':'. $this->hostport .'/';
  }

  /**
   * @brief Returns a native join URI
   *
   * Some games are registering an URI type to allow easy joining of games
   *
   * @return a native join URI or false if not implemented for the game
   */
  function getNativeJoinURI()
  {
    return FALSE;
  }

  /**
   * @brief Querys the server
   *
   * This method is abstract
   *
   * @param getPlayers wheather to retrieve player infos
   * @param getRules wheather to retrieve rules
   * @return TRUE on success
   */
  function query_server($getPlayers=TRUE,$getRules=TRUE)
  {
    $this->errstr = 'This class cannot be used to query a server';
    return FALSE;
  }

  /**
   * @brief Sorts the given players
   *
   * You can sort by name, score, frags, deaths, honor and time
   *
   * @param players players to sort
   * @param sortkey sort by the given key
   * @return sorted player hash
   */
  function sortPlayers($players, $sortkey='name')
  {
    if(!sizeof($players)) {
      return array();
    }
    switch($sortkey) {
    default:
    case 'name':
      uasort($players, array('gsQuery', '_sortbyName'));
      break;
    case 'score':
      uasort($players, array('gsQuery', '_sortbyScore'));
      break;
    case 'frags':
      uasort($players, array('gsQuery', '_sortbyFrags'));
      break;
    case 'deaths':
      uasort($players, array('gsQuery', '_sortbyDeaths'));
      break;
    case 'kills':
      uasort($players, array('gsQuery', '_sortbyKills'));
      break;
    case 'time':
      uasort($players, array('gsQuery', '_sortbyTime'));
      break;
    }
    return ($players);
  }

  /**
   * @brief htmlizes the given raw string
   *
   * @param string a raw string from the gameserver that might contain special chars
   * @return a html version of the given string
   */
  function htmlize($string)
  {
    return htmlentities($string);
  }

  /**
   * @brief converts the raw string to ascii
   *
   * @param string a raw string from the gameserver that might contain special chars
   * @return a plain text version of the given string
   */
  function textify($string)
  {
    return $string;
  }

  /**
   * @brief serializes the object as string
   * @return serialized object representation
   *
   */
  function serialize()
  {
    return $this->_getClassName() .':'. base64_encode(serialize($this));
  }

  /**
   * @brief Creates hexdumps out of the debug info
   *
   * @param html whether to create an html hexdump
   * @param dumper an optional preconfigured HexDumper instance
   * @return an array with hexdumps for each send command/received result
   *
   * Pass an HexDumper instance if you want to format the dump yourself.
   * Each element of the result will contain a 2 element array with the hexdump
   * of the send data first and the dump of the received data behind it.
   */
  function getDebugDumps($html=FALSE, $dumper=NULL) {
    require_once(GSQUERY_DIR . 'includes/HexDumper.class.php');

    if(!isset($dumper)) {
      $dumper = new HexDumper();
    }

    $dumps = array();
    $dumpFunction = array($dumper, $html ? 'createHTMLDump' : 'createASCIIDump');

    foreach ($this->debug as $curCommand) {
      $dumps[] = array_map($dumpFunction, $curCommand);
    }

    return $dumps;
  }

  // private member functions

  // better idea?
  function _sortbyName($a, $b)
  {
    return(strcasecmp($a['name'], $b['name']));
  }

  function _sortbyScore($a, $b)
  {
    if($a['score']==$b['score']) { return 0; }
    elseif($a['score']<$b['score']) { return 1; }
    else { return -1; }
  }

  function _sortbyFrags($a, $b)
  {
    if($a['frags']==$b['frags']) { return 0; }
    elseif($a['frags']<$b['frags']) { return 1; }
    else { return -1; }
  }

  function _sortbyDeaths($a, $b)
  {
    if($a['deaths']==$b['deaths']) { return 0; }
    elseif($a['deaths']<$b['deaths']) { return 1; }
    else { return -1; }
  }

  function _sortbyTime($a, $b)
  {
    if($a['time']==$b['time']) { return 0; }
    elseif($a['time']<$b['time']) { return 1; }
    else { return -1; }
  }

  function _sortbyKills($a, $b)
  {
    if($a['kills']==$b['kills']) { return 0; }
    elseif($a['kills']<$b['kills']) { return 1; }
    else { return -1; }
  }

  /**
   * @internal @brief This method deletes all fetched data
   *
   * This method should be called if an instance is used for multiple querys
   */
  function _init()
  {
    $this->online = FALSE;
    $this->hostport = 0;
    $this->gameversion = '';
    $this->servertitle = '';
    $this->hostport = '';
    $this->mapname = '';
    $this->maptitle = '';
    $this->gametype = '';
    $this->numplayers = 0;
    $this->maxplayers = 0;
    $this->password = -1;
    $this->nextmap='';

    $this->players = array();
    $this->playerkeys = array();
    $this->playerteams = array();
    $this->maplist = array();
    $this->rules = array();
    $this->debug = array();

    $this->errstr='';
  }



  /**
   * @internal @brief sends a command to a server and returns the answer
   *
   * @param address ip or hostname of the server
   * @param port port to connect to
   * @param command data to send
   * @param timeout how long to wait for data (in seconds)
   * @return the raw answser
   *
   */
  function _sendCommand($address, $port, $command, $timeout=500000)
  {
    if(!$socket=@fsockopen('udp://'.$address, $port)) {
      $this->errstr='Cannot open a socket!';
      return FALSE;
    } else {
      socket_set_blocking($socket, true);
      // socket_set_timeout should be used here but this requires PHP >=4.3
      socket_set_timeout($socket, 0, $timeout);

      // send command
      if(fwrite($socket, $command, strlen($command))==-1) {
	fclose($socket);
	$this->errstr='Unable to write on open socket!';
	return FALSE;
      }

      $result='';
      do {
	$result .= fread($socket, 128);
	$socketstatus = socket_get_status($socket);
      } while ($socketstatus['unread_bytes']);

      fclose($socket);
      if(!isset($result)) {
	$this->debug[] = array($command, '');
	return FALSE;
      }
      $this->debug[] = array($command, $result);
      return $result;
    }
  }

  /**
   * @brief returns the class name of the instance
   * @return the class name of the instance
   *
   * Override this for mixed case class names and support for PHP <5
   */
  function _getClassName()
  {
    return get_class($this);
  }

  /**
   * @brief Serialization handler
   * @return array of variable names to serialize
   */
  function __sleep()
  {
    // do not serialize debug info to keep the result small
    return array('version',
		 'address',
		 'queryport',
		 'gamename',
		 'hostport',
		 'online',
		 'gameversion',
		 'servertitle',
		 'mapname',
		 'maptitle',
		 'gametype',
		 'numplayers',
		 'maxplayers',
		 'password',
		 'nextmap',
		 'players',
		 'playerkeys',
		 'playerteams',
		 'maplist',
		 'rules',
		 'errstr'
		 );
  }
}
?>