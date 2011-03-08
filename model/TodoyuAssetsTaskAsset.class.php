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
 * Task asset object
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTaskAsset extends TodoyuAssetsAsset {

	/**
	 * Get task ID
	 *
	 * @return	Integer
	 */
	public function getTaskID() {
		return $this->getParentID();
	}



	/**
	 * Get task object
	 *
	 * @return	Task
	 */
	public function getTask() {
		return TodoyuProjectTaskManager::getTask($this->getTaskID());
	}



//	/**
//	 * Store a file as task asset
//	 *
//	 * @param	String		$path
//	 * @param	String		$name
//	 * @param	String		$mime
//	 * @return	Integer
//	 */
//	public static function store($path, $name, $mime) {
//		$pathFile	= self::storeFile($path, $name);
//
////		return self::addDocumentTemplate($pathFile, $name, $mime);
//	}



//	/**
//	 * Store a file in the storage directory
//	 *
//	 * @param	String		$path			Path to (temporary) source file
//	 * @param	String		$name			Filename
//	 * @return	String|Boolean
//	 */
//	private static function storeFile($path, $name) {
//		$storageBasePath	= TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['EXT']['assets']['basePath']);
//		$storageCachePath	= TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['EXT']['assets']['cachePath']);
//
//		TodoyuFileManager::makeDirDeep($storageBasePath);
//		TodoyuFileManager::makeDirDeep($storageCachePath);
//
//		return TodoyuFileManager::addFileToStorage($storageBasePath, $path, $name, true);
//	}
}

?>