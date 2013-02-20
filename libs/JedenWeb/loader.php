<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb;

use Nette\Diagnostics\Debugger;
use Nette\Application\Routers\SimpleRouter;
use Nette\Application\Routers\Route;
use Nette\Config\Adapters\NeonAdapter;

if (!defined('NETTE')) {
	die('register nette');
	$nettePaths = array(
		__DIR__ . "/../../../../paveljurasek/nette/Nette/loader.php",
		__DIR__ . "/../../../../vendor/paveljurasek/nette/Nette/loader.php",
		__DIR__ . "/../../../../nette/nette/Nette/loader.php",
		__DIR__ . "/../../../../vendor/nette/nette/Nette/loader.php",
	);
	foreach($nettePaths as $path){
		if(file_exists($path)){
			$nettePath = $path;
			break;
		}
	}
	if(!$nettePath){
		die('You must load Nette Framework first');
	}

	include_once $nettePath;
}

define('JEDENWEB', TRUE);
define('JEDENWEB_DIR', __DIR__);
define('JEDENWEB_VERSION', '1.0a');

require_once __DIR__ . '/Config/Configurator.php';