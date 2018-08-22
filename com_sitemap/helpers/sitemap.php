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

use Joomla\CMS\Helper\CMSHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

jimport('joomla.filesystem.file');

class SitemapHelper extends CMSHelper
{

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string $vName The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	static function addSubmenu($vName)
	{
		$uri    = (string) Uri::getInstance();
		$return = urlencode(base64_encode($uri));

		JHtmlSidebar::addEntry(Text::_('COM_SITEMAP_HOME'),
			'index.php?option=com_sitemap&view=home',
			$vName == 'home');

		JHtmlSidebar::addEntry(Text::_('COM_SITEMAP_GENERATION'),
			'index.php?option=com_sitemap&task=generation&return=' . $return,
			$vName == 'generation');

		if (JFile::exists(JPATH_ROOT . '/sitemap.xml'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_SITEMAP_MAP'),
				Uri::root() . 'sitemap.xml',
				$vName == 'sitemap');
		}

		JHtmlSidebar::addEntry(Text::_('COM_SITEMAP_PLUGINS'),
			'index.php?option=com_plugins&filter[folder]=sitemap',
			$vName == 'plugins');


		JHtmlSidebar::addEntry(Text::_('COM_SITEMAP_CONFIG'),
			'index.php?option=com_config&view=component&component=com_sitemap&return=' . $return,
			$vName == 'config');
	}
}