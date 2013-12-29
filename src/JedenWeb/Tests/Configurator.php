<?php

namespace JedenWeb\Tests;

use JedenWeb;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Configurator extends \JedenWeb\Config\Configurator
{

	/**
	 * @var Configurator
	 */
	private static $configurator;




	public function __construct(array $params = array())
	{
		$this->parameters = $this->getDefaultParameters($params);
		$this->setTempDirectory($this->parameters["tempDir"]);

		$this->setEnvironment('test');
		$this->setDebugMode(TRUE);

		self::$configurator = $this;
	}



	/**
	 * @return \SystemContainer|\Nette\DI\Container
	 */
	public static function getTestsContainer()
	{
		return static::$configurator->getContainer();
	}



	/**
	 * @param string $testsDir
	 *
	 * @return Configurator
	 */
	public static function testsInit($testsDir)
	{
		if (!is_dir($testsDir)) {
			throw new JedenWeb\IOException("Given path is not a directory.");
		}

		// arguments
		$params = array(
			'wwwDir' => $testsDir,
			'appDir' => $testsDir,
			'logDir' => $testsDir . '/log',
			'tempDir' => $testsDir . '/temp',
		);

		// cleanup directories
//		Filesystem::cleanDir($params['tempDir'] . '/cache');
//		Filesystem::rm($params['tempDir'] . '/btfj.dat', FALSE);

		// create container
		return new static($params);
	}

}
