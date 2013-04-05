<?php

/**
 * Test: JedenWeb\Module\Helpers
 *
 * @author     Pavel Jurasek
 * @package    JedenWeb\Module
 */

use JedenWeb\Module\Helpers;


require __DIR__ . '/../bootstrap.php';



Assert::same('resources/core/js/jquery/jquery.js', Helpers::expandResource('@CoreModule/js/jquery/jquery.js'));
Assert::same('resources/core/admin/css/bootstrap.min.css', Helpers::expandResource('@CoreModule/admin/css/bootstrap.min.css'));
