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
 * Extractor filter interface
 *
 * @author	Patrik Votoček
 */
interface IFilter
{

	/**
	 * @param Dictionary
	 */
	public function process(Dictionary $dictionary);
	
}
