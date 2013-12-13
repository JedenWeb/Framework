<?php

/**
 * Test: JedenWeb\Utils\Tax
 *
 * @author     Pavel Jurasek
 * @package    JedenWeb\Utils
 */

use JedenWeb\Utils\Tax;


require __DIR__ . '/../bootstrap.php';


\Tester\Assert::equal(999, (int) round(Tax::priceWithoutTax(1208.79)));
\Tester\Assert::equal(999, (int) round(Tax::priceWithoutTax(1148.85, Tax::REDUCED_RATE)));
