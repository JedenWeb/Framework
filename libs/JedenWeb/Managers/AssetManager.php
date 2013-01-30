<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Managers;

use JedenWeb;
use Nette;
use Nette\Caching\Cache;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class AssetManager extends Nette\Object
{

	const PARAM_MEDIA = "media";

	const PARAM_ALIAS = "alias";

	const PARAM_PARNET = "parent";

	const PARAM_TYPE = "type";

	const TYPE_JS = "js";

	const TYPE_CSS = "css";

	/** @var array */
	protected $validParams = array("media", "alias", "parent", "type");

	/** @var string */
	protected $basePath;

	/** @var array */
	protected $css = array();

	/** @var array */
	protected $js = array();



	/**
	 * Constructor
	 *
	 * @param \Nette\DI\Container $container
	 */
	public function __construct(\Nette\DI\Container $container)
	{
		$this->basePath = $container->parameters['basePath'];
	}



	/* ---------------------- Add assets ----------------------- */


	/**
	 * Add external javascript file.
	 *
	 * @param type $path
	 * @param array $params
	 */
	public function addJavascript($path, array $params = array())
	{
		$this->addFile($path, $params + array(self::PARAM_TYPE => self::TYPE_JS));
	}



	/**
	 * Add external stylesheet file.
	 *
	 * @param type $path
	 * @param array $params
	 */
	public function addStylesheet($path, array $params = array())
	{
		$this->addFile($path, $params + array(self::PARAM_TYPE => self::TYPE_CSS));
	}



	/**
	 * Add external file.
	 *
	 * @param type $path
	 * @param array $params
	 */
	protected function addFile($path, array $params = array())
	{
		$path = trim($path, "/");

		if (!$this->areParamsValid($params)) {
			throw new \Nette\InvalidArgumentException;
		}

		$absolutePath = $this->getUrl($path);
		$this->{$params[self::PARAM_TYPE]}[$absolutePath] = $params;
	}



	/**
	 * Check params.
	 *
	 * @param array $params
	 * @return boolean
	 */
	protected function areParamsValid($params)
	{
		foreach ($params as $key => $item) {
			if (array_search($key, $this->validParams) === false) {
				return false;
			}
		}
		return true;
	}



	/**
	 * Get all javascript files.
	 *
	 * @return array
	 */
	public function getJavascripts()
	{
		return $this->js;
	}



	/**
	 * Get all stylesheet files.
	 *
	 * @return array
	 */
	public function getStylesheets()
	{
		return $this->css;
	}



	/**
	 * Absolute path for file.
	 *
	 * @param string $path
	 * @return string
	 */
	protected function getUrl($path)
	{
		return $this->basePath . "/" . JedenWeb\Module\Helpers::expandResource($path);
	}

}

