<?php

/**
 * Test: JedenWeb\Utils\Validators
 *
 * @author     Pavel Jurasek
 * @package    JedenWeb\Utils
 */

use JedenWeb\Utils\Validators;


require __DIR__ . '/../bootstrap.php';



$validCard = '4408-0412-3456-7893';


Assert::same('4408041234567893', \Nette\Utils\Strings::replace($validCard, '([ -])', ''));

Assert::true(Validators::isCreditCard($validCard));
