<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\DI;

use Nette;
use Nette\DI\ContainerBuilder;
use Nette\DI\Container;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class CompilerExtension extends Nette\DI\CompilerExtension
{

	const CONTROL = "control";

	const SUBSCRIBER = "subscriber";

	const SERVICE = "service";

	const MACRO = "macro";

	const HELPER = "helper";

	const FORM = "form";

	const MANAGER = "manager";

	const REPOSITORY = "repository";

	const WIDGET = "widget";

	const ROUTE = "route";


	/**
	 * @var type
	 */
	public static $types = array(
		self::CONTROL => "Control",
		self::SUBSCRIBER => "Subscriber",
		self::SERVICE => "Service",
		self::MACRO => "Macro",
		self::HELPER => "Helper",
		self::FORM => "Form",
		self::MANAGER => "Manager",
		self::REPOSITORY => "Repository",
		self::WIDGET => "Widget",
		self::ROUTE => "Route",
	);



	/**
	 * @param string $tag
	 * @return array
	 */
	protected function getSortedServices($tag)
	{
		$container = $this->getContainerBuilder();

		$items = array();
		foreach ($container->findByTag($tag) as $def => $meta) {
			$priority = isset($meta['priority']) ? $meta['priority'] : (int) $meta;
			$items[$priority][] = $def;
		}

		krsort($items);

		return \Nette\Utils\Arrays::flatten($items);
	}



	/**
	 * @param string $name
	 * @param string $installer
	 * @return \Nette\DI\ServiceDefinition
	 */
	public function addMacro($name, $installer)
	{
		$container = $this->getContainerBuilder();
		
		$macro = $container->addDefinition($name = $this->prefix($name))
			->setClass(substr($installer, 0, strpos($installer, '::')))
			->setFactory($installer, array('@nette.latte'))
			->addTag('latte.macro');
		
		$container->getDefinition('nette.latte')
			->addSetup($installer . '(?->compiler)', array('@self'));
		
		return $macro;
	}



	/**
	 * @param string $class
	 * @param string $name
	 */
	protected function addHelper($name, $installer)
	{
		$container = $this->getContainerBuilder();

		$helper = $container->addDefinition($name = $this->prefix($name))
			->setClass(substr($installer, 0, strpos($installer, '::')))
			->setFactory($installer)
			->addTag('helper');

		return $helper;
	}



	/**
	 * @param string $name
	 * @param string $class
	 */
	protected function compileManager($name, $class)
	{
		$this->getContainerBuilder()
			->addDefinition($this->prefix($name))
			->setClass($class)
			->addTag(self::MANAGER);
	}



	/**
	 * @param string $name
	 * @param string $class
	 */
	protected function compileService($name, $class)
	{
		$this->getContainerBuilder()
			->addDefinition($this->prefix($name))
			->setClass($class)
			->addTag(self::SERVICE);
	}



	/**
	 * @param string $name
	 * @param string $class
	 */
	protected function compileForm($name, $class)
	{
		$this->getContainerBuilder()
			->addDefinition($this->prefix($name))
			->setClass($class)
			->addTag(self::FORM);
	}



	/**
	 * @param string $name
	 * @param string $class
	 * @param int $priority
	 */
	protected function compileRoute($name, $class, $priority = NULL)
	{
		$route = $this->getContainerBuilder()
			->addDefinition($name)
			->setClass($class)
			->setAutowired(FALSE)
			->addTag(self::ROUTE);

		if ($priority) {
			$route->addTag(array("priority" => $priority));
		}
	}



	/**
	 * @param string $class
	 * @param string $name
	 * @return string
	 */
	private function _prefix($class, $name)
	{
		if (preg_match_all('/\w+Module/', $class, $m)) {
			array_walk($m[0], function(&$v, $k) {
				$v = strtolower(substr($v, 0, -6));
			});

			return implode('.', $m[0]) . ".$name";
		}

		return parent::prefix($name);
	}

}
