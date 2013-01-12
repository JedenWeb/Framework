<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Config;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Compiler extends Nette\Config\Compiler
{

	/** @var CompilerExtension[] */
	private $extensions = array();

	/** @var Nette\DI\ContainerBuilder */
	private $container;

	/** @var array */
	private $config;

	/** @var array reserved section names */
	private static $reserved = array('services' => 1, 'factories' => 1, 'parameters' => 1);


	public function compile(array $config, $className, $parentName)
	{
//		\Nette\Diagnostics\Debugger::dump($className);
//		\Nette\Diagnostics\Debugger::dump($parentName);die;
//		parent::compile($config, $className, $parentName);

		$this->config = $config;
		$this->container = new \JedenWeb\DI\ContainerBuilder;
		$this->processParameters();
		$this->processExtensions();
		$this->processServices();

		\Nette\Diagnostics\Debugger::dump($this->container);die;

		return $this->generateCode($className, $parentName);
	}

	public function getContainerBuilder()
	{
		return $this->container;
	}

}
