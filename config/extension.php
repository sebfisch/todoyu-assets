<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

	// Basic paths
Todoyu::$CONFIG['EXT']['assets']['basePath'] 	= PATH_FILES . DIRECTORY_SEPARATOR . 'assets';
Todoyu::$CONFIG['EXT']['assets']['cachePath'] 	= PATH_CACHE . DIRECTORY_SEPARATOR . 'downloads';
	// Delete files on harddisk when delete in database
Todoyu::$CONFIG['EXT']['assets']['deleteFiles'] = true;

	// Add allowed paths where files can be downloaded from
Todoyu::$CONFIG['sendFile']['allow'][] = Todoyu::$CONFIG['EXT']['assets']['basePath'];
Todoyu::$CONFIG['sendFile']['allow'][] = Todoyu::$CONFIG['EXT']['assets']['cachePath'];

	// Configure upload folders for types
Todoyu::$CONFIG['EXT']['assets']['TYPES']['task'] = array(
	'folder'	=> 'task'
);
Todoyu::$CONFIG['EXT']['assets']['TYPES']['project'] = array(
	'folder'	=> 'project'
);

	// Set max upload file size
Todoyu::$CONFIG['EXT']['assets']['max_file_size'] = 50000000; // 50MB


	// Add task tab
if( allowed('assets', 'general:use') ) {
	TodoyuTaskManager::addTaskTab('assets', 'TodoyuTaskAssetViewHelper::getTabLabel', 'TodoyuTaskAssetViewHelper::getTabContent', 30);
}

?>