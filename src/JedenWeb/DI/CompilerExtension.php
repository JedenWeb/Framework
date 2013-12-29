<?php

namespace JedenWeb\DI;

use Nette;
use Nette\DI\ContainerBuilder;
use Nette\DI\Container;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class CompilerExtension extends Nette\DI\CompilerExtension
{

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

}
