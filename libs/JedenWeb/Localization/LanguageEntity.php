<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Localization;

/**
 * Language entity
 *
 * @entity
 * @table(name="langs")
 *
 * @author	Patrik Votoček
 *
 * @property string $name
 * @property string $nativeName
 * @property string $short
 */
class LanguageEntity extends \Venne\Doctrine\ORM\BaseEntity
{

	/**
	 * @column
	 * @var string
	 */
	private $name;

	/**
	 * @column
	 * @var string
	 */
	private $nativeName;

	/**
	 * @column(length=5)
	 * @var string
	 */
	private $short;



	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}



	/**
	 * @param string
	 * @return LanguageEntity
	 */
	public function setName($name)
	{
		$name = trim($name);
		$this->name = $name === "" ? NULL : $name;
		return $this;
	}



	/**
	 * @return string
	 */
	public function getNativeName()
	{
		return $this->nativeName;
	}



	/**
	 * @param string
	 * @return LanguageEntity
	 */
	public function setNativeName($nativeName)
	{
		$nativeName = trim($nativeName);
		$this->nativeName = $nativeName === "" ? NULL : $nativeName;
		return $this;
	}



	/**
	 * @return string
	 */
	public function getShort()
	{
		return $this->short;
	}



	/**
	 * @param string
	 * @return LanguageEntity
	 */
	public function setShort($short)
	{
		$short = trim($short);
		$this->short = $short === "" ? NULL : $short;
		return $this;
	}
	
}
