<?php

use SeleniumClient\By;
use SeleniumClient\SelectElement;
use SeleniumClient\WebDriver;
use SeleniumClient\WebDriverWait;
use SeleniumClient\DesiredCapabilities;
use SeleniumClient\WebElement;

/**
 * Class for the back-end control panel screen.
 *
 */
class ModuleManagerPage extends AdminManagerPage
{
	protected $waitForXpath =  "//ul/li/a[@href='index.php?option=com_modules']";
	protected $url = 'administrator/index.php?option=com_modules';

	public $filters = array(
			'Site' => 'filter_client_id',
			'Status' => 'filter_state',
			'Position' => 'filter_position',
			'Type' => 'filter_module',
			'Access' => 'filter_access',
			'Language' => 'filter_language',
			);

	public $toolbar = array (
			'New' => 'toolbar-new',
			'Edit' => 'toolbar-edit',
			'Duplicate' => 'toolbar-copy',
			'Publish' => 'toolbar-publish',
			'Unpublish' => 'toolbar-unpublish',
			'Check In' => 'toolbar-checkin',
			'Trash' => 'toolbar-trash',
			'Empty Trash' => 'toolbar-delete',
			'Batch' => 'toolbar-batch',
			'Options' => 'toolbar-options',
			'Help' => 'toolbar-help',
			);

	public $submenu = array (
			'option=com_modules&filter_client_id=0',
			'option=com_modules&filter_client_id=1',
			);

	public $moduleTypes = array(
			array('client' => 'site', 'name' => 'Archived Articles'),
			array('client' => 'site', 'name' => 'Articles - Newsflash'),
			array('client' => 'site', 'name' => 'Articles - Related Articles'),
			array('client' => 'site', 'name' => 'Articles Categories'),
			array('client' => 'site', 'name' => 'Articles Category'),
			array('client' => 'site', 'name' => 'Banners'),
			array('client' => 'site', 'name' => 'Breadcrumbs'),
			array('client' => 'site', 'name' => 'Custom HTML'),
			array('client' => 'site', 'name' => 'Feed Display'),
			array('client' => 'site', 'name' => 'Footer'),
			array('client' => 'site', 'name' => 'Language Switcher'),
			array('client' => 'site', 'name' => 'Latest News'),
			array('client' => 'site', 'name' => 'Latest Users'),
			array('client' => 'site', 'name' => 'Login'),
			array('client' => 'site', 'name' => 'Menu'),
			array('client' => 'site', 'name' => 'Most Read Content'),
			array('client' => 'site', 'name' => 'Popular Tags'),
			array('client' => 'site', 'name' => 'Random Image'),
			array('client' => 'site', 'name' => 'Search'),
			array('client' => 'site', 'name' => 'Similar Tags'),
			array('client' => 'site', 'name' => 'Smart Search Module'),
			array('client' => 'site', 'name' => 'Statistics'),
			array('client' => 'site', 'name' => 'Syndication Feeds'),
			array('client' => 'site', 'name' => 'Weblinks'),
			array('client' => 'site', 'name' => 'Who\'s Online'),
			array('client' => 'site', 'name' => 'Wrapper'),
			array('client' => 'administrator', 'name' => 'Admin sub-Menu'),
			array('client' => 'administrator', 'name' => 'Administrator Menu'),
			array('client' => 'administrator', 'name' => 'Custom HTML'),
			array('client' => 'administrator', 'name' => 'Feed Display'),
			array('client' => 'administrator', 'name' => 'Joomla! Version Information'),
			array('client' => 'administrator', 'name' => 'Latest News'),
			array('client' => 'administrator', 'name' => 'Logged-in Users'),
			array('client' => 'administrator', 'name' => 'Login Form'),
			array('client' => 'administrator', 'name' => 'Multilingual Status'),
			array('client' => 'administrator', 'name' => 'Popular Articles'),
			array('client' => 'administrator', 'name' => 'Quick Icons'),
			array('client' => 'administrator', 'name' => 'Statistics'),
			array('client' => 'administrator', 'name' => 'Title'),
			array('client' => 'administrator', 'name' => 'Toolbar'),
			array('client' => 'administrator', 'name' => 'User Status'),
	);

	public function addModule($title = 'Test Module', $client = 'Site', $type = 'Archived Articles', $otherFields = null)
	{
		$this->setFilter('filter_client_id', $client);
		$this->clickButton('toolbar-new');
		$this->driver->waitForElementUntilIsPresent(By::xPath("//ul[@id='new-modules-list']//a[contains(., '" . $type . "')]"))->click();
		$moduleEditPage = $this->test->getPageObject('ModuleEditPage');
		$moduleEditPage->setFieldValues(array('Title' => $title));
		if (is_array($otherFields))
		{
			$moduleEditPage->setFieldValues($otherFields);
		}
		$moduleEditPage->clickButton('toolbar-save');
		$this->test->getPageObject('ModuleManagerPage');
	}

	public function changeModuleState($name, $state = 'published')
	{
		$this->searchFor($name);
		$this->checkAll();
		if (strtolower($state) == 'published')
		{
			$this->clickButton('toolbar-publish');
			$this->driver->waitForElementUntilIsPresent(By::xPath($this->waitForXpath));
		}
		elseif (strtolower($state) == 'unpublished')
		{
			$this->clickButton('toolbar-unpublish');
			$this->driver->waitForElementUntilIsPresent(By::xPath($this->waitForXpath));
		}
		$this->searchFor();
	}

	public function editModule($name, $fields, $groupNames = array())
	{
		$this->clickItem($name);
		$moduleEditPage = $this->test->getPageObject('ModuleEditPage');
		$moduleEditPage->setFieldValues($fields);
		$moduleEditPage->clickButton('toolbar-save');
		$this->test->getPageObject('ModuleManagerPage');
		$this->searchFor();
	}

	/**
	 * Gets the modules field values. In turn calls getFieldValues of AdminManagerPage after selecting module client.
	 */
	public function getModuleFieldValues($title, $client, $fieldNames = array())
	{
		$this->setFilter('filter_client_id', $client);
		return $this->getFieldValues('ModuleEditPage', $title, $fieldNames);
	}

	/**
	 * Gets all module types available
	 *
	 * @return  array  associative array of 'site' or 'administrator' => module name
	 */
	public function getModuleTypes()
	{
		$result = array();
		$clients = array('Site', 'Administrator');
		foreach ($clients as $client)
		{
			$this->setFilter('filter_client_id', $client);
			$this->clickButton('toolbar-new');
			$this->driver->waitForElementUntilIsPresent(By::xPath("//h2[contains(., 'Select a Module Type')]"));
			$el = $this->driver->findElement(By::id('new-modules-list'));
			$moduleElements = $el->findElements(By::xPath("//a/strong"));
			foreach ($moduleElements as $element)
			{
				$result[] = array('client' => strtolower($client), 'name' => $element->getText());
			}
			$this->driver->findElement(By::xPath("//button[contains(., 'Cancel')]"))->click();
			$moduleManagerPage = $this->test->getPageObject('ModuleManagerPage');
		}

		return $result;
	}

	public function getState($title)
	{
		$result = false;
		$this->searchFor($title);
		$text = $this->driver->findElement(By::xPath("//tbody/tr//a[contains(@onclick, 'listItemTask')]"))->getAttribute(@onclick);
		if (strpos($text, 'modules.publish') > 0)
		{
			$result = 'unpublished';
		}
		elseif (strpos($text, 'modules.unpublish') > 0)
		{
			$result = 'published';
		}
		$this->searchFor();
		return $result;
	}
}