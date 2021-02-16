<?php
namespace tea\tests\syntax;

require_once dirname(__DIR__, 2) . '/__public.php';

// ---------
$list = ['a', 'b', 'c'];

$dict = [
	'a' => 1,
	'b' => '1',
	'c' => [1]
];

echo "list is_strict_array: " . (is_strict_array($list) ? 'Yes' : 'No'), LF;
echo "list is_strict_dict: " . (is_strict_dict($list) ? 'Yes' : 'No'), LF;
echo "dict is_strict_array: " . (is_strict_array($dict) ? 'Yes' : 'No'), LF;
echo "dict is_strict_dict: " . (is_strict_dict($dict) ? 'Yes' : 'No'), LF;

echo "\nUse conditional expression:", LF;

foreach ($dict as $key => $value) {
	$result = is_int($value) ? $value + 2 : (is_string($value) ? $value . 2 : array_merge($value, [2]));

	echo "Operation result for key '{$key}' is: ", LF;
	var_dump($result);
}

echo "\nUse if-elseif-else:", LF;

foreach ($dict as $key => $value) {
	$result = null;
	if (is_int($value)) {
		$result = $value + 2;
	}
	elseif (is_string($value)) {
		$result = $value . 2;
	}
	elseif (is_array($value)) {
		$result = array_merge($value, [2]);
	}
	else {
		// no any
	}

	echo "Operation value for key '{$key}' is: ", LF;
	var_dump($result);
}
// ---------

// program end
