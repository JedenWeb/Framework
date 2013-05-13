<?php

/**
 * Test: JedenWeb\Utils\Arrays
 *
 * @author     Pavel Jurasek
 * @package    JedenWeb\Utils
 */

use JedenWeb\Utils\Arrays;


require __DIR__ . '/../bootstrap.php';

//

$input = array(
	'a' => 1,
	'b' => 2,
	array(
		'c' => 3,
		'd' => 4
	),
);
$output = array(1, 2, 3, 4);

\Tester\Assert::compare($output, Arrays::flattenKeys($input));

//

$input = array(
	1,
	array(2)
);

\Tester\Assert::exception(function() use ($input) {
	Arrays::flattenKeys($input);
}, '\JedenWeb\InvalidStateException');

//

$input = array(
	1 => 'a',
	2 => 'b',
	array('c', 'd', 'e'),
);
$output = array('a', 'b', 'c', 'd', 'e');

\Tester\Assert::compare($output, Arrays::flattenKeys($input, FALSE));
