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
	private $db;


	/**
	 * @param \Nette\Database\Connection $param
	 */
	public function __construct(Ntte\Database\Connection $db)
	{
		throw new \JedenWeb\NotImplementedException;
		$this->db = $db;
	}


	/**
	 * @param string $savePath
	 * @param string $sessionName
	 */
    public	function open($savePath, $sessionName)
	{
		\Nette\Diagnostics\Debugger::dump($savePath);
		\Nette\Diagnostics\Debugger::dump($sessionName);die;
    }

	
	/**
	 * @param int $id
	 * @return string
	 */
    public  function read($id)
	{
        $query = '
            SELECT
                [data]
            FROM [session]
            WHERE
                [id] = %s';
        try {
            $result = $this->conn->query($query, $id);
            return $result->fetchSingle();
        } catch (\Exception $e) {

            $this->conn->query('CREATE TABLE [session] ([id] varchar(32) not null primary key, [timestamp] timestamp not null, [data] text)');
            $this->conn->query('CREATE INDEX [session_by_timestamp] ON [session] ([timestamp])');

            return '';
        };
    }

	
	/**
	 * @param int $id
	 * @param type $data
	 * @throws \Nette\InvalidStateException
	 */
    public  function write($id, $data)
	{
        if (is_null($this->conn)) {
            throw new \Nette\InvalidStateException("The connection to database for session storage is not open!");
        };

        $this->conn->begin();
        $this->conn->query('DELETE FROM [session] WHERE [id] = %s', $id);
        $this->conn->query('INSERT INTO [session] VALUES(%s, %s, %s)', $id, time(), $data);
        $this->conn->commit();
    }

	
	/**
	 * @param int $id
	 * @throws \Nette\InvalidStateException
	 */
    public  function destroy($id)
	{
        if (is_null($this->conn)) {
            throw new \Nette\InvalidStateException("The connection to database for session storage is not open!");
        };

        $this->conn->query('DELETE FROM [session] WHERE [id] = %s', $id);
    }

	
	/**
	 * @param int $max
	 * @throws \Nette\InvalidStateException
	 */
    public  function clean($max)
	{
        if (is_null($this->conn)) {
            throw new \Nette\InvalidStateException("The connection to database for session storage is not open!");
        };

        $old = (time() - $max);
        $this->conn->query('DELETE FROM [session] WHERE [timestamp] < %s', $old);
    }



    public  function close()
	{
        $this->conn = null;
    }



	public function remove($id)
	{
	}



	public function __destruct()
	{
		session_write_close();
	}

}
