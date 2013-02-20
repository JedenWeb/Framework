<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Managers\Image;

use JedenWeb;
use Nette;

/**
 * @author Filip Procházka <filip@prochazka.su>
 *
 * @property-read string $file
 * @property \Size $size
 * @property-read \Size $size
 */
class Image extends Nette\Object
{

	/**
	 * @var string
	 */
	private $file;

	/**
	 * @var \Size
	 */
	private $size;



	/**
	 * @param string $file
	 */
	public function __construct($file)
	{
		$this->file = $file;
		$this->size = Size::fromFile($file);
	}



	/**
	 * @return bool
	 */
	public function exists()
	{
		return file_exists($this->file);
	}



	/**
	 * @return float|int
	 */
	public function getFile()
	{
		return $this->file;
	}



	/**
	 * @return \Size
	 */
	public function getSize()
	{
		return $this->size;
	}

}
