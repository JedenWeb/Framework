<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Templating;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class BaseHelper extends Nette\Object implements IHelper
{

	/**
	 * @var LatteHelper
	 */
	protected static $instance;



	public function __construct()
	{
		self::$instance = $this;
	}



	/**
	 * @return string
	 */
	public static function filter()
	{
		return call_user_func_array(array(self::$instance, "run"), func_get_args());
	}

}
