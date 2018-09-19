<?php
/**
 * @package    Sitemap Component
 * @version    1.1.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;

class SiteMapController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since   1.0.0
	 */
	protected $default_view = 'home';

	/**
	 * Method to generate sitemap.xml
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function generation()
	{
		try
		{
			$model = $this->getModel('Generation', 'SitemapModel');
			if (!$count = $model->generate())
			{
				$this->setError($model->getError());
				$this->setMessage(Text::_($this->getError()), 'error');
				$this->setRedirect('index.php?option=com_sitemap');

				return false;
			}

			$this->setMessage(Text::plural('COM_SITEMAP_GENERATION_SUCCESS', $count));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect('index.php?option=com_sitemap');

			return false;
		}

		$this->setRedirect('index.php?option=com_sitemap');

		return true;
	}
}