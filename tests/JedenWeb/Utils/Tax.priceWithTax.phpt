<?php

/**
 * Test: JedenWeb\Utils\Tax
 *
 * @author     Pavel Jurasek
 * @package    JedenWeb\Utils
 */

use JedenWeb\Utils\Tax;


require __DIR__ . '/../bootstrap.php';


\Tester\Assert::equal(1208.79, Tax::priceWithTax(999));
\Tester\Assert::equal(1148.85, Tax::priceWithTax(999, Tax::REDUCED_RATE));
