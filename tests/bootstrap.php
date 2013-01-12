<?php

define('TEST_DIR', __DIR__);
define('SRC_DIR', __DIR__ . '/../src');
define('VENDOR_DIR', __DIR__ . '/../src/vendor');

$loader = require_once VENDOR_DIR . '/autoload.php';
$robot = new \Nette\Loaders\RobotLoader;
$robot->addDirectory(VENDOR_DIR . '/JedenWeb');
$robot->setCacheStorage(new \Nette\Caching\Storages\DevNullStorage);
$robot->register();

\Nette\Diagnostics\Debugger::enable(\Nette\Diagnostics\Debugger::DEVELOPMENT, TEST_DIR . '/log');