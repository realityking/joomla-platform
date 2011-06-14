<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Environment
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Browser class, provides capability information about the current web client.
 *
 * Browser identification is performed by examining the HTTP_USER_AGENT
 * environment variable provided by the web server.
 *
 * This class has many influences from the lib/Browser.php code in
 * version 3 of Horde by Chuck Hagenbuch and Jon Parise.
 *
 * @package     Joomla.Platform
 * @subpackage  Environment
 * @since       11.1
 * @deprecated  This API may be changed in the near future and should not be considered stable
 */
class JBrowser extends JObject
{

	/**
	 * @var    integer  Browser major version number.
	 * @since  11.1
	 */
	private $_browserMajorVersion = 0;

	/**
	 * @var    integer  Browser mersion number
	 * @since  11.1
	 */
	private $_browserMinorVersion = 0;

	/**
	 * @var    string  Browser name.
	 * @since  11.1
	 */
	private $_browser = '';

	/**
	 * @var    integer  Engine major version number.
	 * @since  11.1
	 */
	private $_enginerMajorVersion = 0;

	/**
	 * @var    integer  Engine minor version number
	 * @since  11.1
	 */
	private $_engineMinorVersion = 0;

	/**
	 *  @var    string  Rendering engine.
	 *  @since  11.1
	 */
	private $_engine = '';

	/**
	 * @var    string  Full user agent string.
	 * @since  11.1
	 */
	private $_agent = '';

	/**
	 * @var    string  Lower-case user agent string
	 * @since  11.1
	 */
	private $_lowerAgent = '';

	/**
	 * @var    string  HTTP_ACCEPT string.
	 * @since  11.1
	 */
	private $_accept = '';

	/**
	 * @var    array  Parsed HTTP_ACCEPT string
	 * @since  11.1
	 */
	private $_accept_parsed = array();

	/**
	 * @var    string  Platform the browser is running on
	 * @since  11.1
	 */
	private $_platform = '';

	/**
	 * @var    array  Known robots.
	 * @since  11.1
	 */
	private $_robots = array(
		/* The most common ones. */
		'Googlebot',
		'msnbot',
		'Slurp',
		'Yahoo',
		/* The rest alphabetically. */
		'Arachnoidea',
		'ArchitextSpider',
		'Ask Jeeves',
		'B-l-i-t-z-Bot',
		'Baiduspider',
		'BecomeBot',
		'cfetch',
		'ConveraCrawler',
		'ExtractorPro',
		'FAST-WebCrawler',
		'FDSE robot',
		'fido',
		'geckobot',
		'Gigabot',
		'Girafabot',
		'grub-client',
		'Gulliver',
		'HTTrack',
		'ia_archiver',
		'InfoSeek',
		'kinjabot',
		'KIT-Fireball',
		'larbin',
		'LEIA',
		'lmspider',
		'Lycos_Spider',
		'Mediapartners-Google',
		'MuscatFerret',
		'NaverBot',
		'OmniExplorer_Bot',
		'polybot',
		'Pompos',
		'Scooter',
		'Teoma',
		'TheSuBot',
		'TurnitinBot',
		'Ultraseek',
		'ViolaBot',
		'webbandit',
		'www.almaden.ibm.com/cs/crawler',
		'ZyBorg');

	/**
	 * @var    boolean  Is this a mobile browser?
	 * @since  11.1
	 */
	private $_mobile = false;

	/**
	 * @var    array  Features.
	 * @since  11.1
	 * @deprecated 12.1 This variable will be dropped without replacement
	 */
	private $_features = array(
		'html'			=> true,
		'wml'			=> false,
		'xhtml+xml'		=> false,
		'mathml'		=> false,
		'svg'			=> false
	);

	/**
	 * This list of viewable images works for IE and Netscape/Mozilla.
	 *
	 * @var    array
	 * @since  11.1
	 */
	private $_images = array('jpeg', 'gif', 'png', 'pjpeg', 'x-png', 'bmp');

	/**
	 * @var    array  JBrowser instances container.
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * Create a browser instance (constructor).
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 *
	 * @since   11.1
	 */
	public function __construct($userAgent = null, $accept = null)
	{
		$this->match($userAgent, $accept);
	}

	/**
	 * Returns the global Browser object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 *
	 * @return JBrowser  The Browser object.
	 *
	 * @since  11.1
	 */
	static public function getInstance($userAgent = null, $accept = null)
	{
		$signature = serialize(array($userAgent, $accept));

		if (empty(self::$instances[$signature]))
		{
			self::$instances[$signature] = new JBrowser($userAgent, $accept);
		}

		return self::$instances[$signature];
	}

	/**
	 * Identify which of two types is preferred
	 *
	 * @param   string  $a  The first item in the comparision
	 * @param   string  $b  The second item in the comparison
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public static function _sortMime($a, $b)
	{
		if ($a[1] > $b[1])
		{
			return -1;
		}
		elseif ($a[1] < $b[1])
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Parses the user agent string and inititializes the object with
	 * all the known features and quirks for the given browser.
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 *
	 * @return	void
	 * @since   11.1
	 */
	public function match($userAgent = null, $accept = null)
	{
		// Set our agent string.
		if (is_null($userAgent))
		{
			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				$this->_agent = trim($_SERVER['HTTP_USER_AGENT']);
			}
		}
		else
		{
			$this->_agent = $userAgent;
		}
		$this->_lowerAgent = strtolower($this->_agent);

		// Set our accept string.
		if (is_null($accept))
		{
			if (isset($_SERVER['HTTP_ACCEPT']))
			{
				$this->_accept = strtolower(trim($_SERVER['HTTP_ACCEPT']));
			}
		}
		else
		{
			$this->_accept = strtolower($accept);
		}

		// Parse the HTTP Accept Header
		$accept_mime = explode(",", $this->_accept);
		for ($i = 0, $count = count($accept_mime); $i < $count; $i++)
		{
			$parts = explode(';q=', trim($accept_mime[$i]));
			if (count($parts) === 1)
			{
				$parts[1] = 1;
			}
			$accept_mime[$i] = $parts;
		}

		// Sort so the preferred value is the first
		usort($accept_mime, array(__CLASS__, '_sortMime'));

		$this->_accept_parsed = $accept_mime;

		// Check if browser accepts content type application/xhtml+xml. */* doesn't count ;)
		foreach ($this->_accept_parsed as $mime)
		{
			if (($mime[0] == 'application/xhtml+xml'))
			{
				$this->_setFeature('xhtml+xml');
			}
		}

		// Check for a mathplayer plugin is installed, so we can use MathML on several browsers.
		if (strpos($this->_lowerAgent, 'mathplayer') !== false)
		{
			$this->_setFeature('mathml');
		}

		// Check whehter the UA claims to be a mobile device.
		if (strpos($this->_lowerAgent, 'mobile') !== false) {
			$this->_mobile = true;
		}

		// Check for UTF support.
		if (isset($_SERVER['HTTP_ACCEPT_CHARSET']))
		{
			$this->_setFeature('utf', strpos(strtolower($_SERVER['HTTP_ACCEPT_CHARSET']), 'utf') !== false);
		}

		if (!empty($this->_agent))
		{
			$this->_setPlatform();
			$this->_setEngine();
			$this->_setBrowser();
		}
	}

	/**
	 * Return the currently matched platform.
	 *
	 * @return  string  The user's platform.
	 *
	 * @since   11.1
	 */
	public function getPlatform()
	{
		return $this->_platform;
	}

	/**
	 * Retrieve the current browser.
	 *
	 * @return  string  The current browser.
	 * @since   11.1
	 */
	public function getBrowser()
	{
		return $this->_browser;
	}

	/**
	 * Retrieve the current browser's engine.
	 *
	 * @return  string  The current engine.
	 * @since   11.1
	 */
	public function getEngine()
	{
		return $this->_engine;
	}

	/**
	 * Retrieve the current browser's major version.
	 *
	 * @return  integer  The current browser's major version
	 *
	 * @since   11.1.
	 */
	public function getMajor()
	{
		return $this->_browserMajorVersion;
	}

	/**
	 * Retrieve the current browser's minor version.
	 *
	 * @return  integer  The current browser's minor version.
	 *
	 * @since   11.1
	 */
	public function getMinor()
	{
		return $this->_browserMinorVersion;
	}

	/**
	 * Retrieve the current browser's version.
	 *
	 * @return  string  The current browser's version.
	 *
	 * @since   11.1
	 */
	public function getVersion()
	{
		return $this->_browserMajorVersion . '.' . $this->_browserMinorVersion;
	}

	/**
	 * Return the full browser agent string.
	 *
	 * @return  string  The browser agent string
	 *
	 * @since   11.1
	 */
	public function getAgentString()
	{
		return $this->_agent;
	}

	/**
	 * Returns the server protocol in use on the current server.
	 *
	 * @return  string  The HTTP server protocol version.
	 *
	 * @since   11.1
	 */
	public static function getHTTPProtocol()
	{
		if (isset($_SERVER['SERVER_PROTOCOL']))
		{
			if (($pos = strrpos($_SERVER['SERVER_PROTOCOL'], '/')))
			{
				return substr($_SERVER['SERVER_PROTOCOL'], $pos + 1);
			}
		}
		return null;
	}

	/**
	 * Set capabilities for the current browser.
	 *
	 * @param   string  $feature  The capability to set.
	 * @param   string  $value    Special capability parameter.
	 *
	 * @return	void
	 * @since   11.1
	 * @deprecated 12.1 This function will be dropped without replacement
	 */
	public function setFeature($feature, $value = true)
	{
		JLog::add('JBrowser::setFeature() is deprecated.', JLog::WARNING, 'deprecated');
		$this->_features[$feature] = $value;
	}

	/**
	 * Check the current browser capabilities.
	 *
	 * @param   string  $feature  The capability to check.
	 *
	 * @return  boolean  Does the browser have the capability set?
	 *
	 * @since   11.1
	 * @deprecated 12.1 This function will be dropped without replacement
	 */
	public function hasFeature($feature)
	{
		JLog::add('JBrowser::hasFeature() is deprecated.', JLog::WARNING, 'deprecated');
		return !empty($this->_features[$feature]);
	}

	/**
	 * Retrieve the current browser capability.
	 *
	 * @param   string  $feature  The capability to retrieve.
	 *
	 * @return  string  The value of the requested capability.
	 *
	 * @since   11.1
	 * @deprecated 12.1 This function will be dropped without replacement
	 */
	public function getFeature($feature)
	{
		JLog::add('JBrowser::getFeature() is deprecated.', JLog::WARNING, 'deprecated');
		return isset($this->_features[$feature]) ? $this->_features[$feature] : null;
	}

	/**
	 * Determines if a browser can display a given MIME type.
	 *
	 * Note that  image/jpeg and image/pjpeg *appear* to be the same
	 * entity, but Mozilla doesn't seem to want to accept the latter.
	 * For our purposes, we will treat them the same.
	 *
	 * @param   string  $mimetype  The MIME type to check.
	 *
	 * @return  boolean  True if the browser can display the MIME type.
	 *
	 * @since   11.1
	 */
	public function isViewable($mimetype)
	{
		$mimetype = strtolower($mimetype);
		list ($type, $subtype) = explode('/', $mimetype);

		if (!empty($this->_accept))
		{
			$wildcard_match = false;

			if (strpos($this->_accept, $mimetype) !== false)
			{
				return true;
			}

			if (strpos($this->_accept, '*/*') !== false)
			{
				$wildcard_match = true;
				if ($type != 'image')
				{
					return true;
				}
			}

			// Deal with Mozilla pjpeg/jpeg issue
			if ($this->isBrowser('mozilla')
				&& ($mimetype == 'image/pjpeg')
				&& (strpos($this->_accept, 'image/jpeg') !== false)) {
					return true;
			}

			if (!$wildcard_match)
			{
				return false;
			}
		}

		if (!$this->hasFeature('images') || ($type != 'image'))
		{
			return false;
		}

		return (in_array($subtype, $this->_images));
	}

	/**
	 * Determine if the given browser is the same as the current.
	 *
	 * @param   string  $browser  The browser to check.
	 *
	 * @return  boolean  True if the given browser the same as the current.
	 * @since   11.1
	 */
	public function isBrowser($browser)
	{
		return ($this->_browser === strtolower($browser));
	}

	/**
	 * Determines if the browser is a robot or not.
	 *
	 * @return  boolean  True if browser is a known robot.
	 *
	 * @since   11.1
	 */
	public function isRobot()
	{
		foreach ($this->_robots as $robot)
		{
			if (strpos($this->_agent, $robot) !== false)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Determines if the browser is mobile version or not.
	 *
	 * @return boolean  True if browser is a known mobile version.
	 *
	 * @since   11.1
	 */
	public function isMobile()
	{
		return $this->_mobile;
	}

	/**
	 * Determine if we are using a secure (SSL) connection.
	 *
	 * @return  boolean  True if using SSL, false if not.
	 *
	 * @since   11.1
	 */
	public static function isSSLConnection()
	{
		return ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) || getenv('SSL_PROTOCOL_VERSION'));
	}

	/**
	 * Parse the user agent string to determine the browser.
	 *
	 * @return  void
	 * @since   11.1
	 */
	private function _setBrowser()
	{
		if (preg_match('|MSIE ([0-9.]+)|', $this->_agent, $version)) {
			$this->_browser = 'msie';
			if (strpos($version[1], '.') !== false) {
				list($this->_browserMajorVersion, $this->_browserMinorVersion) = explode('.', $version[1]);
			} else {
				$this->_browserMajorVersion = $version[1];
				$this->_browserMinorVersion = 0;
			}
		} else if ((preg_match('|Netscape/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version))
				|| (preg_match('|Navigator/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version))) {
			$this->_browser = 'netscape';
			$this->_browserMajorVersion = $version[1];
			if (isset($version[2])) {
				$this->_browserMinorVersion = $version[2];
			}
		} else if (preg_match('|Firefox/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version)) {
			$this->_browser = 'firefox';
			$this->_browserMajorVersion = $version[1];
			if (isset($version[2])) {
				$this->_browserMinorVersion = $version[2];
			}
		} else if (preg_match('|Chrome/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version)) {
			$this->_browser = 'chrome';
			$this->_browserMajorVersion = $version[1];
			if (isset($version[2])) {
				$this->_browserMinorVersion = $version[2];
			}
		} else if (strpos($this->_lowerAgent, 'android') !== false) {
			$this->_browser = 'android';
			preg_match('|Version/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version);
			$this->_browserMajorVersion = $version[1];
			if (isset($version[2])) {
				$this->_browserMinorVersion = $version[2];
			}
		} else if (strpos($this->_lowerAgent, 'safari') !== false) {
			$this->_browser = 'safari';
			if (preg_match('|Version/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version)) {
				$this->_browserMajorVersion = $version[1];
				if (isset($version[2])) {
					$this->_browserMinorVersion = $version[2];
				}
			}
		} else if (strpos($this->_lowerAgent, 'opera') !== false) {
			$this->_browser = 'opera';
			// first condition is for versions >= 10, they've changed the UA string at that time
			if (preg_match('|Version/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version) || (preg_match('|Opera/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version))) {
				$this->_browserMajorVersion = $version[1];
				if (isset($version[2])) {
					$this->_browserMinorVersion = $version[2];
				}
			}
		} else if (preg_match('|Konqueror/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version)) {
			$this->_browser = 'konqueror';
			$this->_browserMajorVersion = $version[1];
			if (isset($version[2])) {
				$this->_browserMinorVersion = $version[2];
			}
		}
	}

	/**
	 * Match the platform of the browser.
	 *
	 * This is a pretty simplistic implementation, but it's intended
	 * to let us tell what line breaks to send, so it's good enough
	 * for its purpose.
	 *
	 * @return	void
	 * @since   11.1
	 */
	private function _setPlatform()
	{
		if (strpos($this->_lowerAgent, 'wind') !== false) {
			$this->_platform = 'win';
		} elseif (strpos($this->_lowerAgent, 'mac') !== false) {
			$this->_platform = 'mac';
		} else {
			$this->_platform = 'unix';
		}
	}

	/**
	 * Match the engine of the browser.
	 *
	 * @return	void
	 * @since   11.1
	 */
	private function _setEngine()
	{
		if (preg_match('|Gecko/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version)) {
			$this->_engine = 'gecko';
		} else if (preg_match('|Trident/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version)) {
			$this->_engine = 'trident';
		} else if (preg_match('|AppleWebKit/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version)) {
			$this->_engine = 'webkit';
		} else if (preg_match('|Presto/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version)) {
			$this->_engine = 'presto';
		} else if (preg_match('|KHTML/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version)) {
			$this->_engine = 'khtml';
		}
	}
}
