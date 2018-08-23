<?php
/**
 * @package    Sitemap Component
 * @version    1.0.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

class SitemapModelGeneration extends BaseDatabaseModel
{
	/**
	 * Plugins array
	 *
	 * @var    array
	 *
	 * @since  1.0.0
	 */
	protected $_plugins = null;

	/**
	 * Urls array
	 *
	 * @var    array
	 *
	 * @since  1.0.0
	 */
	protected $_urls = null;

	/**
	 * Method to generate sitemap.xml
	 *
	 * @return bool|int
	 *
	 * @since 1.0.0
	 */
	public function generate()
	{

		if (empty($this->getPlugins()))
		{
			$this->setError('COM_SITEMAP_ERROR_PLUGINS_NOT_FOUND');

			return false;
		}

		$urls = $this->getUrls();

		echo '<pre>', print_r($urls, true), '</pre>';



		$this->setError('Function in development');

		return false;
	}

	/**
	 * Method to get Urls array
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	protected function getUrls()
	{
		if ($this->_urls === null)
		{
			$site   = SiteApplication::getInstance('site');
			$router = $site->getRouter();
			$config = ComponentHelper::getParams('com_sitemap');

			$urls = array();
			foreach ($this->getPlugins() as $name => $plugin)
			{
				$pluginUrls = $plugin->getUrls();
				if (!empty($pluginUrls))
				{
					foreach ($pluginUrls as $url)
					{
						$url = new Registry($url);

						if ($loc = $url->get('loc', false))
						{
							$loc        = trim(str_replace('administrator/', '', $router->build($loc)->toString()), '/');
							$key        = (empty($loc)) ? 'default_page' : $loc;
							$changefreq = $url->get('changefreq', $config->get('changefreq', 'weekly'));
							$priority   = $url->get('priority', $config->get('priority', '0.5'));
							$lastmod    = $url->get('lastmod', false);

							$item             = new stdClass();
							$item->loc        = Uri::root() . $loc;
							$item->changefreq = $changefreq;
							$item->priority   = $priority;

							if ($lastmod)
							{
								$item->lastmod = Factory::getDate($lastmod)->toISO8601();
							}

							$urls[$key] = new Registry($item);
						}
					}
				}
			}

			$this->_urls = $urls;
		}

		return $this->_urls;
	}

	/**
	 * Method to get Plugins array
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	protected function getPlugins()
	{
		if ($this->_plugins === null)
		{
			$plugins = array();

			PluginHelper::importPlugin('sitemap');
			$rows = PluginHelper::getPlugin('sitemap');

			foreach ($rows as $plugin)
			{
				$key       = $plugin->name;
				$className = 'plg' . $plugin->type . $plugin->name;
				if (class_exists($className))
				{
					$plugin = new $className($this, (array) $plugin);

					if (method_exists($className, 'getUrls'))
					{
						$plugins[$key] = $plugin;
					}
				}
			}

			$this->_plugins = $plugins;
		}


		return $this->_plugins;
	}

	/**
	 * Attach an observer object
	 *
	 * @param   object $observer An observer object to attach
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function attach($observer)
	{
	}
}