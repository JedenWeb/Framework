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
interface ITemplateConfigurator
{

	/**
	 * @param \Nette\Templating\Template $template
	 */
	public function configure(Nette\Templating\Template $template);


	/**
	 * @param \Nette\Templating\Template $template
	 */
	public function prepareFilters(\Nette\Latte\Engine $latte);

}
