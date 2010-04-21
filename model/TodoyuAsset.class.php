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
 * Asset object
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAsset extends TodoyuBaseObject {

	/**
	 * Initialize the asset object
	 *
	 * @param	Integer		$idAsset
	 */
	public function __construct($idAsset) {
		parent::__construct($idAsset, 'ext_assets_asset');
	}



	/**
	 * Get parenttype of the asset (ex: task)
	 *
	 * @return	Integer
	 */
	public function getParentType() {
		return $this->get('parenttype');
	}



	/**
	 * Get id of parent element
	 *
	 * @return	Integer
	 */
	public function getParentID() {
		return $this->get('id_parent');
	}



	/**
	 * Get ID of the uploader
	 *
	 * @return	Integer
	 */
	public function getPersonID() {
		return parent::getPersonID('create');
	}



	/**
	 * Get uploader person object
	 *
	 * @return	TodoyuPerson
	 */
	public function getPerson() {
		return parent::getPerson('create');
	}



	/**
	 * Get file storage path
	 *
	 * @return	String
	 */
	public function getFileStoragePath() {
		$basePath	= TodoyuAssetManager::getStorageBasePath();
		$filePath	= $this->get('file_storage');

		return TodoyuFileManager::pathAbsolute($basePath . DIR_SEP . $filePath);
	}



	/**
	 * Get filesize
	 *
	 * @return	Integer
	 */
	public function getFilesize() {
		return $this->get('file_size');
	}



	/**
	 * Get mime type
	 *
	 * @return	String
	 */
	public function getMimeType() {
		return $this->get('file_type') . '/' . $this->get('file_subtype');
	}



	/**
	 * Get filename
	 *
	 * @return	String
	 */
	public function getFilename() {
		return $this->get('file_name');
	}



	/**
	 * Check whether asset is public
	 *
	 * @return	Boolean
	 */
	public function isPublic() {
		return $this->get('is_public') == 1;
	}

}

?>