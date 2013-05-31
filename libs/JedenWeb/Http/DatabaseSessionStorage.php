<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Http;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class DatabaseSessionStorage extends Nette\Object implements Nette\Http\ISessionStorage
{

	/** @var \Nette\Database\Connection */
	private $connection;

	

	/**
	 * @param \Nette\Database\Connection $param
	 */
	public function __construct(Nette\Database\Connection $connection)
	{
		$this->connection = $connection;
	}


	/**
	 * @param string $savePath
	 * @param string $sessionName
	 */
    public	function open($savePath, $sessionName)
	{
		$id = session_id();

        while (!$this->connection->query("SELECT IS_FREE_LOCK('session_$id') AS locked")->fetch()->locked);
        
		$this->connection->exec("SELECT GET_LOCK('session_$id', 160)");

        return TRUE;
    }

	
	/**
	 * @param int $id
	 * @return string
	 */
    public  function read($id)
	{		
        if ($data = $this->connection->table('session')->get($id)) {
            $data = $data->data;
		} else {
            $data = NULL;
		}
		
        return $data;
    }

	
	/**
	 * @param int $id
	 * @param type $data
	 * @throws \Nette\InvalidStateException
	 */
    public  function write($id, $data)
	{
//		$row = $this->connection->table('session')->find($id);
//		$this->connection->exec("REPLACE INTO session (id, timestamp, data) VALUES (?, ?, ?)", $id, time(), $data);
		
        $this->connection->query('DELETE FROM session WHERE id = ?', $id);
        $this->connection->query('INSERT INTO session VALUES (?, ?, ?)', $id, time(), $data);
		
		return TRUE;
    }

	
	/**
	 * @param int $max
	 * @return boolean
	 */
    public  function clean($max)
	{
        $this->connection->table('session')->where("time < ?", ( time() - $maxlifetime ))->delete();
        return TRUE;
    }


	/**
	 * @return boolean
	 */
    public  function close()
	{
        $id = session_id();

        $this->connection->exec("SELECT RELEASE_LOCK('session_$id')");
		
        return TRUE;
    }


	/**
	 * @param mixed $id
	 * @return boolean
	 */
	public function remove($id)
	{
		$this->connection->table('session')->find($id)->delete();

        return TRUE;
	}



	public function __destruct()
	{
		session_write_close();
	}

}
