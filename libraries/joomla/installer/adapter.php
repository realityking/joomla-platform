<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Installer
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.base.adapterinstance');

/**
 * Abstract adapter for the installer.
 *
 * @package     Joomla.Platform
 * @subpackage  Installer
 * @since       12.2
 */
abstract class JInstallerAdapter extends JAdapterInstance
{
	/**
	 * The unique identifier for the extension (e.g. mod_login)
	 *
	 * @var    string
	 * @since  12.2
	 * */
	protected $element = null;

	/**
	 * Copy of the XML manifest file
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $manifest = null;

	/**
	 * Name of the extension
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $name = null;

	/**
	 * Install function routing
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $route = 'Install';

	/**
	 * @var    string
	 * @since  12.2
	 */
	protected $scriptElement = null;

	/**
	 * A path to the PHP file that the scriptfile declaration in
	 * the manifest refers to.
	 *
	 * @var    string
	 * @since  12.2
	 * */
	protected $manifest_script = null;

	/**
	 * Load language files
	 *
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function doLoadLanguage($extension, $source)
	{
		$lang = JFactory::getLanguage();
		$lang->load($extension . '.sys', $source, null, false, false)
			|| $lang->load($extension . '.sys', JPATH_SITE, null, false, false)
			|| $lang->load($extension . '.sys', $source, $lang->getDefault(), false, false)
			|| $lang->load($extension . '.sys', JPATH_SITE, $lang->getDefault(), false, false);
	}

	/**
	 * Actually refresh the extension table cache
	 *
	 * @return  boolean  Result of operation, true if updated, false on failure
	 *
	 * @since   12.2
	 */
	protected function doRefreshManifestCache($manifestPath)
	{
		$this->parent->manifest = $this->parent->isManifest($manifestPath);
		$this->parent->setPath('manifest', $manifestPath);

		$manifest_details = JInstaller::parseXMLInstallFile($this->parent->getPath('manifest'));
		$this->parent->extension->manifest_cache = json_encode($manifest_details);
		$this->parent->extension->name = $manifest_details['name'];

		try
		{
			return $this->parent->extension->store();
		}
		catch (RuntimeException $e)
		{
			JLog::add(JText::_('JLIB_INSTALLER_ERROR_REFRESH_MANIFEST_CACHE'), JLog::WARNING, 'jerror');
			return false;
		}
	}

	/**
	 * Generic install method for extensions
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function install()
	{
		// Get the extension manifest object
		$this->manifest = $this->parent->getManifest();

		// Set the extensions name
		$name = (string) $this->manifest->name;
		$name = JFilterInput::getInstance()->clean($name, 'string');
		$this->name = $name;

		// Get the component description
		$description = (string) $this->manifest->description;
		if ($description)
		{
			$this->parent->set('message', JText::_($description));
		}
		else
		{
			$this->parent->set('message', '');
		}
	}

	/**
	 * Setup the manifest script file for those adapters that use it.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function setupScriptfile()
	{
		// If there is an manifest class file, lets load it; we'll copy it later (don't have dest yet)
		$this->scriptElement = (string) $this->manifest->scriptfile;

		if ($manifestScript)
		{
			$manifestScriptFile = $this->parent->getPath('source') . '/' . $this->scriptElement;

			if (is_file($manifestScriptFile))
			{
				// Load the file
				include_once $manifestScriptFile;
			}

			$classname = $this->getScriptClassName();

			if (class_exists($classname))
			{
				// Create a new instance
				$this->parent->manifestClass = new $classname($this);

				// And set this so we can copy it later
				$this->set('manifest_script', $manifestScript);
			}
		}
	}

	protected function triggerManifestScriptPreflight()
	{
		ob_start();
		ob_implicit_flush(false);

		if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'preflight'))
		{
			if ($this->parent->manifestClass->preflight($this->route, $this) === false)
			{
				// Preflight failed, rollback changes
				$this->parent->abort(JText::_('JLIB_INSTALLER_ABORT_INSTALL_CUSTOM_INSTALL_FAILURE'));
				return false;
			}
		}

		// Create msg object; first use here
		$msg = ob_get_contents();
		ob_end_clean();

		return $msg;
	}

	protected function triggerManifestScriptPostflight()
	{
		ob_start();
		ob_implicit_flush(false);

		if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'postflight'))
		{
			$this->parent->manifestClass->postflight('install', $this);
		}

		// Append messages
		$msg .= ob_get_contents();
		ob_end_clean();
	}

	protected function triggerManifestScriptUninstall()
	{
		ob_start();
		ob_implicit_flush(false);

		if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'postflight'))
		{
			$this->parent->manifestClass->postflight('install', $this);
		}

		// Append messages
		$msg .= ob_get_contents();
		ob_end_clean();
	}

	protected function handleDbInstall()
	{
		// Get a database connector object
		$db = $this->parent->getDbo();

		// Let's run the install queries for the component
		if (isset($this->manifest->install->sql))
		{
			$result = $this->parent->parseSQLFiles($this->manifest->install->sql);

			if ($result === false)
			{
				// Install failed, rollback changes
				$this->parent->abort(
					JText::sprintf('JLIB_INSTALLER_ABORT_INSTALL_SQL_ERROR', JText::_('JLIB_INSTALLER_' . $this->route), $db->stderr(true))
				);

				return false;
			}
		}
		return true;
	}

	/**
	 * Get the class name for the install adapter script.
	 *
	 * @return  string  The class name.
	 *
	 * @since   12.2
	 */
	protected function getScriptClassName()
	{
		return $this->element . 'InstallerScript';
	}

	/**
	 * Function used to check if extension is already installed
	 *
	 * @param   string  $extension  The element name of the extension to install
	 *
	 * @return  boolean  True if extension exists
	 *
	 * @since   12.2
	 */
	protected function extensionExists($extension, $type, $clientId = null)
	{
		// Get a database connector object
		$db = $this->parent->getDBO();

		$query = $db->getQuery(true);
		$query->select($query->qn('extension_id'))
			->from($query->qn('#__extensions'));
		$query->where($query->qn('type') . ' = ' . $query->q($type)
			->where($query->qn('element') . ' = ' . $query->q($extension));

		if (!is_null())
		{
			$query->where($query->qn('client_id') . ' = ' . (int) $clientId);
		}
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			// Install failed, roll back changes
			$this->parent->abort(JText::sprintf('JLIB_INSTALLER_ABORT_ROLLBACK', $db->stderr(true)));
			return false;
		}
		$id = $db->loadResult();

		if (empty($id))
		{
			return false;
		}

		return true;

	}
}
