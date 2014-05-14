<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_tags_child
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_tags_child
 *
 * @package     Joomla.Site
 * @subpackage  mod_tags_child
 * @since       3.3
 */
abstract class ModTagschildHelper
{
	/**
	 * Get a list of tags
	 *
	 * @param   JRegistry  &$params  Module parameters
	 *
	 * @return  mixed                Results array / null
	 */
	public static function getList(&$params)
	{
		$app        = JFactory::getApplication();
		$option     = $app->input->get('option');
		$view       = $app->input->get('view');

		$db         = JFactory::getDbo();
		$user       = JFactory::getUser();
		$groups     = implode(',', $user->getAuthorisedViewLevels());
		$listtype   = $params->get('listtype', 'all');
		$parent     = $params->get('parent');
		$maximum    = $params->get('maximum', 5);
		$tagsHelper = new JHelperTags;
		$prefix     = $option . '.' . $view;

		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tags/tables');
		$childTags = $tagsHelper->getTagTreeArray($parent);
		array_shift($childTags);

		if (count($childTags) < 1)
		{
			return;
		}

		if ($listtype == 'current')
		{
			$id = $app->input->getInt('id');
			$currentTags = $tagsHelper->getItemTags($prefix, $id);

			if (!$currentTags || is_null($currentTags))
			{
				return;
			}

			foreach ($currentTags as $i=>$tag)
			{
				if (!in_array($tag->tag_id, $childTags))
				{
					unset($currentTags[$i]);
				}
			}

			return $currentTags;
		}
		else
		{
			$db				= JFactory::getDbo();
			$user     		= JFactory::getUser();
			$groups 		= implode(',', $user->getAuthorisedViewLevels());
			$query = $db->getQuery(true)
			->select(
					array(
							$db->quoteName('id') . 'AS tag_id',
							$db->quoteName('access'),
							$db->quoteName('alias'),
							$db->quoteName('title')
							)
			)
			->from($db->quoteName('#__tags'), 't')
			->where($db->quoteName('access') . ' IN (' . $groups . ')');

			// Only return published tags
			$query->where($db->quoteName('published') . ' = 1 ');
			$query->where($db->quoteName('id') . ' IN ( ' . implode(',', $childTags) . ')');

			// Optionally filter on language
			$language = JComponentHelper::getParams('com_tags')->get('tag_list_language_filter', 'all');
			if ($language != 'all')
			{
				if ($language == 'current_language')
				{
					$language = JHelperContent::getCurrentLanguage();
				}

				$query->where($db->quoteName('language') . ' IN (' . $db->quote($language) . ', ' . $db->quote('*') . ')');
			}
			$db->setQuery($query, 0, $maximum);
			$results = $db->loadObjectList();

		}
		return $results;
	}
}
