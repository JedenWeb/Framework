<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Config\Extensions;

use JedenWeb\Config\CompilerExtension;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class JedenWebExtension extends CompilerExtension
{

	public $defaults = array(
		'stopwatch' => array(
			'debugger' => TRUE,
		),
		'session' => array(
//			'savePath' => '%tempDir%/session'
		)
	);



	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		# application

		# session
		if (array_key_exists('savePath', $config['session'])) {
			$container->getDefinition('session')
				->addSetup("setSavePath", $config['session']['savePath']);
		}

		# translator
		$container->addDefinition("translator")
			->setClass("JedenWeb\Localization\Translator")
			->addSetup("setLang", "cs");

		$container->addDefinition("translatorPanel")
			->setClass("JedenWeb\Localization\Panel");

		# http
		$container->getDefinition('httpResponse')
			->addSetup('setHeader', array('X-Powered-By', 'Nette Framework && JedenWeb'));

		# template
//		$container->getDefinition('nette.latte')
//			->setClass('JedenWeb\Latte\Engine');

		$container->addDefinition($this->prefix("templateConfigurator"))
			->setClass("JedenWeb\Templating\TemplateConfigurator");

		# macros
		$this->addMacro('macros.ui', 'JedenWeb\Latte\Macros\UIMacros::install')
				->addSetup('setModules', array('%modules%'));

		# helpers
		$container->addDefinition($this->prefix("helpers"))
			->setClass("JedenWeb\Templating\Helpers");

//		$container->addDefinition("authorizatorFactory")
//			->setFactory("CoreModule\AuthorizatorFactory")
//			->setAutowired(false);

//		$container->addDefinition("authorizator")
//			->setClass("Nette\Security\Permission")
//			->setFactory("@authorizatorFactory::getCurrentPermissions");


		# mappers
		$container->addDefinition("configFormMapper")
			->setClass("Venne\Forms\Mapping\ConfigFormMapper", array($container->parameters["appDir"] . "/config/config.neon"));

		# managers
		$container->addDefinition("configManager")
			->setClass("Venne\Config\ConfigBuilder", array("%configDir%/config.neon"))
			->addTag("manager");

		$container->addDefinition($this->prefix('assetManager'))
			->setClass("JedenWeb\Managers\AssetManager")
			->addTag("manager");

		# modules
		foreach ((array) @$container->parameters["modules"] as $module => $item) {
			$container->addDefinition($module . "Module")
				->addTag("module")
				->setClass(ucfirst($module) . "Module\\Module");
		}
	}



	public function beforeCompile()
	{
		$this->registerMacroFactories();
		$this->registerHelperFactories();
	}



	public function afterCompile(\Nette\PhpGenerator\ClassType $class)
	{
		parent::afterCompile($class);

		$initialize = $class->methods['initialize'];

		$initialize->addBody('$this->parameters[\'baseUrl\'] = rtrim($this->getService("httpRequest")->getUrl()->getBaseUrl(), "/");');
		$initialize->addBody('$this->parameters[\'basePath\'] = preg_replace("#https?://[^/]+#A", "", $this->parameters["baseUrl"]);');
	}


	protected function registerMacroFactories()
	{
		$container = $this->getContainerBuilder();
		$templateConfigurator = $container->getDefinition($this->prefix('templateConfigurator'));

		foreach ($this->findByTag('macro') as $factory => $meta) {
			$definition = $container->getDefinition($factory);
			$templateConfigurator->addSetup('addFactory', array(substr($factory, 0, -7)));
		}
	}


	protected function registerHelperFactories()
	{
		$container = $this->getContainerBuilder();
		$config = $container->getDefinition($this->prefix('helpers'));

		foreach ($container->findByTag('helper') as $factory => $meta) {
			$config->addSetup('addHelper', array($meta, "@{$factory}"));
		}
	}



	/**
	 * @param \Nette\DI\ContainerBuilder $container
	 * @param $tag
	 * @return array
	 */
	protected function getSortedServices($tag)
	{
		$container = $this->getContainerBuilder();

		$items = array();
		$ret = array();
		foreach ($container->findByTag($tag) as $route => $meta) {
			$priority = isset($meta['priority']) ? $meta['priority'] : (int)$meta;
			$items[$priority][] = $route;
		}

		krsort($items);

		foreach ($items as $items2) {
			foreach ($items2 as $item) {
				$ret[] = $item;
			}
		}
		return $ret;
	}



	public function findByTag($tag)
	{
		$container = $this->getContainerBuilder();

		$found = array();
		foreach ($container->getDefinitions() as $name => $def) {
			if (isset($def->tags[$tag])) {
				$found[$name] = $def->tags[$tag];
			}
		}
		return $found;
	}

}
