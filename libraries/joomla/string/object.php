<?php
/**
 * @package     Joomla.Platform
 * @subpackage  String
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 *
 * @package     Joomla.Platform
 * @subpackage  String
 * @since       12.2
 */
class JStringObject implements ArrayAccess //, JsonSerializable
{
	private $string = '';
	private $length = 0;

	public function __construct($string, $encoding = 'UTF-8')
	{
		if (!extension_loaded('mbstring'))
		{
			throw new RuntimeException('JStringObject requires mbstring');
		}
		
		if ($encoding != 'UTF-8')
		{
			mb_convert_encoding($string, 'UTF-8', $encoding);
		}

		if (!mb_check_encoding($string, 'UTF-8'))
		{
			throw new RuntimeException('String is not valid UTF-8');
		}

		$this->string = $string;
		$this->_calcLength();
	}

	public function __toString()
	{
		return $this->string;
	}

	public function toLowerCase()
	{
		$this->string = mb_strtolower($this->string, 'UTF-8');
	}

	public function toUpperCase()
	{
		$this->string = mb_strtoupper($this->string, 'UTF-8');
	}
	
	public function length()
	{
		return $this->length;
	}

	public function strpos($needle, $offset = 0)
	{
		return mb_strpos($this->string, $needle, $offset, 'UTF-8');
	}
	
	public function offsetExists($offset)
	{
		return (bool)($this->length > $offset);
	}

	public function offsetUnset($offset)
	{
		throw new BadMethodCallException('Cannot unset string offsets');
	}

	public function offsetGet($offset)
	{
		if (!is_numeric($offset) || $offset < 0)
		{
			throw new InvalidArgumentException('Offset must be a non-negative integer');
		}

		return mb_substr($this->string, $offset, 1, 'UTF-8');
	}

	public function offsetSet($offset, $value)
	{
		$value = (string) $value;
		if (!mb_check_encoding($value, 'UTF-8'))
		{
			throw new RuntimeException('String is not valid UTF-8');
		}

		$tmp = mb_substr($this->string, 0, $offset, 'UTF-8');
		$tmp .= mb_substr($value, 0, 1, 'UTF-8');
		$tmp .= mb_substr($this->string, $offset + 1, $this->length - $offset, 'UTF-8');

		$this->string = $tmp;
		$this->_calcLength();
	}
	
	public function jsonSerialize()
	{
		return $this->string;
	}

	private function _calcLength()
	{
		$this->length = mb_strlen($this->string, 'UTF-8');
	}
}
