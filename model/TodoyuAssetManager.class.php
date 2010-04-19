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

/**
 * Manager for asset files
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetManager {

	/**
	 * Default table for database requests
	 */
	const TABLE = 'ext_assets_asset';



	/**
	 * Get project
	 *
	 * @param	Integer		$idAsset
	 * @return	TodoyuAsset
	 */
	public static function getAsset($idAsset) {
		$idAsset	= intval($idAsset);

		return TodoyuRecordManager::getRecord('TodoyuAsset', $idAsset);
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
	 * @return	TodoyuTaskAsset
	 */
	public static function getTaskAsset($idAsset) {
		$idAsset	= intval($idAsset);

		return TodoyuRecordManager::getRecord('TodoyuTaskAsset', $idAsset);
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
		$where	= '	id_parent		= ' . $idParent . ' AND
					parenttype		= ' . $type . ' AND
					deleted			= 0';
		$order	= 'date_create DESC';

			// If person can't see all assets, limit to public and own
		if( Todoyu::person()->isExternal()  && ! allowed('assets', 'asset:seeAll') ) {
			$where .= ' AND (
							is_public 		= 1 OR
							id_person_create 	= ' . personid() . '
						)';
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
	 *  - Copy the file in the file structure
	 *  - Add a asset record to the database
	 *
	 * @param	Integer		$idTask			Task ID
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
		$filePath	= self::addFileToStorage($storageDir, $tempFile, $fileName);

			// Get storage path (relative to basePath)
		$relStoragePath	= str_replace($basePath . DIR_SEP, '', $filePath);

			// Get filesize and file info
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
			'is_public'	=> 0,
			'file_ext'				=> $info['extension'],
			'file_mime'				=> $fileMime,
			'file_mime_sub'			=> $fileMimeSub,
			'file_storage'			=> $relStoragePath,
			'file_name'				=> $fileName,
			'file_size'				=> $fileSize
		);

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Move a file to the folder structure
	 *
	 * @param	Integer		$idTask
	 * @param	String		$sourceFile
	 * @param	String		$uploadFileName
	 * @return	Boolean
	 */
	public static function addFileToStorage($basePath, $sourceFile, $uploadFileName) {
		$fileName	= NOW . '_' . self::cleanFileName($uploadFileName);
		$filePath	= $basePath . DIR_SEP . $fileName;

		$fileMoved	= move_uploaded_file($sourceFile, $filePath);

		return $fileMoved ? $filePath : false ;
	}



	/**
	 * Download an asset. Send headers and data to the browser
	 *
	 * @param	Integer	$idAsset
	 */
	public static function downloadAsset($idAsset) {
		$idAsset	= intval($idAsset);

		self::sendAssetDownloadHeaders($idAsset);
		self::sendAssetDownloadData($idAsset);
	}



	/**
	 * Send asset file headers to the browser
	 *
	 * @param	Integer	$idAsset
	 */
	private static function sendAssetDownloadHeaders($idAsset) {
		$idAsset	= intval($idAsset);
		$asset		= TodoyuAssetManager::getAsset($idAsset);

		TodoyuHeader::sendHeader('Content-type', $asset->getMimeType());
		TodoyuHeader::sendHeader('Content-disposition', 'attachment; filename="' . $asset->getFilename() . '"');
		TodoyuHeader::sendHeader('Content-length', $asset->getFilesize());
		TodoyuHeader::sendHeader('Expires', date('r', NOW+600));
		TodoyuHeader::sendHeader('Last-Modified', date('r', $asset->get('date_update')));
		TodoyuHeader::sendHeader('Cache-Control', 'no-cache, must-revalidate');
		TodoyuHeader::sendHeader('Pragma', 'no-cache');
	}



	/**
	 * Send asset file to the browser
	 *
	 * @param	Integer	$idAsset
	 */
	private static function sendAssetDownloadData($idAsset) {
		$idAsset	= intval($idAsset);
		$asset		= TodoyuAssetManager::getAsset($idAsset);
		$filePath	= $asset->getFileStoragePath();

		TodoyuFileManager::sendFile($filePath);
	}



	/**
	 * Delete an asset (file stays in file system)
	 *
	 * @param	Integer	$idAsset
	 */
	public static function deleteAsset($idAsset) {
		$idAsset	= intval($idAsset);
		$update		= array(
			'deleted' 		=> 1
		);

		TodoyuRecordManager::updateRecord(self::TABLE, $idAsset, $update);

			// Delete file on harddisk?
		if( Todoyu::$CONFIG['EXT']['assets']['deleteFiles'] === true ) {
			$asset		= self::getAsset($idAsset);
			$filePath	= $asset->getFileStoragePath();

			unlink($filePath);
		}
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

		$filename	= 'Assets_' . $idTask . '.zip';
		$filesize	= filesize($zipFile);

		TodoyuHeader::sendHeader('Content-type', 'application/octet-stream');
		TodoyuHeader::sendHeader('Content-disposition', 'attachment; filename=' . $filename);
		TodoyuHeader::sendHeader('Content-length', $filesize);
		TodoyuHeader::sendNoCacheHeaders();

			// Delete temporary zip file after download
		TodoyuFileManager::sendFile($zipFile);

		unlink($zipFile);
	}



	/**
	 * Create zip file from assets
	 *
	 * @param	Integer	$idTask
	 * @param	Array	$assetIDs
	 * @return	String	path to zip file
	 */
	private static function createAssetZip($idTask, array $assetIDs) {
		$idTask		= intval($idTask);
		$assetIDs	= TodoyuArray::intval($assetIDs, true, true);

		TodoyuFileManager::makeDirDeep(Todoyu::$CONFIG['EXT']['assets']['cachePath']);

			// Build file path and name
		$zipName	= self::makeZipFileName($idTask, $assetIDs);
		$zipPath	= TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['EXT']['assets']['cachePath'] . DIR_SEP . $zipName);

			// Create zip file
		$zip	= new ZipArchive();
		$status	= $zip->open($zipPath, ZIPARCHIVE::CREATE);

		if( $status !== true ) {
			Todoyu::log('Can\'t create zip archive: ' . $zipPath, LOG_LEVEL_ERROR);
		}

			// Get asset data
		$fields	= 'file_name, file_storage';
		$table	= self::TABLE;

		if(count($assetIDs) > 0)	{
			$where	= 'id IN(' . implode(',', $assetIDs) . ')';
		} else {
			$where = 'id_task = ' . $idTask . ' AND deleted = 0';
		}

			// Get selected asset records
		$assets			= Todoyu::db()->getArray($fields, $table, $where);
			// Counter for identical filenames
		$fileNameCounter= array();

			// Add assets
		foreach($assets as $asset) {
				// Handle doublicated filenames
			$inZipName	= $asset['file_name'];
				// If filename is already in archive, postfile with a counter
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
				Todoyu::log('Failed to add asset to zipfile', LOG_LEVEL_ERROR, Todoyu::$CONFIG['EXT']['assets']['asset_dir'] . $asset['file_storage'], $asset['file_name']);
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
	 * Generate filename for zip file
	 *
	 * @param	Integer	$idTask
	 * @param	Array	$assetIDs
	 * @return	String	filename of zip
	 */
	private static function makeZipFileName($idTask, array $assetIDs) {
		$idTask		= intval($idTask);
		$assetIDs	= TodoyuArray::intval($assetIDs, true, true);

		$field	= 'date_create';
		$table	= self::TABLE;
		if(count($assetIDs) > 0)	{
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
	 * @return	String
	 */
	public static function getAssetStoragePath($type, $idParent) {
		$type		= intval($type);
		$idParent	= intval($idParent);
		$basePath	= self::getStorageBasePath();

		switch($type) {
			case ASSET_PARENTTYPE_TASK:
				$folder = Todoyu::$CONFIG['EXT']['assets']['TYPES']['task']['folder'];
				break;

			case ASSET_PARENTTYPE_PROJECT:
				$folder = Todoyu::$CONFIG['EXT']['assets']['TYPES']['project']['folder'];
				break;

			default:
				die('INVALID ASSET TYPE');
		}

		$storagePath = TodoyuFileManager::pathAbsolute($basePath . DIR_SEP . $folder . DIR_SEP . $idParent);

		return TodoyuFileManager::makeDirDeep($storagePath);
	}



	/**
	 * Clean filename: replace illegal characters in filename by "_"
	 *
	 * @param	String		$dirtyFileName
	 * @return	String
	 */
	public static function cleanFileName($dirtyFileName) {
		return TodoyuFileManager::makeCleanFilename($dirtyFileName);
	}

}

?>