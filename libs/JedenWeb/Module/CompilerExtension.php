<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Module;

use Venne;
use Nette\DI\ContainerBuilder;
use Nette\Utils\Finder;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class CompilerExtension extends \JedenWeb\Config\CompilerExtension
{

	/**
	 * @var array
	 */
	protected $classes = array(
		self::CONTROL => array(),
		self::SUBSCRIBER => array(),
		self::SERVICE => array(),
		self::MACRO => array(),
		self::HELPER => array(),
		self::FORM => array(),
//		self::MANAGER => array(),
		self::REPOSITORY => array(),
		self::WIDGET => array(),
		self::ROUTE => array()
	);

	/**
	 * @var array
	 */
	protected $config = array();



	/**
	 * @param string $modulePath
	 * @param string $moduleNamespace
	 */
	public function __construct($modulePath, $moduleNamespace)
	{
		$classes = $this->classes;

		foreach (Finder::findFiles("*.php")->from($modulePath)->exclude(".git", "Resources") as $file) {
			$relative = $file->getRealpath();
			$relative = strtr($relative, array($modulePath => '', '/' => '\\'));
			$class = $moduleNamespace . '\\' . ltrim(substr($relative, 0, -4), '\\');
			$class = str_replace("presenters\\", "", $class);

			try {
				$refl = \Nette\Reflection\ClassType::from($class);

				foreach (array_keys($classes) as $item) {
					if ($item == self::REPOSITORY) {
						continue;
					}

					if (\Nette\Utils\Strings::endsWith($class, ucfirst($item))) {
						$classes[$item][$class] = lcfirst(substr($class, strrpos($class, "\\") + 1));
					}
				}

				if ($refl->isSubclassOf("\\Venne\\Doctrine\\ORM\\IEntity") && $refl->hasAnnotation("Entity")) {
					$anot = $refl->getAnnotation("Entity");
					$classes[self::REPOSITORY][$class] = substr($anot["repositoryClass"], 0, 1) == "\\" ? substr($anot["repositoryClass"], 1) : $anot["repositoryClass"];
				}
			} catch (\ReflectionException $ex) {

			}
		}

		foreach (Finder::findFiles("*.neon")->from($modulePath) as $file) {
			$this->config[] = $file->getRealpath();
		}

		$this->classes = $classes;
	}



	public function loadConfiguration()
	{
		parent::loadConfiguration();

		$container = $this->getContainerBuilder();
		$config = $this->getConfig();


		/* services */
		foreach ($this->classes[self::SERVICE] as $class => $name) {
			$this->compileService($name, $class);
		}


		/* managers */
//		foreach ($this->classes[self::MANAGER] as $class => $name) {
//			$this->compileManager($name, $class);
//		}


		/* macros */
//		foreach ($this->classes[self::MACRO] as $class => $name) {
//			$this->compileMacro($name, $class);
//		}


		/* helpers */
		foreach ($this->classes[self::HELPER] as $class => $name) {
			$this->addHelper($name, $class);
		}


//		\Nette\Diagnostics\Debugger::dump($this->classes[self::FORM]);die;
		/* forms */
		foreach ($this->classes[self::FORM] as $class => $name) {
			$this->compileForm($name, $class);
		}


		/* routes */
		foreach ($this->classes[self::ROUTE] as $class => $name) {
			$this->compileRoute($name, $class);
		}
	}

}
