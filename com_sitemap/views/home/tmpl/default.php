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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;


jimport('joomla.filesystem.file');

$uri    = (string) Uri::getInstance();
$return = urlencode(base64_encode($uri));

HTMLHelper::_('stylesheet', 'media/com_sitemap/default.min.css', array('version' => 'auto'));
?>

<div class="row-fluid">
	<div class="row-fluid icons-block">
		<div class="span2">
			<a class="item"
			   href="<?php echo Route::_('index.php?option=com_sitemap&task=generation&return=' . $return); ?>">
				<div class="img">
					<span class="icon-play large-icon"></span>
				</div>
				<div class="title">
					<?php echo Text::_('COM_SITEMAP_GENERATION'); ?>
				</div>
			</a>
		</div>
		<div class="span2">
			<?php if (JFile::exists(JPATH_ROOT . '/sitemap.xml')): ?>
				<a class="item" href="<?php echo Uri::root() . 'sitemap.xml'; ?>" target="_blank">
					<div class="img">
						<span class="icon-tree-2 large-icon"></span>
					</div>
					<div class="title">
						<?php echo Text::_('COM_SITEMAP_MAP'); ?>
					</div>
				</a>
			<?php else: ?>
				<div class="item not-active">
					<div class="img">
						<span class="icon-tree-2 large-icon"></span>
					</div>
					<div class="title text-error">
						<?php echo Text::_('COM_SITEMAP_MAP_NOT_FOUND'); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="span2">
			<a class="item" href="<?php echo Route::_('index.php?option=com_plugins&filter[folder]=sitemap'); ?>"
			   target="_blank">
				<div class="img">
					<span class="icon-power-cord large-icon"></span>
				</div>
				<div class="title">
					<?php echo Text::_('COM_SITEMAP_PLUGINS'); ?>
				</div>
			</a>
		</div>
		<div class="span2">
			<a class="item"
			   href="<?php echo Route::_('index.php?option=com_config&view=component&component=com_sitemap&return=' . $return); ?>">
				<div class="img">
					<span class="icon-options large-icon"></span>
				</div>
				<div class="title">
					<?php echo Text::_('COM_SITEMAP_CONFIG'); ?>
				</div>
			</a>
		</div>
	</div>
</div>
