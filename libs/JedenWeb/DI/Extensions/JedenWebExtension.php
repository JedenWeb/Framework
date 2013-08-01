<?php

namespace JedenWeb\DI\Extensions;

use JedenWeb;
use Nette;
use Nette\Utils\Validators;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class JedenWebExtension extends JedenWeb\DI\CompilerExtension
{

	/** @var array */
	public $defaults = array(
		'session' => array(
			'storages' => array(
				'database' => 'JedenWeb\Http\DatabaseSessionStorage',
			),
			'storage' => FALSE,
		),
		'macros' => array(
		),
		'helpers' => array(
			'phone' => 'JedenWeb\Templating\Helpers\PhoneHelper::phone',
			'plural' => 'JedenWeb\Templating\Helpers\PluralHelper::plural',
		),
	);



	/**
	 */
	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		# application
		$container->getDefinition('application')
			->addSetup('!headers_sent() && header(?);', 'X-Powered-By: Nette Framework & JedenWeb');
		
		# http
		if ($storage = $config['session']['storage']) {
			Validators::assertField($config['session']['storages'], $storage);
			
			$definition = $container->addDefinition($this->prefix('sessionStorage'))
				->setClass($config['session']['storages'][$storage]);
			
			$container->getDefinition('session')
				->addSetup('setStorage', array($definition));
		}
		
		# security
		$container->getDefinition('user')
				->setClass('JedenWeb\Security\User');

		# helpers
		$loader = $container->addDefinition($this->prefix("helpers"))
			->setClass("JedenWeb\Templating\Helpers");
		
		foreach ($config['helpers'] as $name => $helper) {
			$loader->addSetup('addHelper', array($name, $helper));
		}
	}


	/**
	 * @param Nette\PhpGenerator\ClassType $class
	 */
	public function afterCompile(Nette\PhpGenerator\ClassType $class)
	{
		parent::afterCompile($class);

		$initialize = $class->methods['initialize'];

		$initialize->addBody('$this->parameters["baseUrl"] = rtrim($this->getService("httpRequest")->getUrl()->getBaseUrl(), "/");');
		$initialize->addBody('$this->parameters["basePath"] = preg_replace("#https?://[^/]+#A", "", $this->parameters["baseUrl"]);');
	}

}
