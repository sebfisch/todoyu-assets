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

TodoyuHookManager::registerHook('project', 'taskIcons', 'TodoyuAssetsAssetManager::hookAddTaskIcons');
TodoyuHookManager::registerHook('project', 'quickcreatetask', 'TodoyuAssetsAssetManager::hookRemoveTempSessionFiles');

TodoyuFormHook::registerBuildForm('ext/project/config/form/task.xml', 'TodoyuAssetsAssetManager::hookAddAssetUploadToTaskCreateForm');
TodoyuFormHook::registerSaveData('ext/project/config/form/task.xml', 'TodoyuAssetsAssetManager::hookSaveTask');

TodoyuFormHook::registerBuildForm('ext/project/config/form/quicktask.xml', 'TodoyuAssetsAssetManager::hookModifyQuickTaskForm');
TodoyuFormHook::registerSaveData('ext/project/config/form/quicktask.xml', 'TodoyuAssetsAssetManager::hookSaveTask');

?>