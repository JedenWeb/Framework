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
 * Localization storage interface
 *
 * @author	Patrik Votoček
 */
interface IStorage
{

	/**
	 * @param Dictionary
	 * @param string
	 */
	public function save(Dictionary $dictionary, $lang);



	/**
	 * @param string
	 * @return Dictionary
	 */
	public function load($lang, Dictionary $dictionary);

}
