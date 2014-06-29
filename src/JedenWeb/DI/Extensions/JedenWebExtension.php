<?php

namespace JedenWeb\DI\Extensions;

use JedenWeb;
use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class JedenWebExtension extends JedenWeb\DI\CompilerExtension
{

	/** @var array */
	public $defaults = array(
		'macros' => array(
		),
		'helpers' => array(
			'phone' => 'JedenWeb\Templating\Helpers\PhoneHelper::phone',
			'plural' => 'JedenWeb\Templating\Helpers\PluralHelper::plural',
		),
	);


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);
		
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
