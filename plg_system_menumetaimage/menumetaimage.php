<?php
/**
 * @package    System - Menu Meta Image Plugin
 * @version    1.1.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;

jimport('joomla.filesystem.folder');

JLoader::register('FieldTypesFilesHelper', JPATH_PLUGINS . '/fieldtypes/files/helper.php');

class plgSystemMenuMetaImage extends CMSPlugin
{
	/**
	 * Images root path
	 *
	 * @var    string
	 *
	 * @since  1.1.0
	 */
	protected $images_root = 'images/menu';

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Adds additional fields & rules to From
	 *
	 * @param   Joomla\CMS\Form\Form $form The form to be altered.
	 * @param   mixed                $data The associated data for the form.
	 *
	 * @return  void
	 *
	 * @since  1.1.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		if ($form->getName() == 'com_menus.item')
		{
			Form::addFormPath(__DIR__);
			$form->loadFile('form', true);

			$filesHelper = new FieldTypesFilesHelper();
			$filesHelper->checkFolder($this->images_root);

			// Set images folder root
			$form->setFieldAttribute('images_folder', 'root', $this->images_root);
		}
	}

	/**
	 * Saves user  data
	 *
	 * @param   string $context The context of the content passed to the plugin (added in 1.6).
	 * @param   object $article A JTableContent object.
	 * @param   bool   $isNew   If the content is just about to be created.
	 *
	 * @return  void
	 *
	 * @since 1.2.0
	 */
	public function onContentAfterSave($context, $article, $isNew)
	{
		if ($context == 'com_menus.item')
		{
			$data = Factory::getApplication()->input->post->get('jform', array(), 'array');

			// Save images
			if ($isNew && !empty($data['images_folder']))
			{
				$filesHelper = new FieldTypesFilesHelper();
				$filesHelper->moveTemporaryFolder($data['images_folder'], $article->id, $this->images_root);
			}
		}
	}

	/**
	 * Listener for the `onAfterRoute` event
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	public function onAfterRoute()
	{

		$app = Factory::getApplication();
		if ($app->isSite())
		{
			$menu         = $app->getMenu()->getActive();
			$imagesHelper = new FieldTypesFilesHelper();
			if (!empty($menu->id))
			{
				$imageFolder = $this->images_root . '/' . $menu->id;
				if (JFolder::exists($imageFolder))
				{
					$metaImage = $imagesHelper->getImage('meta', $imageFolder, false, false);
					if ($metaImage)
					{
						$params = $menu->getParams();
						$params->set('menu-meta_image', $metaImage);

						$menu->setParams($params);
					}
				}
			}
		}
	}

	/**
	 *Runs after content delete
	 *
	 * @param   string $context The context of the content passed to the plugin (added in 1.6).
	 * @param   object $article A JTableContent object.
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	public function onContentAfterDelete($context, $article)
	{
		if ($context == 'com_menus.item')
		{
			$filesHelper = new FieldTypesFilesHelper();
			$filesHelper->deleteItemFolder($article->id, $this->images_root);
		}
	}
}