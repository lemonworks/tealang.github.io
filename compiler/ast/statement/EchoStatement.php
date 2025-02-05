<?php
/**
 * This file is part of the Tea programming language project
 * @copyright 	(c)2019 tealang.org
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Tea;

class EchoStatement extends BaseStatement
{
	const KIND = 'echo_statement';

	/**
	 * @var BaseExpression[]
	 */
	public $arguments;

	public $end_newline = true;

	public function __construct(array $arguments)
	{
		$this->arguments = $arguments;
	}
}

// end
