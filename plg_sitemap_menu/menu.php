<?php
/**
 * @package    Sitemap - Menu Plugin
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;

class plgSitemapMenu extends CMSPlugin
{

	/**
	 * Urls array
	 *
	 * @var    array
	 *
	 * @since  1.0.0
	 */
	protected $_urls = null;

	/**
	 * Method to get Links array
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function getUrls()
	{
		if ($this->_urls === null)
		{
			$db = Factory::getDbo();

			$menus = $this->params->def('menus', array());
			foreach ($menus as $key => $menu)
			{
				$menus[$key] = $db->quote($menu);
			}

			$excludeTypes = array(
				$db->quote('alias'),
				$db->quote('separator'),
				$db->quote('heading'),
				$db->quote('url')
			);

			$query = $db->getQuery(true)
				->select('m.id')
				->from($db->quoteName('#__menu', 'm'))
				->where('client_id = 0')
				->where('published = 1')
				->where('m.access IN (' . implode(',', Factory::getUser(0)->getAuthorisedViewLevels()) . ')')
				->where('m.type NOT IN (' . implode(',', $excludeTypes) . ')');

			if (!empty($menus))
			{
				$query->where('m.menutype IN (' . implode(',', $menus) . ')');
			}

			$db->setQuery($query);
			$ids = $db->loadColumn();

			$changefreq = $this->params->def('changefreq', 'weekly');
			$priority   = $this->params->def('priority', '0.5');

			$urls = array();
			foreach ($ids as $id)
			{
				$url             = new stdClass();
				$url->loc        = 'index.php?Itemid=' . $id;
				$url->changefreq = $changefreq;
				$url->priority   = $priority;

				$urls[] = $url;
			}

			$this->_urls = $urls;
		}

		return $this->_urls;
	}
}