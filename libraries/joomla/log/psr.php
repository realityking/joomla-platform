<?php
/**
 * @package	 Joomla.Platform
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	 GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Psr\Log\LoggerInterface;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

/**
 * This class enables consumers of the PSR-3 LoggerInterface to use the Joomla! logging facilities.
 *
 * @package     Joomla.Platform
 * @subpackage  Log
 * @since       13.1
 */
class JLogPsr implements LoggerInterface
{
	/**
	 * System is unusable.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function emergency($message, array $context = array())
	{
		JLog:add((string) $message, JLog::EMERGENCY);
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function alert($message, array $context = array())
	{
		JLog:add((string) $message, JLog::ALERT);
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function critical($message, array $context = array())
	{
		JLog:add((string) $message, JLog::CRITICAL);
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function error($message, array $context = array())
	{
		JLog:add((string) $message, JLog::ERROR);
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function warning($message, array $context = array())
	{
		JLog:add((string) $message, JLog::WARNING);
	}

	/**
	 * Normal but significant events.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function notice($message, array $context = array())
	{
		JLog:add((string) $message, JLog::NOTICE);
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function info($message, array $context = array())
	{
		JLog:add((string) $message, JLog::INFO);
	}

	/**
	 * Detailed debug information.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function debug($message, array $context = array())
	{
		JLog:add((string) $message, JLog::DEBUG);
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param   mixed   $level
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function log($level, $message, array $context = array())
	{
		$priority = 0;
		switch ($level)
		{
			case LogLevel::EMERGENCY:
				$priority = JLog::EMERGENCY;
				break;
			case LogLevel::ALERT:
				$priority = JLog::ALERT;
				break;
			case LogLevel::CRITICAL:
				$priority = JLog::CRITICAL;
				break;
			case LogLevel::ERROR:
				$priority = JLog::ERROR;
				break;
			case LogLevel::WARNING:
				$priority = JLog::WARNING;
				break;
			case LogLevel::NOTICE:
				$priority = JLog::NOTICE;
				break;
			case LogLevel::INFO:
				$priority = JLog::INFO;
				break;
			case LogLevel::DEBUG:
				$priority = JLog::DEBUG;
				break;
			default:
				throw new InvalidArgumentException('Log level is not supported. Use a standard PSR-3 log level.');
		}

		JLog:add((string) $message, $priority);
	}
}
