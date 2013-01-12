<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Config\Adapters;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class NeonAdapter extends Nette\Config\Adapters\NeonAdapter
{

	/**
	 * @param array $values
	 * @param string $file
	 */
	public function save($values, $file)
	{
		file_put_contents($file, $this->dump($values));
	}

}
