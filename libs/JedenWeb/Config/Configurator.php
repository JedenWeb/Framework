<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Config;

use JedenWeb;
use Nette;
use Nette\Application\Routers\Route;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Configurator extends \Nette\Config\Configurator
{

	const CACHE_NAMESPACE = 'Nette.Configurator';

	/** @var array */
	protected $modules = array();

	/** @var \JedenWeb\Module\IModule[] */
	protected $moduleInstances = array();

	/** @var \Nette\DI\Container */
	protected $container;

	/** @var \Nette\Loaders\RobotLoader */
	protected $robotLoader;

	/** @var Compiler */
	protected $compiler;



	/**
	 * @param array $params
	 */
	public function __construct($params = array())
	{
		$this->parameters = $this->getDefaultParameters($params);
		$this->modules = $this->getDefaultModules();
		$this->setTempDirectory($this->parameters["tempDir"]);
	}



	/**
	 * @return array
	 */
	protected function getDefaultModules()
	{
		$adapter = new Adapters\NeonAdapter();
		return $adapter->load($this->parameters["configDir"] . "/modules.neon");
	}



	/**
	 * @return array
	 */
	protected function getModuleInstances()
	{
		if (!$this->moduleInstances) {
			foreach ($this->modules as $module) {
				$class = "\\" . ucfirst($module) . "Module\\Module";
				$this->moduleInstances[] = new $class;
			}
		}
		return $this->moduleInstances;
	}



	/**
	 * @param array $parameters
	 * @return array
	 */
	protected function getDefaultParameters($parameters = array())
	{
		$defaults = parent::getDefaultParameters();

		$defaults['rootDir'] = realpath($defaults['wwwDir'] . '/..');
		$defaults['appDir'] = realpath($defaults['rootDir'] . '/app');
		$defaults['libsDir'] = realpath($defaults['rootDir'] . '/vendor');
		$defaults['tempDir'] = realpath($defaults['rootDir'] . '/temp');
		$defaults['logDir'] = realpath($defaults['rootDir'] . '/log');
		$defaults['configDir'] = realpath($defaults['appDir'] . '/config');

		return array_merge($defaults, $parameters);
	}



	/**
	 * @param string $name
	 */
	public function setEnvironment($name)
	{
		$this->parameters["environment"] = $name;
	}



	/**
	 * @return \Nette\DI\Container
	 */
	public function getContainer()
	{
		if (!$this->container) {
			$this->container = $this->createContainer();
		}

		return $this->container;
	}



	/**
	 * Loads configuration from file and process it.
	 *
	 * @return \Nette\DI\Container
	 */
	public function createContainer()
	{
		foreach ($this->getConfigFiles() as $file) {
			if (is_file($file)) {
				$this->addConfig($file, self::NONE);
			}
		}


		// create container
		$container = parent::createContainer();


		// register robotLoader and configurator
		if ($this->robotLoader) {
			$container->addService("robotLoader", $this->robotLoader);
		}
		$container->addService("configurator", $this);

		// add default routes
		$container->router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);


		// setup Application
		$application = $container->application;
		$application->catchExceptions = (bool) !$this->isDebugMode();
		$application->errorPresenter = $container->parameters['website']['errorPresenter'];


		// initialize modules
		foreach ($container->findByTag("module") as $module => $par) {
			$container->{$module}->configure($container);
		}


//		$container->application->onRequest[] = function($application, $request) {
//			$presenter = $request->presenterName;
//			$errorPresenter = 'Error';
//
//			if(($pos = strrpos($presenter, ':')) !== false) {
//				try {
//					$errorPresenter = substr($presenter, 0, ($pos + 1)) . 'Error';
//					$errorPresenterClass = $application->presenterFactory->createPresenter($errorPresenter);
//				}
//				catch (Nette\Application\InvalidPresenterException $e) {
//					$errorPresenter = 'Error';
//				}
//			}
//
//			$application->errorPresenter = $errorPresenter;
//		};

		return $container;
	}



	/**
	 * @return Compiler
	 */
	protected function createCompiler()
	{
		$this->compiler = parent::createCompiler();
		$this->compiler
				->addExtension('jedenWeb', new Extensions\JedenWebExtension);

		foreach ($this->getModuleInstances() as $instance) {
			$instance->compile($this->compiler);
		}

		return $this->compiler;
	}



	/**
	 * @return array
	 */
	protected function getConfigFiles()
	{
		$configs = array(
			$this->parameters['configDir'] . "/config.neon",
			$this->parameters['configDir'] . "/config.local.neon",
		);

		foreach ($this->getModuleInstances() as $instance) {
			$path = $instance->getPath() . "/public/config/config.neon";
			if (is_file($path)) {
				$configs[] = $path;
			}
		}

		return $configs;
	}



	/**
	 * Enable robotLoader.
	 */
	public function enableRobotLoader()
	{
		$this->robotLoader = $this->createRobotLoader();
		$this->robotLoader
				->addDirectory($this->parameters["libsDir"])
				->addDirectory($this->parameters["appDir"])
				->register();
	}



	/**
	 * @param string $logDirectory
	 * @param string $email
	 */
	public function enableDebugger($logDirectory = NULL, $email = NULL)
	{
		Nette\Diagnostics\Debugger::$strictMode = TRUE;
		Nette\Diagnostics\Debugger::enable(
			!$this->parameters['debugMode'],
			$logDirectory ?: $this->parameters["logDir"],
			$email
		);
	}



	/**
	 * @return Compiler
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}

}
