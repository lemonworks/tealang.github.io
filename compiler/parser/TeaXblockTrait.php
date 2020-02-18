<?php
/**
 * This file is part of the Tea programming language project
 *
 * @author 		Benny <benny@meetdreams.com>
 * @copyright 	(c)2019 YJ Technology Ltd. [http://tealang.org]
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Tea;

const _XTAG_SELF_END = '/>';

const _LEAF_TAGS = [
	'meta', 'link', 'img', 'input', 'br', 'hr', '!doctype',
	'wbr', 'col', 'embed', 'param', 'source', 'track', 'area', 'keygen'
];

const _VIRTUAL_TAG = 'vtag';

trait TeaXBlockTrait
{
	protected function read_xblock()
	{
		$block_previous_spaces = $this->get_previous_inline_spaces();

		$token = $this->scan_token();
		if (!TeaHelper::is_xtag_name($token)) {
			throw $this->new_unexpect_exception($token);
		}

		$items[] = $this->try_read_xtag($token, $block_previous_spaces);

		$skiped_spaces = '';
		while ($this->get_token_closely($skiped_spaces) === _XTAG_OPEN) {
			$this->scan_token_ignore_empty(); // _XTAG_OPEN

			$token = $this->scan_token(); //
			if (!TeaHelper::is_xtag_name($token)) {
				break;
			}

			$items[count($items) - 1]->post_spaces = $this->strip_previous_spaces($skiped_spaces, $block_previous_spaces);
			$items[] = $this->read_xtag($token, $block_previous_spaces);
		}

		$node = new XBlock(...$items);
		$node->pos = $this->pos;

		return $node;
	}

	protected function try_read_xtag(?string $tag, string $block_previous_spaces)
	{
		if (!TeaHelper::is_xtag_name($tag)) {
			throw $this->new_unexpect_exception($token);
		}

		return $this->read_xtag($tag, $block_previous_spaces);
	}

	protected function read_xtag(string $tag, string $block_previous_spaces)
	{
		if ($tag === _EXCLAMATION) {
			$tag .= $this->scan_token(); // maybe the !DOCTYPE
			if ($tag === '!--') {
				// <!--
				return $this->read_xcomment_block($block_previous_spaces);
			}
		}

		// support the variable tag name
		// eg. <item=$name>...</item>
		if ($this->skip_token_ignore_space(_ASSIGN) && $this->skip_token_ignore_space(_DOLLAR)) {
			$real_tag = $this->try_read_identifier_expression();
		}

		$attributes = $this->read_xtag_attributes();

		$current_token = $this->get_current_token();

		// xtag end
		if ($current_token === _XTAG_SELF_END) {
			return new XBlockElement($real_tag ?? $tag, $attributes);
		}

		// no children
		if (in_array(strtolower($tag), _LEAF_TAGS, true)) {
			return new XBlockLeaf($real_tag ?? $tag, $attributes);
		}

		// expect xtag head close
		if ($current_token !== _XTAG_CLOSE) {
			throw $this->new_unexpect_exception();
		}

		$children = $this->read_xtag_children($tag, $block_previous_spaces);

		return new XBlockElement($real_tag ?? $tag, $attributes, $children);
	}

	protected function read_xcomment_block(string $block_previous_spaces)
	{
		$content = $this->scan_to_token('-->');
		$this->scan_token(); // skip -->

		$block_previous_spaces && $content = $this->strip_previous_spaces($content, $block_previous_spaces);

		return new XBlockComment($content);
	}

	protected function read_xtag_attributes()
	{
		$string = '';
		$items = [];
		while (($token = $this->scan_token()) !== null) {
			// the close tag
			if ($token === _XTAG_CLOSE || $token === _XTAG_SELF_END) {
				if ($string !== '') {
					$items[] = $string;
				}

				return $items;
			}

			if ($token === _SHARP && $this->skip_token(_BLOCK_BEGIN)) {
				$expression = $this->read_instring_sharp_expression();
				static::collect_and_reset_temp($items, $string, $expression);
				continue;
			}
			elseif ($token === _DOLLAR) {
				if ($this->get_token() === _BLOCK_BEGIN) {
					$expression = $this->read_dollar_block_expression();
					if (!$expression) {
						continue; // just a empty expression
					}
				}
				else {
					$expression = $this->try_read_identifier_expression();
					if (!$expression) {
						$string .= $token;
						continue;
					}
				}

				static::collect_and_reset_temp($items, $string, $expression);
				continue;
			}
			elseif ($token === _BACK_SLASH) {
				$token .= $this->scan_token();
			}

			// the string
			$string .= $token;
		}

		// close not found
		throw $this->new_unexpect_exception($token);
	}

	protected function read_xtag_children(string $tag, $block_previous_spaces)
	{
		$items = [];
		$string = '';
		while (($token = $this->scan_token()) !== null) {
			switch ($token) {
				case _XTAG_OPEN:
					if ($string !== '') {
						$items[] = $string;
						$string = ''; // reset
					}

					// it should be a child tag
					$next = $this->scan_token();
					if (TeaHelper::is_xtag_name($next)) {
						$items[] = $this->read_xtag($next, $block_previous_spaces);
					}
					elseif (TeaHelper::is_space_tab($next)) {
						$string .= $token . $next; // that's just a string
						continue 2;
					}
					elseif ($next === _SLASH) { // the </
						if ($this->scan_token() !== $tag) {
							// a wrong close tag
							throw $this->new_unexpect_exception($token);
						}

						$this->expect_token_ignore_empty(_XTAG_CLOSE); // the >

						// current element end
						return $this->strip_previous_spaces_for_items($items, $block_previous_spaces);
					}
					else {
						throw $this->new_unexpect_exception($next);
					}

					break;

				case _SHARP:
					if ($this->skip_token(_BLOCK_BEGIN)) {
						$expression = $this->read_instring_sharp_expression();
						static::collect_and_reset_temp($items, $string, $expression);
					}
					else {
						$string .= $token;
					}
					break;

				case _DOLLAR:
					if ($this->get_token() === _BLOCK_BEGIN) {
						$expression = $this->read_dollar_block_expression();
						if (!$expression) {
							break;
						}
					}
					else {
						$expression = $this->try_read_identifier_expression();
						if (!$expression) {
							$string .= $token;
							break;
						}
					}

					static::collect_and_reset_temp($items, $string, $expression);
					break;

				case _BACK_SLASH:
					$token .= $this->scan_token();
					// unbreak

				default:
					// the string
					$string .= $token;
			}
		}

		// the close not found
		throw $this->new_unexpect_exception($token);
	}

	protected function strip_previous_spaces_for_items(array $items, string $block_previous_spaces)
	{
		if (empty($block_previous_spaces)) {
			return $items;
		}

		foreach ($items as $key => $value) {
			if (is_string($value)) {
				$items[$key] = $this->strip_previous_spaces($value, $block_previous_spaces);
			}
		}

		return $items;
	}

	protected function strip_previous_spaces(string $string, string $block_previous_spaces)
	{
		if (strpos($string, $block_previous_spaces) !== false) {
			return str_replace(LF . $block_previous_spaces, LF, $string);
		}

		return $string;
	}
}

// end
