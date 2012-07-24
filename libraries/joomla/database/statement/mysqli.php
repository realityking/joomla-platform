<?php
/**
 * @package	 Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	 GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Abstract class for prepared statements.
 *
 * @package	 Joomla.Platform
 * @subpackage  Database
 * @since	   12.2
 * @note		Based on Doctrine's MysqliStatement
 */
class JDatabaseStatementMysqli implements IteratorAggregate
{
	protected static $_paramTypeMap = array(
		PDO::PARAM_STR => 's',
		PDO::PARAM_BOOL => 'i',
		PDO::PARAM_NULL => 's',
		PDO::PARAM_INT => 'i',
		// TODO Support LOB bigger then max package size.
		PDO::PARAM_LOB => 's'
	);

	private $_stmt;

	/**
	 * @var null|false|array
	 */
	private $_columnNames;

	/**
	 * @var null|array
	 */
	private $_rowBindedValues;

	/**
	 * @var array
	 */
	private $_bindedValues;

	/**
	 * Contains ref values for bindValue()
	 *
	 * @var   array
	 */
	protected $_values = array();

	protected $_defaultFetchMode = PDO::FETCH_BOTH;

	public function __construct(mysqli $connection, $prepareString)
	{
		$this->_stmt = $connection->prepare($prepareString);

		if ($this->_stmt === false)
		{
			throw new RuntimeException($connection->error, $connection->errno);
		}

		$paramCount = $this->_stmt->param_count;
		if ($paramCount > 0)
		{
			// Index 0 is types
			// Need to init the string else php think we are trying to access it as a array.
			$bindedValues = array(0 => str_repeat('s', $paramCount));
			$null = null;
			for ($i = 1; $i < $paramCount; $i++)
			{
				$bindedValues[] = &$null;
			}
			$this->_bindedValues = $bindedValues;
		}
	}

	public function bindParam($column, &$variable, $type = null)
	{
		if (null === $type)
		{
			$type = 's';
		}
		else
		{
			if (isset(self::$_paramTypeMap[$type]))
			{
				$type = self::$_paramTypeMap[$type];
			}
			else
			{
				throw new RuntimeException('Unknown type: ' . $type);
			}
		}

		$this->_bindedValues[$column] = &$variable;
		$this->_bindedValues[0][$column - 1] = $type;
		return true;
	}

	public function bindValue($param, $value, $type = null)
	{
		if (null === $type)
		{
			$type = 's';
		}
		else
		{
			if (isset(self::$_paramTypeMap[$type]))
			{
				$type = self::$_paramTypeMap[$type];
			}
			else
			{
				throw new RuntimeException('Unknown type: ' . $type);
			}
		}

		$this->_values[$param] = $value;
		$this->_bindedValues[$param] =& $this->_values[$param];
		$this->_bindedValues[0][$param - 1] = $type;
		return true;
	}

	public function execute($params = null)
	{
		if (null !== $this->_bindedValues)
		{
			if (null !== $params)
			{
				if (!$this->_bindValues($params))
				{
					throw new RuntimeException($this->_stmt->error, $this->_stmt->errno);
				}
			}
			else
			{
				if (!call_user_func_array(array($this->_stmt, 'bind_param'), $this->_bindedValues))
				{
					throw new RuntimeException($this->_stmt->error, $this->_stmt->errno);
				}
			}
		}

		if (!$this->_stmt->execute())
		{
			throw new RuntimeException($this->_stmt->error, $this->_stmt->errno);
		}

		if (null === $this->_columnNames)
		{
			$meta = $this->_stmt->result_metadata();
			if (false !== $meta)
			{
				$columnNames = array();
				foreach ($meta->fetch_fields() as $col)
				{
					$columnNames[] = $col->name;
				}
				$meta->free();

				$this->_columnNames = $columnNames;
				$this->_rowBindedValues = array_fill(0, count($columnNames), NULL);

				$refs = array();
				foreach ($this->_rowBindedValues as $key => &$value)
				{
					$refs[$key] =& $value;
				}

				if (!call_user_func_array(array($this->_stmt, 'bind_result'), $refs))
				{
					throw new RuntimeException($this->_stmt->error, $this->_stmt->errno);
				}
			}
			else
			{
				$this->_columnNames = false;
			}
		}

		if ($this->_columnNames !== false)
		{
			$this->_stmt->store_result();
		}
		return true;
	}

	/**
	 * Bind a array of values to bound parameters
	 *
	 * @param   array  $values
	 *
	 * @return  boolean
	 */
	private function _bindValues($values)
	{
		$params = array();
		$types = str_repeat('s', count($values));
		$params[0] = $types;

		foreach ($values as &$v)
		{
			$params[] =& $v;
		}
		return call_user_func_array(array($this->_stmt, 'bind_param'), $params);
	}

	/**
	 * @return  boolean|array
	 */
	private function _fetch()
	{
		$ret = $this->_stmt->fetch();

		if ($ret === true)
		{
			$values = array();
			foreach ($this->_rowBindedValues as $v)
			{
				// Mysqli converts them to a scalar type it can fit in.
				$values[] = null === $v ? null : (string)$v;
			}
			return $values;
		}
		return $ret;
	}

	public function fetch($fetchMode = null)
	{
		$values = $this->_fetch();
		if (null === $values)
		{
			return null;
		}

		if (false === $values)
		{
			throw new RuntimeException($this->_stmt->error, $this->_stmt->errno);
		}

		$fetchMode = $fetchMode ?: $this->_defaultFetchMode;

		switch ($fetchMode)
		{
			case PDO::FETCH_NUM:
				return $values;

			case PDO::FETCH_ASSOC:
				return array_combine($this->_columnNames, $values);

			case PDO::FETCH_BOTH:
				$ret = array_combine($this->_columnNames, $values);
				$ret += $values;
				return $ret;

			default:
				throw new RuntimeException('Unknown fetch type: '. $fetchMode);
		}
	}

	public function fetchAll($fetchMode = null)
	{
		$fetchMode = $fetchMode ?: $this->_defaultFetchMode;

		$rows = array();
		if (PDO::FETCH_COLUMN == $fetchMode)
		{
			while (($row = $this->fetchColumn()) !== false)
			{
				$rows[] = $row;
			}
		}
		else
		{
			while (($row = $this->fetch($fetchMode)) !== null)
			{
				$rows[] = $row;
			}
		}

		return $rows;
	}

	public function fetchColumn($columnIndex = 0)
	{
		$row = $this->fetch(PDO::FETCH_NUM);
		if (null === $row)
		{
			return false;
		}
		return $row[$columnIndex];
	}

	public function errorCode()
	{
		return $this->_stmt->errno;
	}

	public function errorInfo()
	{
		return $this->_stmt->error;
	}

	public function closeCursor()
	{
		$this->_stmt->free_result();
		return true;
	}

	public function rowCount()
	{
		if (false === $this->_columnNames)
		{
			return $this->_stmt->affected_rows;
		}
		return $this->_stmt->num_rows;
	}

	public function columnCount()
	{
		return $this->_stmt->field_count;
	}

	public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
	{
		$this->_defaultFetchMode = $fetchMode;
	}

	public function getIterator()
	{
		// @todo change this to use JDatabaseiterator
		$data = $this->fetchAll();
		return new JDatabaseIteratorMysqli($this->_stmt);
	}
}
