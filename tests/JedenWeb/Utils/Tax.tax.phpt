<?php

/**
 * Test: JedenWeb\Utils\Tax
 *
 * @author     Pavel Jurasek
 * @package    JedenWeb\Utils
 */

use JedenWeb\Utils\Tax;


require __DIR__ . '/../bootstrap.php';


$priceNoTax = 999;

$fullPriceBase = Tax::priceWithTax($priceNoTax);
$fullPriceReduced = Tax::priceWithTax($priceNoTax, Tax::REDUCED_RATE);


\Tester\Assert::equal(round($fullPriceBase - $priceNoTax), Tax::tax($fullPriceBase));
\Tester\Assert::equal(round($fullPriceReduced - $priceNoTax), Tax::tax($fullPriceReduced, Tax::REDUCED_RATE));
