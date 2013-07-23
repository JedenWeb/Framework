<?php

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
