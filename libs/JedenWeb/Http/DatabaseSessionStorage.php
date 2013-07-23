<?php

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
		
        while (!$this->connection->query("SELECT IS_FREE_LOCK('session_$id') AS free")->fetch()->free);
		
		$this->connection->query("SELECT GET_LOCK('session_$id', 1)");

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
            $data = "";
		}
		
        return $data;
    }

	
	/**
	 * @param int $id
	 * @param type $data
	 * @throws \Nette\InvalidStateException
	 */
    public function write($id, $data = "")
	{
		if ($row = $this->connection->table('session')->get($id)) {
			$row->update(array(
				'timestamp' => time(),
				'data' => $data,
			));
		} else {
			$this->connection->table('session')->insert(array(
				'id' => $id,
				'timestamp' => time(),
				'data' => $data,
			));
		}
		
		return TRUE;
    }

	
	/**
	 * @param int $max
	 * @return boolean
	 */
    public  function clean($max)
	{		
        $this->connection->table('session')->where("timestamp < ?", ( time() - $max ))->delete();
		
        return TRUE;
    }


	/**
	 * @return boolean
	 */
    public function close()
	{
        $id = session_id();
		
        $this->connection->query("SELECT RELEASE_LOCK('session_$id')");
		
        return TRUE;
    }


	/**
	 * @param mixed $id
	 * @return boolean
	 */
	public function remove($id)
	{
		if ($row = $this->connection->table('session')->get($id)) {
			$row->delete();
		}

        return TRUE;
	}
	
	
	/**
	 * @return boolean
	 */
	public function destroy()
	{
		return (bool) $this->connection->table('session')->delete();
	}


	
	public function __destruct()
	{
		session_write_close();
	}

}
