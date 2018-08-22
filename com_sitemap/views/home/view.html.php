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

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class SitemapViewHome extends HtmlView
{
	/**
	 * The sidebar html
	 *
	 * @var  string
	 *
	 * @since   1.0.0
	 */
	protected $sidebar;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return mixed A string if successful, otherwise an Error object.
	 *
	 * @since   1.0.0
	 */
	public function display($tpl = null)
	{
		JToolBarHelper::title(Text::_('COM_SITEMAP'), 'tree-2');

		// Sidebar
		SitemapHelper::addSubmenu('home');
		$this->sidebar = JHtmlSidebar::render();

		$user = Factory::getUser();
		if ($user->authorise('core.admin', 'com_sitemap') || $user->authorise('core.options', 'com_sitemap'))
		{
			JToolbarHelper::preferences('com_sitemap', '', '', 'COM_SITEMAP_CONFIG');
		}

		return parent::display($tpl);
	}
}