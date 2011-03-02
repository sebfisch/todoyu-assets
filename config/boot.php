<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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

	// Declare ext ID, path
define('EXTID_ASSETS', 101);
define('PATH_EXT_ASSETS', PATH_EXT . '/assets');

require_once( PATH_EXT_ASSETS . '/config/constants.php' );
require_once( PATH_EXT_ASSETS . '/dwoo/plugins.php');

	// Register module locales
//TodoyuLabelManager::register('assets', 'assets', 'ext.xml');
	// Register hooks
TodoyuHookManager::registerHook('project', 'taskIcons', 'TodoyuAssetsAssetManager::hookAddTaskIcons');

?>