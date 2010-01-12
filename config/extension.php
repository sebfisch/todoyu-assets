<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 snowflake productions gmbh
*  All rights reserved
*
*  This script is part of the todoyu project.
*  The todoyu project is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License, version 2,
*  (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html) as published by
*  the Free Software Foundation;
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

	// Basic paths
$CONFIG['EXT']['assets']['basePath'] 	= PATH_FILES . DIRECTORY_SEPARATOR . 'assets';
$CONFIG['EXT']['assets']['cachePath'] 	= PATH_CACHE . DIRECTORY_SEPARATOR . 'downloads';
	// Delete files on harddisk when delete in database
$CONFIG['EXT']['assets']['deleteFiles'] = true;

	// Add allowed paths where files can be downloaded from
$CONFIG['sendFile']['allow'][] = $CONFIG['EXT']['assets']['basePath'];
$CONFIG['sendFile']['allow'][] = $CONFIG['EXT']['assets']['cachePath'];

	// Configure upload folders for types
$CONFIG['EXT']['assets']['TYPES']['task'] = array(
	'folder'	=> 'task'
);
$CONFIG['EXT']['assets']['TYPES']['project'] = array(
	'folder'	=> 'project'
);

	// Add task tab
if( allowed('assets', 'general:use') ) {
	TodoyuTaskManager::addTaskTab('assets', 'TodoyuTaskAssetViewHelper::getTabLabel', 'TodoyuTaskAssetViewHelper::getTabContent', 30);
}

?>