<?php

/**
 * Test: JedenWeb\Utils\Tax
 *
 * @author     Pavel Jurasek
 * @package    JedenWeb\Utils
 */

use JedenWeb\Utils\Tax;


require __DIR__ . '/../bootstrap.php';


\Tester\Assert::equal(21, (int) round(Tax::rate(1208.79, 999)));
\Tester\Assert::equal(15, (int) round(Tax::rate(1148.85, 999)));
