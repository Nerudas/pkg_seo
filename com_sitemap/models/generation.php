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

class SitemapModelGeneration extends BaseDatabaseModel
{
	/**
	 * Method to generate sitemap.xml
	 *
	 * @return bool|int
	 *
	 * @since 1.0.0
	 */
	public function generate()
	{

		$this->setError('Function in development');

		return false;
	}
}