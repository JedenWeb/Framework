<?php

define('TEST_DIR', __DIR__);
define('SRC_DIR', __DIR__ . '/../libs');
define('VENDOR_DIR', __DIR__ . '/../vendor');

/**
 * @var \Composer\Autoload\ClassLoader
 */
$loader = require_once VENDOR_DIR . '/autoload.php';
$loader->add('JedenWeb', SRC_DIR);

\Nette\Diagnostics\Debugger::enable(\Nette\Diagnostics\Debugger::DEVELOPMENT, TEST_DIR . '/log');

unset($loader); // cleanup
