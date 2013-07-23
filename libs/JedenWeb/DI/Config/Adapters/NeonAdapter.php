<?php

namespace JedenWeb\DI\Config\Adapters;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class NeonAdapter extends Nette\DI\Config\Adapters\NeonAdapter
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
