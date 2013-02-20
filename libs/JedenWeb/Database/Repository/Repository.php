<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Database\Repository;

use JedenWeb;
use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Repository extends Nette\Object
{

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @var \Nette\Database\Connection
	 */
	protected $connection;



	/**
	 * @param string $table
	 */
	public function __construct($table)
	{
		$this->table = $table;
	}



	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getTable()
	{
		return $this->connection->table($this->table);
	}

	

	/**
	 * @param \Nette\Database\Connection $connection
	 * @throws \Nette\InvlidStateException
	 */
	public function injectConnection(\Nette\Database\Connection $connection)
	{
		if ($this->connection) {
			throw new \Nette\InvlidStateException;
		}
		$this->connection = $connection;
	}

}
