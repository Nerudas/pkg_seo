<?php
/**
 * @package    JZCron - Sitemap Plugin
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;

class plgJZCronSitemap extends CMSPlugin
{

	/**
	 * @param JObject $options subtask options
	 * @param JObject $params  plguin params
	 *
	 * @return int
	 *
	 * @since 1.0.0
	 */
	public function runSubtask($options, $params)
	{
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_sitemap/models', 'SitemapModel');
		$model = BaseDatabaseModel::getInstance('Generation', 'SitemapModel', array('ignore_request' => true));

		return $model->generate();
	}

	/**
	 * @param int     $data    count deletes
	 * @param JObject $options subtask options
	 * @param JObject $params  plguin params
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getNotification($data = 0, $options, $params)
	{
		$notification = '';
		if ($data > 0)
		{
			$notification = Text::sprintf('PLG_JZCRON_SITEMAP_SUCCESS', $data);
		}

		return $notification;
	}
}