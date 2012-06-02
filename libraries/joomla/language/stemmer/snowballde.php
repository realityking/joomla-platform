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
 * Snowball German stemmer class.
 *
 * Based on:
 * http://snowball.tartarus.org/algorithms/german/stemmer.html
 *
 * @package     Joomla.Platform
 * @subpackage  Language
 * @since       12.2
 */
class JLanguageStemmerSnowballde extends JLanguageStemmer
{
	/**
	 * Regex for matching a consonant.
	 *
	 * @var    string
	 * @since  12.1
	 */
	private static $_regex_consonant = '(?:[bcdfghjklmnpqrstvwxz]|(?<=[aeiou])y|^y)';

	/**
	 * List of vowels
	 *
	 * @const    string
	 * @since    12.2
	 */	
	const $vowels = 'aeiouyäöü';

	/**
	 * Method to stem a token and return the root.
	 *
	 * @param   string  $token  The token to stem.
	 * @param   string  $lang   The language of the token.
	 *
	 * @return  string  The root token.
	 *
	 * @since   12.1
	 */
	public function stem($token, $lang)
	{
		// Check if the token is long enough to merit stemming.
		if (strlen($token) <= 2)
		{
			return $token;
		}

		// Check if the language is English or All.
		if ($lang !== 'de')
		{
			return $token;
		}
		
		// Make lower case
		$token = JString::strtolower($token);

		// Stem the token if it is not in the cache.
		if (!isset($this->cache[$lang][$token]))
		{
			// Stem the token.
			$result = $token;
			$result = self::_prepare($result);

			// Add the token to the cache.
			$this->cache[$lang][$token] = $result;
		}

		return $this->cache[$lang][$token];
	}

	/**
	 * Preparation step:
	 * - Replace ß with ss
	 *
	 * @param   string  $word  The token to stem.
	 *
	 * @return  string
	 *
	 * @since   12.2
	 */
	private static function _prepare($word)
	{	
		// Replace ß with ss
		$word = str_replace("ß", "ss", $word);
		
		// put u and y between vowels into upper case
		$word = preg_replace(array(
          '/(?<=['. self::$vowels .'])u(?=['. self::$vowels .'])/u',
          '/(?<=['. self::$vowels .'])y(?=['. self::$vowels .'])/u'),
        array('U', 'Y'),
        $word);
        
        $length = strlen($word);

		return $word;
	}
}
