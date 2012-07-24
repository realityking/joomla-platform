<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Interface for JDatabaseDriver classes that support prepared statements.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       12.2
 */
interface JDatabaseDriverPrepareable
{
	/*
	 * @param   mixed    $query   The SQL statement to set either as a JDatabaseQuery object or a string.
	 *
	 * @return  JDatabaseStatement
	 *
	 * @since   12.2
	 */
	public function prepare($query);
}
