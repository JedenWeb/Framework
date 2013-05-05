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
use Nette\Config\Configurator;
use Nette\Utils\Finder;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
abstract class Module extends \Nette\Object implements IModule
{

	/** @var string */
	protected $name;

	/** @var string */
	protected $version = "1.0";

	/** @var string */
	protected $description = "";

	/** @var array */
	protected $dependencies = array();



	/** 
	 * @return string 
	 */
	public function getName()
	{
		if ($this->name !== NULL) {
			return $this->name;
		}

		return lcfirst(substr($this->getReflection()->getNamespaceName(), 0, -6));
	}


	/** 
	 * @return string 
	 */
	public function getVersion()
	{
		return $this->version;
	}


	/** 
	 * @return string 
	 */
	public function getDescription()
	{
		return $this->description;
	}


	/** 
	 * @return array 
	 */
	public function getDependencies()
	{
		return $this->dependencies;
	}


	/** 
	 * @return array 
	 */
	public function getAdminModules()
	{
		if (is_dir($dir = $this->getPath().'/AdminModule')) {
			$modules = array();
			foreach (Finder::findDirectories('*Module')->from($dir) as $module) {
				$modules[] = substr($module->getFilename(), 0, -6);
			}

			return $modules;
		}

		return array();
	}


	/** 
	 * @return array 
	 */
	public function getAdminPresenters()
	{
		if ($this->getName() !== 'core' && is_dir($dir = $this->getPath().'/AdminModule')) {
			$presenters = array();
			foreach (Finder::findFiles('*Presenter.php')->from($dir) as $presenter) {
				$presenters[] = substr($presenter->getFilename(), 0, -13);
			}

			return $presenters;
		}

		return array();
	}


	/** 
	 * @return string 
	 */
	public function getPath()
	{
		return dirname($this->getReflection()->getFileName());
	}


	/** 
	 * @return string 
	 */
	public function getNamespace()
	{
		return $this->getReflection()->getNamespaceName();
	}


	/** 
	 * @param Nette\Config\Compiler $compiler 
	 */
	public function compile(Nette\Config\Compiler $compiler)
	{
		$compiler->addExtension($this->getName(), new CompilerExtension($this->getPath(), $this->getNamespace()));
	}


	/** 
	 * @param \Nette\DI\Container $container 
	 */
	public function configure(Container $container)
	{
	}

}
