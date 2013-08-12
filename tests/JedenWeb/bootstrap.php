<?php

/**
 * Test initialization and helpers.
 *
 * @author     David Grudl
 * @package    Nette\Test
 */

if (@!$loader = include __DIR__ . '/../../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}

$loader->add('JedenWeb', __DIR__ . '/../../libs');
require_once __DIR__ . '/../../libs/JedenWeb/common/Configurator.php';


// configure environment
Tester\Helpers::setup();
class_alias('Tester\Assert', 'Assert');
date_default_timezone_set('Europe/Prague');


// create temporary directory
define('TEMP_DIR', __DIR__ . '/../temp/' . getmypid());
@mkdir(dirname(TEMP_DIR)); // @ - directory may already exist
Tester\Helpers::purge(TEMP_DIR);

define('LOG_DIR', __DIR__ . '/../log/');
@mkdir(dirname(LOG_DIR)); // @ - directory may already exist
Nette\Diagnostics\Debugger::$logDirectory = LOG_DIR;


$_SERVER = array_intersect_key($_SERVER, array_flip(array('PHP_SELF', 'SCRIPT_NAME', 'SERVER_ADDR', 'SERVER_SOFTWARE', 'HTTP_HOST', 'DOCUMENT_ROOT', 'OS', 'argc', 'argv')));
$_SERVER['REQUEST_TIME'] = 1234567890;
$_ENV = $_GET = $_POST = array();


if (extension_loaded('xdebug')) {
	xdebug_disable();
	Tester\CodeCoverage\Collector::start(__DIR__ . '/coverage.dat');
}


function id($val) {
	return $val;
}


if (!class_exists('Notes')) {
	class Notes
	{
		static public $notes = array();

		public static function add($message)
		{
			self::$notes[] = $message;
		}

		public static function fetch()
		{
			$res = self::$notes;
			self::$notes = array();
			return $res;
		}

	}
}
