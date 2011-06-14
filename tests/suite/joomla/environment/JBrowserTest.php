<?php

require_once JPATH_PLATFORM.'/joomla/environment/browser.php';

/**
 * Test class for JBrowser.
 * Generated by PHPUnit on 2011-03-24 at 23:47:14.
 */
class JBrowserTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var JBrowser
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new JBrowser;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{

	}

	/**
	 * @todo Implement testGetInstance().
	 */
	public function testGetInstance()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testMatch().
	 */
	public function testMatch()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	public function testGetPlatform()
	{
		$this->object->match('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
		$this->assertThat(
			$this->object->getPlatform(),
			$this->equalTo('mac')
		);
	}

	public function testGetBrowser()
	{
		$this->object->match('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
		$this->assertThat(
			$this->object->getBrowser(),
			$this->equalTo('safari')
		);
	}

	public function testGetMajor()
	{
		$this->object->match('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
		$this->assertThat(
			$this->object->getMajor(),
			$this->equalTo(5)
		);
	}

	public function testGetMinor()
	{
		$this->object->match('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
		$this->assertThat(
			$this->object->getMinor(),
			$this->equalTo(0)
		);
	}

	public function testGetVersion()
	{
		$this->object->match('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
		$this->assertThat(
			$this->object->getVersion(),
			$this->equalTo('5.0')
		);
	}

	public function testGetAgentString()
	{
		$this->object->match('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
		$this->assertThat(
			$this->object->getAgentString(),
			$this->equalTo('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27')
		);
	}

	/**
	 * @todo Implement testGetHTTPProtocol().
	 */
	public function testGetHTTPProtocol()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testSetFeature().
	 */
	public function testSetFeature()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testHasFeature().
	 */
	public function testHasFeature()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGetFeature().
	 */
	public function testGetFeature()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	public function testIsViewable()
	{
		$this->object->match('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
		$this->assertThat(
			$this->object->isViewable('application/xhtml+xml'),
			$this->equalTo(TRUE)
		);
	}

	public function testIsBrowser()
	{
		$this->object->match('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
		$this->assertThat(
			$this->object->isBrowser('safari'),
			$this->equalTo(TRUE)
		);
		$this->assertThat(
			$this->object->isBrowser('SaFaRi'),
			$this->equalTo(TRUE)
		);
		$this->assertThat(
			$this->object->isBrowser('firefox'),
			$this->equalTo(FALSE)
		);
	}

	public function testIsRobot()
	{
		$this->object->match('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
		$this->assertThat(
			$this->object->isRobot(),
			$this->equalTo(FALSE)
		);
	}

	public function testIsMobile()
	{
		$this->object->match('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
		$this->assertThat(
			$this->object->isMobile(),
			$this->equalTo(FALSE)
		);
	}

	public function testIsSSLConnection()
	{
		unset($_SERVER['HTTPS']);

		$this->assertThat(
			JBrowser::isSSLConnection(),
			$this->equalTo(false)
		);

		$_SERVER['HTTPS'] = 'on';

		$this->assertThat(
			JBrowser::isSSLConnection(),
			$this->equalTo(true)
		);
	}

	/**
     * @dataProvider provider
     */
    public function testBrowser($agent, $accept, $platform, $browser, $engine, $mobile, $browserMajor, $browserMinor, $robot)
    {
        $this->object->match($agent, $accept);

		$this->assertThat(
			$this->object->getAgentString(),
			$this->equalTo(trim($agent))
		);
		$this->assertThat(
			$this->object->getPlatform(),
			$this->equalTo($platform)
		);
		$this->assertThat(
			$this->object->getBrowser(),
			$this->equalTo($browser)
		);
		$this->assertThat(
			$this->object->isBrowser($browser),
			$this->equalTo(TRUE)
		);
		$this->assertThat(
			$this->object->isBrowser('NOT-A-BROWSER'),
			$this->equalTo(FALSE)
		);
		$this->assertThat(
			$this->object->getMajor(),
			$this->equalTo($browserMajor)
		);
		$this->assertThat(
			$this->object->getMinor(),
			$this->equalTo($browserMinor)
		);
		$this->assertThat(
			$this->object->getEngine(),
			$this->equalTo($engine)
		);
		$this->assertThat(
			$this->object->isMobile(),
			$this->equalTo($mobile)
		);
		$this->assertThat(
			$this->object->isRobot(),
			$this->equalTo($robot)
		);
    }

	public function provider()
	{
		return array(
			// Apple Safari 5.0.4, Mac OS X 10.6.6
			array('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
				'mac', 'safari', 'webkit', FALSE, 5, 0, FALSE),
			// Apple Safari 1.0, Mac OS X
			array('Mozilla/5.0 (Macintosh; U; PPC Mac OS X; es) AppleWebKit/85 (KHTML, like Gecko) Safari/85',
				'',
				'mac', 'safari', 'webkit', FALSE, 0, 0, FALSE),
			// Mozilla Firefox 3.6.15, Mac OS X 10.6.6
			array('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
				'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'mac', 'firefox', 'gecko', FALSE, 3, 6, FALSE),
        	// Google Chrome 10, Mac OS X 10.6.6
			array('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.151 Safari/534.16',
				'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
				'mac', 'chrome', 'webkit', FALSE, 10, 0, FALSE),
			// Opera 11.01, Mac OS X 10.6.6
			array('Opera/9.80 (Macintosh; Intel Mac OS X 10.6.6; U; en) Presto/2.7.62 Version/11.01',
				'text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1',
				'mac', 'opera', 'presto', FALSE, 11, 01, FALSE),
			// Microsoft Internet Explorer 8, Windows Vista
			array('Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)',
				'',
				'win', 'msie', 'trident', FALSE, 8, 0, FALSE),
			// Microsoft Internet Explorer 9, Windows 7
			array('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
				'',
				'win', 'msie', 'trident', FALSE, 9, 0, FALSE),
			// Apple iPhone, iOS 1.0
			array('Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543 Safari/419.3',
				'',
				'mac', 'safari', 'webkit', TRUE, 3, 0, FALSE),
			// Apple iPad, iOS 3.2
			array('Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10',
				'',
				'mac', 'safari', 'webkit', TRUE, 4, 0, FALSE),
			// Apple iPod, iOS 3.1.1
			array('Mozilla/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/4A93 Safari/419.3',
				'',
				'mac', 'safari', 'webkit', TRUE, 3, 0, FALSE),
			// Google Nexus One, Android 2.2 
			array('Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
				'',
				'unix', 'android', 'webkit', TRUE, 4, 0, FALSE),
			// KDE Konqueror 4.0.5, Kubuntu Hardy Heron
			array('Mozilla/5.0 (compatible; Konqueror/4.0; Linux) KHTML/4.0.5 (like Gecko)',
				'',
				'unix', 'konqueror', 'khtml', FALSE, 4, 0, FALSE),
			// Microsoft Internet Explorer 6, Windows 98
			array('Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Rogers Hi·Speed Internet; (R1 1.3))',
				'',
				'win', 'msie', '', FALSE, 6, 0, FALSE),
			// Microsoft Internet Explorer 2, Windows 95
			array('Mozilla/1.22 (compatible; MSIE 2.0; Windows 95)',
				'',
				'win', 'msie', '', FALSE, 2, 0, FALSE),
			// Netscape Navigator 9.0, Linux
			array('Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.12) Gecko/20080219 Firefox/2.0.0.12 Navigator/9.0.0.6',
				'',
				'unix', 'netscape', 'gecko', FALSE, 9, 0, FALSE),
			// Netscape Navigator 8.0, Windows 2000
			array('Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.5) Gecko/20050519 Netscape/8.0.1',
				'',
				'win', 'netscape', 'gecko', FALSE, 8, 0, FALSE),
			// OmniWeb 5.10.1, Mac OS X 10.5.8, this Browser is not (yet) supported by JBrowser, however since it claims to be Safari based, it should be deteced as such.
			array('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; en-US) AppleWebKit/531.9+(KHTML, like Gecko, Safari/528.16) OmniWeb/v622.10.0',
				'',
				'mac', 'safari', 'webkit', FALSE, 0, 0, FALSE),
			// Opera 9.62, Windows XP
			array('Opera/9.62 (Windows NT 5.1; U; en) Presto/2.1.1',
				'',
				'win', 'opera', 'presto', FALSE, 9, 62, FALSE),
		);
	}
}
