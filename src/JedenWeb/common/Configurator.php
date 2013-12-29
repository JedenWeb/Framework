<?php

namespace JedenWeb;

use JedenWeb;
use Nette;
use Nette\Application\Routers\Route;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Configurator extends Nette\Configurator
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
		$adapter = new DI\Config\Adapters\NeonAdapter;
		
		if (file_exists($this->parameters["configDir"] . "/modules.neon")) {
			return $adapter->load($this->parameters["configDir"] . "/modules.neon");
		}
		return array();
	}



	/**
	 * @return array
	 */
	protected function getModuleInstances()
	{
		if (!$this->moduleInstances) {
			foreach ($this->modules as $module) {
//				$class = "\\" . ucfirst($module) . "Module\\Module";
				$class = "\\JedenWeb\\" . ucfirst($module) . "\\Module";
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
		$container->getService('router')->offsetSet(NULL, new Route('index.php', 'Homepage:default', Route::ONE_WAY));


		// initialize modules
		foreach ($container->findByTag("module") as $module => $par) {
			$container->{$module}->configure($container);
		}

		return $container;
	}



	/**
	 * @return Compiler
	 */
	protected function createCompiler()
	{
		$this->compiler = parent::createCompiler();
		$this->compiler
				->addExtension('jedenWeb', new DI\Extensions\JedenWebExtension);

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
		$logDirectory = $logDirectory ?: $this->parameters["logDir"];
		
		Nette\Diagnostics\Debugger::$showLocation = TRUE;
		parent::enableDebugger($logDirectory, $email);
	}



	/**
	 * @return Compiler
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}

}
