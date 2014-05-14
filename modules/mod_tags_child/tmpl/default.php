<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_tags_child
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<?php JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php'); ?>
<div class="tagschild<?php echo $moduleclass_sfx; ?>">
<?php if (!count($list)) : ?>
	<div class="alert alert-no-items"><?php echo JText::_('MOD_TAGS_CHILD_NO_ITEMS_FOUND'); ?></div>
<?php else : ?>
	<ul >
	<?php foreach ($list as $item) : ?>
	<li><?php $route = new TagsHelperRoute; ?>
		<a href="<?php echo JRoute::_(TagsHelperRoute::getTagRoute($item->tag_id . '-' . $item->alias)); ?>">
			<?php echo htmlspecialchars($item->title); ?></a>
	</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
</div>
