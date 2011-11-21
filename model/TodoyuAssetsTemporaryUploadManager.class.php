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
 * Manage temporary uploaded files during task creation
 * Add files to cache folder an keep track of current files
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTemporaryUploadManager {

	/**
	 * Session key where the temp path is stored
	 *
	 * @var	String
	 */
	private static $keyPath = 'assets/path';

	/**
	 * Session key where the temp files are stored (file infos)
	 *
	 * @var	String
	 */
	private static $keyFiles = 'assets/files';



	/**
	 * Add a temporary file
	 *
	 * @param	Array	$file		File upload info array from php
	 */
	public static function addFile(array $file) {
		$storagePath	= self::storeFile($file['tmp_name']);

		$info	= array(
			'name'	=> $file['name'],
			'type'	=> $file['type'],
			'size'	=> $file['size'],
			'path'	=> $storagePath,
			'time'	=> NOW,
			'key'	=> md5($storagePath)
		);

		self::addFileInfo($info);
	}



	/**
	 * Delete a temporary uploaded file
	 *
	 * @param	String		$key		Key of the file (created at upload)
	 */
	public static function deleteFile($key) {
		$files	= self::getFiles();

		foreach($files as $index => $file) {
			if( $file['key'] === $key ) {
				TodoyuFileManager::deleteFile($file['path']);
				unset($files[$index]);
				break;
			}
		}

		TodoyuSession::set(self::$keyFiles, $files);
	}



	/**
	 * Get all temporary uploaded files
	 *
	 * @return	Array
	 */
	public static function getFiles() {
		return TodoyuArray::assure(TodoyuSession::get(self::$keyFiles));
	}



	/**
	 * Destroy the upload session
	 * - Remove uploaded files
	 * - Reset path and file infos
	 *
	 */
	public static function destroy() {
		self::removeFiles();
		self::removePath();
		self::removeFileInfos();
	}



	/**
	 * Hook. Removed temp files
	 *
	 */
	public static function hookRemoveTempSessionFiles() {
		self::destroy();
	}



	/**
	 * Hook. Remove temp files before creating a new task
	 * 
	 */
	public static function hookTaskCreate() {
		self::destroy();
	}



	/**
	 * Store a file to the temporary session folder
	 *
	 * @param	String			$sourceFile		Path to temporary uploaded file
	 * @return	String|Boolean	Path to file in session folder or false
	 */
	private static function storeFile($sourceFile) {
		$sourceFile		= TodoyuFileManager::pathAbsolute($sourceFile);
		$storagePath	= self::getStoragePath();
		$randomName		= md5(NOW . $sourceFile . uniqid());
		$targetFile		= TodoyuFileManager::pathAbsolute($storagePath . '/' . $randomName);

		TodoyuFileManager::makeDirDeep(dirname($targetFile));

		$success		= rename($sourceFile, $targetFile);

		return $success ? $targetFile : false;
	}


	/**
	 * Add file information to session
	 *
	 * @param	Array	$info
	 */
	private static function addFileInfo(array $info) {
		$files	= self::getFiles();
		$files[]= $info;

		TodoyuSession::set(self::$keyFiles, $files);
	}



	/**
	 * Remove all file infos from session
	 *
	 */
	private static function removeFileInfos() {
		TodoyuSession::set(self::$keyFiles, array());
	}



	/**
	 * Get path to store files for current session
	 *
	 * @return	String		Session storage path file temp files
	 */
	private static function getStoragePath() {
		if( ! self::hasPath() ) {
			$random			= md5(NOW . Todoyu::personid() . uniqid());
			$storagePath	= TodoyuFileManager::pathAbsolute(PATH_CACHE . '/files/assets/' . $random);
			self::setPath($storagePath);
		}

		return self::getPath();
	}



	/**
	 * Remove temporary uploaded files
	 *
	 */
	private static function removeFiles() {
		if( self::hasPath() ) {
			TodoyuFileManager::deleteFolder(self::getPath());
		}
	}



	/**
	 * Get storage path from session
	 *
	 * @return	String
	 */
	private static function getPath() {
		return TodoyuSession::get(self::$keyPath);
	}



	/**
	 * Check whether a path is already stored in session
	 *
	 * @return	Boolean
	 */
	private static function hasPath() {
		return TodoyuSession::isIn(self::$keyPath);
	}



	/**
	 * Set session storage path
	 *
	 * @param	String		$storagePath
	 */
	private static function setPath($storagePath) {
		TodoyuSession::set(self::$keyPath, $storagePath);
	}


	/**
	 * Remove session storage path
	 *
	 */
	private static function removePath() {
		TodoyuSession::remove(self::$keyPath);
	}

}

?>