<?php

/**
 * Test: JedenWeb\Managers\Image\ImageManager
 *
 * @author     Pavel Jurásek
 * @package    JedenWeb\Managers\Image
 */

include __DIR__ . '/../../../bootstrap.php';

$_SERVER = array(
	'REQUEST_URI' => '/framework/src/www/',
	'SCRIPT_FILENAME' => '/Users/admin/Sites/framework/tests/JedenWeb/Managers/Image/www/index.php',
	'SCRIPT_NAME' => '/framerowk/src/www/index.php',
	'REMOTE_ADDR' => '::1',
	'HTTP_HOST' => 'localhost',
	'REQUEST_METHOD' => 'GET'
);

$factory = new Nette\Http\RequestFactory;
$request = $factory->createHttpRequest();

$manager = new \JedenWeb\Managers\Image\ImageManager(
	'/Users/admin/Sites/framework/tests/JedenWeb/Managers/Image/www',
	$request,
	'media'
);

$manager->setAssetsDir('assets');


Assert::same(
	'/Users/admin/Sites/framework/tests/JedenWeb/Managers/Image/www/assets',
	$manager->getAssetsDir()
);


Assert::exception(
	function() use ($manager) {
		$manager->setAssetsDir('void');
	},
	'Nette\IOException',
	"Directory '/Users/admin/Sites/framework/tests/JedenWeb/Managers/Image/www/void' is not writable."
);
