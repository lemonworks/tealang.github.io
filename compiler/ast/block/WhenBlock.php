<?php
/**
 * This file is part of the Tea programming language project
 *
 * @author 		Benny <benny@meetdreams.com>
 * @copyright 	(c)2019 YJ Technology Ltd. [http://tealang.org]
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Tea;

class WhenBlock extends ControlBlock implements IElseAble, IExceptAble, ILoopLikeBlock
{
	use ElseTrait, ExceptTrait;

	const KIND = 'when_block';

	public $test;
	public $branches;
	public $is_senior_mode;

	public function __construct(BaseExpression $test)
	{
		$this->test = $test instanceof Parentheses ? $test->expression : $test;
	}

	public function set_branches(WhenBranch ...$branches)
	{
		$this->branches = $branches;
	}
}

class WhenBranch extends ControlBlock
{
	const KIND = 'when_branch';

	public $rule;

	public function __construct(BaseExpression $rule)
	{
		$this->rule = $rule;
	}
}
