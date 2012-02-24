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
 * Fake stemmer that doesn't actually stem. It's fast though.
 *
 * @package     Joomla.Platform
 * @subpackage  Language
 * @since       12.1
 */
class JLanguageStemmerDummy extends JLanguageStemmer
{

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
			return $token;
	}
	
	/**
	 * Test if a stemmer supports a given language.
	 *
	 * @param   string  $lang   The language to test.
	 *
	 * @return  boolean  True if the language is supported.
	 *
	 * @since   12.1
	 */
	public function supportsLanguage($lang)
	{
		return true;
	}
}
