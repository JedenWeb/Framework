<?php

namespace JedenWeb;

use Nette\Diagnostics\Debugger;
use Nette\Application\Routers\SimpleRouter;
use Nette\Application\Routers\Route;
use Nette\Config\Adapters\NeonAdapter;

if (!defined('NETTE')) {
	$nettePaths = array(
		__DIR__ . "/../../../../nette/nette/Nette/loader.php",
		__DIR__ . "/../../../../vendor/nette/nette/Nette/loader.php",
	);
	
	foreach ($nettePaths as $path) {
		if (file_exists($path)) {
			$nettePath = $path;
			break;
		}
	}
	
	if (!$nettePath) {
		die('You must load Nette Framework first');
	}

	include_once $nettePath;
}

define('JEDENWEB', TRUE);
define('JEDENWEB_DIR', __DIR__);
define('JEDENWEB_VERSION', '1.1');

require_once __DIR__ . '/common/Configurator.php';