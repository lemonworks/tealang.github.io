<?php
namespace tests\syntax;

// ---------
$i = 0;
while ($i < 10) {
	if ($i == 5) {
		continue;
	}

	$i += 1;
}

$i = 0;
while (1) {
	while (true) {
		$i = $i + 1;
		if ($i < 3) {
			continue;
		}

		if ($i > 5) {
			break 2;
		}
	}
}
// ---------

// program end
