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

jimport('joomla.filesystem.file');

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
	 * Sitemap xml
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $_xml = null;

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
		$xml  = $this->getXML();

		$file = JPATH_ROOT . '/sitemap.xml';

		if (JFile::exists($file))
		{
			JFile::delete($file);
		}

		JFile::append($file, $xml);

		return count($urls);
	}

	/**
	 * Method to SiteMap xml file
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	protected function getXML()
	{
		if ($this->_xml === null)
		{
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');

			foreach ($this->getUrls() as $registry)
			{
				$url = $xml->addChild('url');
				foreach ($registry->toArray() as $name => $value)
				{
					$url->addChild($name, $value);
				}
			}

			$this->_xml = $xml->asXML();
		}

		return $this->_xml;
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

			$changefreqPriority = array(
				'always'  => 1,
				'hourly'  => 2,
				'daily'   => 3,
				'weekly'  => 4,
				'monthly' => 5,
				'yearly'  => 6,
				'never'   => 7
			);

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
							$loc = trim(str_replace('administrator/', '', $router->build($loc)->toString()), '/');
							$key = (empty($loc)) ? 'default_page' : $loc;

							$changefreq = $url->get('changefreq', $config->get('changefreq', 'weekly'));
							$priority   = $url->get('priority', $config->get('priority', '0.5'));
							$lastmod    = $url->get('lastmod', false);

							$exist = (isset($urls[$key])) ? $urls[$key] : false;

							if ($exist)
							{
								$changefreq = ($changefreqPriority[$changefreq] < $changefreqPriority[$exist->get('changefreq')]) ?
									$changefreq : $exist->get('changefreq');
								$priority   = (floatval($priority) > floatval($exist->get('priority'))) ? $priority : $exist->get('priority');
								$lastmod    = ($lastmod && $lastmod > $exist->get('lastmod')) ? $lastmod : $exist->get('lastmod');
							}

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