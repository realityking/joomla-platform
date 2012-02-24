<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Language
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Factory for the language package.
 *
 * @package     Joomla.Platform
 * @subpackage  Language
 * @since       12.1
 */
class JLanguageFactory
{
	private static $_adapters = array('snowball', 'porteren');

	private static $_stemmers = array();

	public static function registerStemmer($adapter)
	{
		self::$_adapters[] = $adapter;
	}

	public static function getStemmer($lang)
	{
		// Check if we already got a stemmer
		if (isset(self::$_stemmers[$lang])
		{
			return self::$_stemmers[$lang];
		}

		// Appearently not, so lets look trough all those registered and choose one that works
		foreach (self::$_adapters as $adapter)
		{
			$class = 'JLanguageStemmer' . ucfirst($adapter);
			if (class_exists($stemmer) && $class::supportsLanguage($lang)
			{
				self::$_stemmers[$lang] = JLanguageStemmer::getInstance($adapter);
				return self::$_stemmers[$lang];
			}
		}
		
		// We still haven't found a suitable stemmer but ran out of options. Let's use a fake one.
		self::$_stemmers[$lang] = JLanguageStemmer::getInstance('dummy');
		return self::$_stemmers[$lang];
	}
}
