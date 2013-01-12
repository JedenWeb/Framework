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
use Nette\Caching\Cache;
use Nette\DI;
use Nette\Diagnostics\Debugger;
use Nette\Application\Routers\SimpleRouter;
use Nette\Application\Routers\Route;
use Nette\Config\Compiler;
use Nette\Config\Adapters\NeonAdapter;
use Nette\Utils\Finder;
use Venne\Panels\Stopwatch;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class Configurator extends \Nette\Config\Configurator
{


	const CACHE_NAMESPACE = 'Nette.Configurator';

	/**
	 * @var array
	 */
	protected $modules = array();

	/**
	 * @var Venne\Module\IModule[]
	 */
	protected $moduleInstances = array();

	/**
	 * @var \Nette\DI\Container
	 */
	protected $container;

	/**
	 * @var \Nette\Loaders\RobotLoader
	 */
	protected $robotLoader;

	/**
	 * @var Compiler
	 */
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



	protected function getDefaultModules()
	{
		$adapter = new NeonAdapter();
		$modules = $adapter->load($this->parameters["configDir"] . "/modules.neon");

		return $modules;
	}



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
	 * @return DI\Container
	 */
	public function createContainer()
	{
		// add config files
		foreach ($this->getConfigFiles() as $file) {
			if (is_file($file)) {
				$this->addConfig($file, self::NONE);
			}
		}


		// create container
		Stopwatch::start();
		$container = parent::createContainer();
		Stopwatch::stop("generate container");
		Stopwatch::start();

		// register panels
		\Addons\Panels\Callback::register($container);



		// register robotLoader and configurator
		$container->addService("robotLoader", $this->robotLoader);
		$container->addService("configurator", $this);

		// add default routes
		$container->router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);

		// parameters
		$baseUrl = rtrim($container->httpRequest->getUrl()->getBaseUrl(), '/');
		$container->parameters['baseUrl'] = $baseUrl;
		$container->parameters['basePath'] = preg_replace('#https?://[^/]+#A', '', $baseUrl);


		// setup Application
		$application = $container->application;
		$application->catchExceptions = (bool) !$this->isDebugMode();
		$application->errorPresenter = $container->parameters['website']['errorPresenter'];
		$application->onShutdown[] = function() {
			Stopwatch::stop("shutdown");
		};


		// initialize modules
		foreach ($container->findByTag("module") as $module => $par) {
			$container->{$module}->configure($container);
		}

		// set timer to router
		$container->application->onStartup[] = function() {
			Stopwatch::start();
		};
		$container->application->onRequest[] = function() {
			Stopwatch::stop("routing");
		};
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


		Stopwatch::stop("container configuration");
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
	 * @return \Nette\Config\Compiler
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}

}
