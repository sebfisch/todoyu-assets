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

/**
 * Manager for asset files
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsAssetManager {

	/**
	 * @var String		Default table for database requests
	 */
	const TABLE = 'ext_assets_asset';

	/**
	 * Session path for temporary storage path
	 *
	 * @var	String
	 */
	private static $sessionTempPath = 'assets/temppath';



	/**
	 * Get asset record
	 *
	 * @param	Integer		$idAsset
	 * @return	TodoyuAssetsAsset
	 */
	public static function getAsset($idAsset) {
		$idAsset	= intval($idAsset);

		return TodoyuRecordManager::getRecord('TodoyuAssetsAsset', $idAsset);
	}



	/**
	 * Get asset record array
	 *
	 * @param	Integer		$idAsset
	 * @return	Array
	 */
	public static function getAssetArray($idAsset) {
		$idAsset	= intval($idAsset);

		return Todoyu::db()->getRecord(self::TABLE, $idAsset);
	}



	/**
	 * Get a task asset
	 *
	 * @param	Integer				$idAsset
	 * @return	TodoyuAssetsTaskAsset
	 */
	public static function getTaskAsset($idAsset) {
		$idAsset	= intval($idAsset);

		return TodoyuRecordManager::getRecord('TodoyuAssetsTaskAsset', $idAsset);
	}



	/**
	 * Get the number of assets in a task
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function getNumTaskAssets($idTask) {
		$idTask	= intval($idTask);
		$assets	= self::getTaskAssets($idTask);

		return sizeof($assets);
	}



	/**
	 * Get IDs of assets of given parent element
	 *
	 * @param	Integer		$idParent		ID of parent element
	 * @param	Integer		$type			type of parent element, e.g. task
	 * @return	Array
	 */
	public static function getElementAssetIDs($idParent, $type = ASSET_PARENTTYPE_TASK) {
		$idParent	= intval($idParent);
		$type		= intval($type);

		$assets		= self::getElementAssets($idParent, $type);

		return TodoyuArray::getColumn($assets, 'id');
	}



	/**
	 * Get assets of given parent element
	 *
	 * @param	Integer		$idParent		ID of parent element
	 * @param	Integer		$type			type of parent element, e.g. task
	 * @return	Array
	 */
	public static function getElementAssets($idParent, $type = ASSET_PARENTTYPE_TASK) {
		$idParent	= intval($idParent);
		$type		= intval($type);

		$fields	= '*';
		$table	= self::TABLE;
		$where	= '		id_parent	= ' . $idParent .
				  ' AND	parenttype	= ' . $type .
				  ' AND	deleted		= 0';
		$order	= 'date_create DESC';

			// If person can't see all assets, limit to public and own
		if( ! Todoyu::allowed('assets', 'asset:seeAll') ) {
			$where .= ' AND (is_public 		= 1
							 OR id_person_create	= ' . Todoyu::personid() . ')';
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get the IDs of all assets of given task
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTaskAssetIDs($idTask) {
		$idTask	= intval($idTask);

		return self::getElementAssetIDs($idTask, ASSET_PARENTTYPE_TASK);
	}



	/**
	 * Get the assets of a task
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTaskAssets($idTask) {
		$idTask	= intval($idTask);

		return self::getElementAssets($idTask, ASSET_PARENTTYPE_TASK);
	}



	/**
	 * Get task ID of an asset
	 *
	 * @param	Integer		$idAsset
	 * @return	Integer
	 */
	public static function getTaskID($idAsset) {
		$idAsset	= intval($idAsset);

		$asset		= self::getAssetArray($idAsset);

		return intval($asset['id_parent']);
	}



	/**
	 * Add an uploaded file as task asset
	 *
	 * @param	Integer		$idTask			Task ID
	 * @param	String		$tempFile		Path to temporary file on server
	 * @param	String		$fileName		Filename on browser system
	 * @param	String		$mimeType		Submitted file type by browser
	 * @return	Integer		Asset ID
	 */
	public static function addTaskAsset($idTask, $tempFile, $fileName, $mimeType) {
		$idTask	= intval($idTask);

		return self::addAsset(ASSET_PARENTTYPE_TASK, $idTask, $tempFile, $fileName, $mimeType);
	}



	/**
	 * Add a new file to the system.
	 *  - Copy the file to the file structure
	 *  - Add an asset record to the database
	 *
	 * @param	Integer		$type
	 * @param	Integer		$idParent
	 * @param	String		$tempFile		Absolute path to the temporary file
	 * @param	String		$fileName		Original file name
	 * @param	String		$mimeType		File mime type
	 * @return	Integer		Asset ID
	 */
	public static function addAsset($type, $idParent, $tempFile, $fileName, $mimeType) {
		$type		= intval($type);
		$idParent	= intval($idParent);
		$basePath	= self::getStorageBasePath();

			// Move temporary file to asset storage
		$storageDir	= self::getAssetStoragePath($type, $idParent);
		$filePath	= TodoyuFileManager::addFileToStorage($storageDir, $tempFile, $fileName);

		if( $filePath === false ) {
			return false;
		}

			// Get storage path (relative to basePath)
		$relStoragePath	= str_replace($basePath . DIR_SEP, '', $filePath);

			// Get file size and file info
		$fileSize	= filesize($filePath);
		$info		= pathinfo($filePath);

			// Get mime type
		$types		= explode('/', $mimeType);
		$fileMime	= $types[0];
		$fileMimeSub= $types[1];

			// Add record to database
		$data		= array(
			'parenttype'			=> $type,
			'id_parent'				=> $idParent,
			'deleted'				=> 0,
			'is_public'				=> 0,
			'file_ext'				=> $info['extension'],
			'file_mime'				=> $fileMime,
			'file_mime_sub'			=> $fileMimeSub,
			'file_storage'			=> $relStoragePath,
			'file_name'				=> $fileName,
			'file_size'				=> $fileSize
		);

		$idAsset = TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('assets', 'asset.add', array($idAsset));

		return $idAsset;
	}



	/**
	 * Download an asset. Send headers and data to the browser
	 *
	 * @param	Integer		$idAsset
	 * @return	Boolean
	 */
	public static function downloadAsset($idAsset) {
		$idAsset	= intval($idAsset);
		$asset		= TodoyuAssetsAssetManager::getAsset($idAsset);
		$filePath	= $asset->getFileStoragePath();
		$mimeType 	= $asset->getMimeType();
		$filename	= $asset->getFilename();

		TodoyuHookManager::callHook('assets', 'asset.download', array($idAsset));

		return TodoyuFileManager::sendFile($filePath, $mimeType, $filename);
	}



	/**
	 * Delete an asset (file stays in file system)
	 *
	 * @param	Integer		$idAsset
	 */
	public static function deleteAsset($idAsset) {
		$idAsset	= intval($idAsset);
		$update		= array(
			'deleted' 		=> 1
		);

		TodoyuRecordManager::updateRecord(self::TABLE, $idAsset, $update);

			// Delete file on hard disk?
		if( Todoyu::$CONFIG['EXT']['assets']['deleteFiles'] === true ) {
			$asset		= self::getAsset($idAsset);
			$filePath	= $asset->getFileStoragePath();

			TodoyuFileManager::deleteFile($filePath);
		}

		TodoyuHookManager::callHook('assets', 'asset.delete', array($idAsset));
	}

	

	/**
	 * Toggle asset public flag
	 *
	 * @param	Integer		$idAsset
	 */
	public static function togglePublic($idAsset) {
		$idAsset	= intval($idAsset);

		Todoyu::db()->doBooleanInvert(self::TABLE, $idAsset, 'is_public');
	}



	/**
	 * Download assets zipped
	 *
	 * @param	Integer $idTask
	 * @param	Array	$assetIDs
	 */
	public static function downloadAssetsZipped($idTask, array $assetIDs) {
		$idTask		= intval($idTask);
		$assetIDs	= TodoyuArray::intval($assetIDs);

		$zipFile	= self::createAssetZip($idTask, $assetIDs);

		if( ! is_file($zipFile) ) {
			die("Download of ZIP file failed");
		}

		$filename	= 'Assets_' . $idTask . '.zip';
		$mimeType	= 'application/octet-stream';

			// Delete temporary ZIP file after download
		TodoyuFileManager::sendFile($zipFile, $mimeType, $filename);

		unlink($zipFile);

		TodoyuHookManager::callHook('assets', 'asset.download.zip', array($idTask, $assetIDs));
	}



	/**
	 * Create ZIP file from assets
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$assetIDs
	 * @return	String		path to ZIP file
	 */
	private static function createAssetZip($idTask, array $assetIDs) {
		$idTask		= intval($idTask);
		$assetIDs	= TodoyuArray::intval($assetIDs, true, true);

		TodoyuFileManager::makeDirDeep(Todoyu::$CONFIG['EXT']['assets']['cachePath']);

			// Build file path and name
		$zipName	= self::makeZipFileName($idTask, $assetIDs);
		$zipPath	= TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['EXT']['assets']['cachePath'] . DIR_SEP . $zipName);

			// Create ZIP file
		$zip	= new ZipArchive();
		$status	= $zip->open($zipPath, ZIPARCHIVE::CREATE);

		if( $status !== true ) {
			TodoyuLogger::logError('Can\'t create zip archive: ' . $zipPath);
		}

			// Get asset data
		$fields	= 'file_name, file_storage';
		$table	= self::TABLE;

		if( count($assetIDs) > 0 ) {
			$where	= 'id IN(' . implode(',', $assetIDs) . ')';
		} else {
			$where = 'id_task = ' . $idTask . ' AND deleted = 0';
		}

			// Get selected asset records
		$assets			= Todoyu::db()->getArray($fields, $table, $where);
			// Counter for identical file names
		$fileNameCounter= array();

			// Add assets
		foreach($assets as $asset) {
				// Handle duplicated file names
			$inZipName	= $asset['file_name'];
				// If filename is already in archive, post-file with a counter
			if( array_key_exists($inZipName, $fileNameCounter) ) {
				$index		= intval($fileNameCounter[$asset['file_name']]);
				$inZipName	= TodoyuFileManager::appendToFilename($inZipName, '_' . $index);
			}
				// Get path to file on server
			$storageFilePath= self::getStoragePath($asset['file_storage']);

				// Add file
			$success = $zip->addFile($storageFilePath, $inZipName);

				// Log error if adding failed
			if( $success !== true ) {
				$data	= array(
					'dir'		=> Todoyu::$CONFIG['EXT']['assets']['asset_dir'],
					'storage'	=> $asset['file_storage'],
					'file'		=> $asset['file_name']
				);
				TodoyuLogger::logError('Failed to add asset to zip file', $data);
			}

				// Count filename (check for duplicates)
			$fileNameCounter[$asset['file_name']]++;
		}

		$zip->close();

		return $zipPath;
	}



	/**
	 * Get path to file in storage
	 *
	 * @param	String		$storageFileName		Relative path from asset storage
	 * @return	String		Absolute path to file in asset storage
	 */
	public static function getStoragePath($storageFileName) {
		return Todoyu::$CONFIG['EXT']['assets']['basePath'] . DIR_SEP . $storageFileName;
	}



	/**
	 * Generate filename for ZIP file
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$assetIDs
	 * @return	String		filename of ZIP
	 */
	private static function makeZipFileName($idTask, array $assetIDs) {
		$idTask		= intval($idTask);
		$assetIDs	= TodoyuArray::intval($assetIDs, true, true);

		$field	= 'date_create';
		$table	= self::TABLE;
		if( count($assetIDs) > 0 ) {
			$where	= 'id IN(' . implode(',', $assetIDs) . ')';
		} else {
			$where = 'id_task = '.intval($idTask).' AND deleted = 0';
		}

		$dates	= Todoyu::db()->getColumn($field, $table, $where);
		$sum	= array_sum($dates);

		return 'Assets_' . $idTask . '_' . $sum . '.zip';
	}



	/**
	 * Get storage base path (absolute path)
	 *
	 * @return	String
	 */
	public static function getStorageBasePath() {
		return TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['EXT']['assets']['basePath']);
	}



	/**
	 * Get storage path of assets of given task
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function getTaskAssetStoragePath($idTask) {
		$idTask		= intval($idTask);

		return self::getAssetStoragePath(ASSET_PARENTTYPE_TASK, $idTask);
	}



	/**
	 * Get (root) storage path of assets
	 *
	 * @param	Integer		$type			type of parent element, e.g. task
	 * @param	Integer		$idParent		ID of parent element
	 * @return	String		Path to storage folder
	 */
	public static function getAssetStoragePath($type, $idParent) {
		$type		= intval($type);
		$idParent	= intval($idParent);
		$basePath	= self::getStorageBasePath();

		switch($type) {
			case ASSET_PARENTTYPE_TASK:
					// User project ID as parent folder
				$folder 	= TodoyuProjectTaskManager::getProjectID($idParent);;
				break;

//			case ASSET_PARENTTYPE_PROJECT:
//				$folder = Todoyu::$CONFIG['EXT']['assets']['TYPES']['project']['folder'];
//				break;

			default:
				die('INVALID ASSET TYPE');
		}

		$storagePath = TodoyuFileManager::pathAbsolute($basePath . DIR_SEP . $folder . DIR_SEP . $idParent);

			// Create storage folder if it doesn't exist
		TodoyuFileManager::makeDirDeep($storagePath);

		return $storagePath;
	}



	/**
	 * Check whether a task has assets
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function taskHasAssets($idTask) {
		$idTask		= intval($idTask);
		$assetIDs	= self::getTaskAssetIDs($idTask);

		return sizeof($assetIDs) > 0;
	}



	/**
	 * Add asset icon to task if it has assets
	 *
	 * @param	Array		$icons
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function hookAddTaskIcons(array $icons, $idTask) {
		$idTask	= intval($idTask);

		if( self::taskHasAssets($idTask) ) {
			$icons['assets'] = array(
				'id'		=> 'task-' . $idTask . '-assets',
				'class'		=> 'assets',
				'label'		=> 'assets.ext.task.icon',
				'position'	=> 80
			);
		}

		return $icons;
	}



	/**
	 * Modify form for task creation - add assets fieldset
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idTask
	 * @param	Array			$params
	 * @return	TodoyuForm
	 */
	public static function hookAddAssetUploadToTaskCreateForm(TodoyuForm $form, $idTask, array $params) {
		$idTask	= intval($idTask);

		if( $idTask === 0 && TodoyuProjectTaskManager::getTask($idTask)->isTask() ) {
				// Set encoding type, add initialization of file options, add hidden field MAX_FILE_SIZE
			$form->setEnctype('multipart/form-data');
			$form->setAttribute('extraOnDisplay', 'Todoyu.Ext.assets.TaskEdit.initFileOperationButtons(' . $idTask . ')' );
			$form->addHiddenField('MAX_FILE_SIZE', 50000000, true, true);

				// Add assets fieldset
			$xmlPathSave	= 'ext/assets/config/form/taskedit-fieldset-assets.xml';
			$assetForm		= TodoyuFormManager::getForm($xmlPathSave);
			$assetFieldset	= $assetForm->getFieldset('assets');

			$form->addFieldset('assets', $assetFieldset, 'before:buttons');
		}

		return $form;
	}



	/**
	 * Modify form for quicktask creation - add assets fieldset
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idTask
	 * @param	Array			$params
	 * @return	TodoyuForm
	 */
	public static function hookModifyQuickTaskForm(TodoyuForm $form, $idTask, array $params = array()) {
		return self::hookAddAssetUploadToTaskCreateForm($form, $idTask, $params, true);
	}



	/**
	 * Save assets (uploaded inline from within task creation form) of new task
	 *
	 * @param	Array		$data
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function hookAddUplodedAssetsToTask(array $data, $idTask) {
		$idTask	= intval($idTask);

			// Remove asset fields from form data
		unset($data['MAX_FILE_SIZE']);
		unset($data['id_asset']);

		$assets	= TodoyuAssetsTemporaryUploadManager::getFiles();

		foreach($assets as $asset) {
			self::addTaskAsset($idTask, $asset['path'], $asset['name'], $asset['type']);
		}

		TodoyuAssetsTemporaryUploadManager::destroy();

		return $data;
	}



	/**
	 * Get asset file select options (temporary uploaded assets to be attached to to-be created task)
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTaskAssetFileOptions($idTask = 0) {
		$files	= TodoyuAssetsTemporaryUploadManager::getFiles();
		$options= array();

		$files	= TodoyuArray::sortByLabel($files, 'time', true);

		foreach($files as $file) {
			$options[] = array(
				'value'	=> $file['key'],
				'label'	=> $file['name'] . ' (' . TodoyuTime::format($file['time'], 'timesec') . ')'
			);
		}

		return $options;
	}



	/**
	 * Save temp path in session
	 *
	 * @param	String		$path
	 */
	public static function saveSessionTempPath($path) {
		TodoyuSession::set(self::$sessionTempPath, $path);
	}



	/**
	 * Get temp path from session
	 *
	 * @return	String
	 */
	public static function getSessionTempPath() {
		return TodoyuSession::get(self::$sessionTempPath);
	}



	/**
	 * Check whether session has a temp path
	 *
	 * @return	Boolean
	 */
	public static function hasSessionTempPath() {
		return TodoyuSession::isIn(self::$sessionTempPath);
	}



	/**
	 * Get items for the task context menu
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$items
	 * @return	Array
	 */
	public static function getTaskContextMenuItems($idTask, array $items) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);
		$allowed= array();

		$allowUse	= Todoyu::allowed('assets', 'general:use');
		$isLocked	= $task->isLocked();
		$isTask		= $task->isTask();

		if( $allowUse && !$isLocked && $isTask ) {
			$ownItems	=& Todoyu::$CONFIG['EXT']['asset']['ContextMenu']['Task'];

			if( array_key_exists('add', $items) ) {
				$allowed['add']['submenu']['add-asset'] = $ownItems['add']['submenu']['add-asset'];
			}
		}

		return array_merge_recursive($items, $allowed);
	}

}

?>