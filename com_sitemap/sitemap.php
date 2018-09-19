<?php
/**
 * @package    Sitemap Component
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\HTML\HTMLHelper;

JLoader::register('SitemapHelper', __DIR__ . '/helpers/sitemap.php');

HTMLHelper::_('behavior.tabstate');
HTMLHelper::addIncludePath(__DIR__ . '/helpers/html');

if (!Factory::getUser()->authorise('core.manage', 'com_sitemap'))
{
	throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

$controller = BaseController::getInstance('Sitemap');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();