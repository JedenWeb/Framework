<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Module;

use Nette;
use Nette\DI\Container;
use Nette\Security\Permission;
use Nette\Application\Routers\RouteList;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
interface IModule
{

	public function getName();

	public function getVersion();

	public function getDescription();

	public function getDependencies();

	public function getPath();

	public function getNamespace();

	public function compile(Nette\Config\Compiler $compiler);

	public function configure(Container $container);

//	public function install(Container $container);

//	public function uninstall(Container $container);

}
