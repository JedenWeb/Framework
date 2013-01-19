<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Database;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 *
 * @property int $id
 */
abstract class BaseEntity extends Nette\Object implements \ArrayAccess
{

	/**
	 * @var \Nette\Database\Table\ActiveRow
	 */
	protected $entity;



	/**
	 * @param Nette\Database\Table\ActiveRow $entity
	 */
	public function __construct(Nette\Database\Table\ActiveRow $entity)
	{
		$this->entity = $entity;
	}



	/**
	 * @return \Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		return $this->entity->getTable();
	}



	/**
	 * @return \Nette\Database\Connection
	 */
	protected function getConnection()
	{
		return $this->getTable()->getConnection();
	}



	/**
	 * @param string $name
	 * @return mixed
	 * @throws \JedenWeb\Database\Entity\MemberAccessException
	 */
	public function &__get($name)
	{
		try {
			return parent::__get($name);
		} catch (\Nette\MemberAccessException $e) {
			if ($this->entity->offsetExists($name)) {
				$value = $this->entity->$name; // must assign to new variable
				return $value; // to it could return referrence
			}

			throw $e;
		}
	}



	/**
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 * @throws \JedenWeb\Database\Entity\MemberAccessException
	 */
	public function __call($name, $args)
	{
		try {
			parent::__call($name, $args);
		} catch (\Nette\MemberAccessException $e) {
			if (method_exists($this->entity, $name)) {
				return call_user_func_array(callback($this->entity, $name), $args);
			}

			throw $e;
		}
	}



	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->entity->id;
	}



	/*********************** array access ***********************/



	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
			$this->entity[] = $value;
		} else {
			$this->entity[$offset] = $value;
		}
	}



	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->entity[$offset]);
	}



	/**
	 * @param mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->entity[$offset]);
	}



	/**
	 * @param mixed $offset
	 * @return mixed|null
	 */
	public function offsetGet($offset)
	{
		return isset($this->entity[$offset]) ? $this->entity[$offset] : null;
	}

}
