<?php

namespace JedenWeb\Module;

use Nette;
use Nette\DI;
use Nette\Security\Permission;
use Nette\Application\Routers\RouteList;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
interface IModule
{

	public function getName();

	public function getMapping();
	
	public function compile(DI\Compiler $compiler);

	public function configure(DI\Container $container);

}
