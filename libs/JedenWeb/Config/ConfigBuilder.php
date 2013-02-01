<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Config;

use JedenWeb;
use JedenWeb\Config\Adapters\NeonAdapter;
use Nette;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class ConfigBuilder extends \Nette\Object implements \ArrayAccess, \Countable, \IteratorAggregate
{

	/** @var array */
	protected $data;

	/** @var array */
	protected $dataOrig;

	/** @var array */
	protected $sections;

	/** @var string */
	protected $fileName;

	/** @var NeonAdapter */
	protected $adapter;



	/**
	 * @param string $fileName
	 */
	public function __construct($fileName)
	{
		$this->fileName = $fileName;
		$this->adapter = new NeonAdapter;
		$this->load();
	}



	/**
	 * Load data
	 */
	public function load()
	{
		$this->data = \Nette\ArrayHash::from($this->adapter->load($this->fileName), true);
	}



	/**
	 * Save data
	 */
	public function save()
	{
		$this->adapter->save((array)$this->data, $this->fileName);
		$this->load();
	}



	/* ------------------------------ Interfaces -------------------------------- */


	/**
	 * Returns items count.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->count($this->data);
	}



	/**
	 * Returns an iterator over all items.
	 *
	 * @return \RecursiveArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->data);
	}



	/**
	 * Determines whether a item exists.
	 *
	 * @param  mixed
	 * @return bool
	 */
	public function offsetExists($index)
	{
		return $index >= 0 && $index < count($this->data);
	}



	/**
	 * Returns a item.
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($index)
	{
		if ($index < 0 || $index >= count($this->data)) {
			throw new OutOfRangeException("Offset invalid or out of range");
		}
		return $this->data[$index];
	}



	/**
	 * Replaces or appends a item.
	 *
	 * @param mixed
	 * @param mixed
	 * @return void
	 */
	public function offsetSet($index, $value)
	{
		if ($index === NULL) {
			$this->data[] = is_array($value) ? \Nette\ArrayHash::from($value, true) : $value;
		} else {
			$this->data[$index] = is_array($value) ? \Nette\ArrayHash::from($value, true) : $value;
		}
	}



	/**
	 * Removes the element from this list.
	 *
	 * @param mixed
	 * @return void
	 */
	public function offsetUnset($index)
	{
		if ($index < 0 || $index >= count($this->data)) {
			throw new Nette\OutOfRangeException("Offset invalid or out of range");
		}
		array_splice($this->data, $index, 1);
	}

}
