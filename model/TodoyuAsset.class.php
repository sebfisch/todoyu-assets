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
	public function getUserID() {
		return parent::getUserID('create');
	}



	/**
	 * Get uploader user object
	 *
	 * @return	User
	 */
	public function getUser() {
		return parent::getUser('create');
	}



	/**
	 * Get file storage path
	 *
	 * @return	String
	 */
	public function getFileStoragePath() {
		$basePath	= TodoyuAssetManager::getStorageBasePath();
		$filePath	= $this->get('file_storage');

		return TodoyuFileManager::pathAbsolute($basePath . DIRECTORY_SEPARATOR . $filePath);
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
	 * Check if asset is public
	 *
	 * @return	Bool
	 */
	public function isPublic() {
		return $this->get('is_public') == 1;
	}

}

?>