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
 * Assets file upload action controller for task edit form inline
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTaskEditActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params) {
		restrict('assets', 'general:use');
	}



	/**
	 * Refresh options of asset file selector
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function assetfileoptionsAction(array $params) {
		$idTask	= intval($params['id_task']);

		return TodoyuAssetsTaskEditRenderer::renderAssetFileOptions($idTask);
	}



	/**
	 * Get upload form for assets
	 *
	 * @param	Array	$params
	 */
	public static function assetsuploadformAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuAssetsTaskEditRenderer::renderAssetUploadForm($idTask);
	}



	/**
	 * Default action: upload an asset
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		return $this->uploadassetfileAction($params);
	}



	/**
	 * Upload an asset file
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function uploadassetfileAction(array $params) {
		$idTask	= intval($params['assetfile']['id_task']);

		$file	= TodoyuRequest::getUploadFile('file', 'assetfile');
		$error	= intval($file['error']);

			// Check again for file limit
		$maxFileSize	= intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size']);
		if( $file['size'] > $maxFileSize ) {
			$error	= UPLOAD_ERR_FORM_SIZE;
		}
			// Check length of file name
		if( strlen($file['name']) > Todoyu::$CONFIG['EXT']['assets']['max_length_filename'] ) {
			$file['error']	= 3;
		}

			// Render frame content. Success or error
		if( $error === UPLOAD_ERR_OK && ! $file['error'] ) {
			TodoyuAssetsAssetManager::addTaskAssetTemporary($file['tmp_name'], $file['name'], $file['type']);

			return TodoyuAssetsTaskEditRenderer::renderUploadframeContent($file['name'], $idTask);
		} else {
				// Notify upload failure
			TodoyuLogger::logError('File upload failed: ' . $file['name'] . ' (ERROR:' . $error . ')');

			return TodoyuAssetsTaskEditRenderer::renderUploadframeContentFailed($error, $file['name'], $idTask);
		}
	}



	/**
	 * Delete temporary (uploaded prior to creation of task) asset file
	 *
	 * @param	Array	$params
	 */
	public static function deletetempassetfileAction(array $params) {
		$filename	= $params['filename'];

		$success	= TodoyuAssetsAssetManager::deleteTemporaryAsset($filename);

		TodoyuHeader::sendTodoyuHeader('success', $success);
	}



	/**
	 * Delete all temporary (uploaded prior to creation of task) asset files
	 *
	 * @param	Array	$params
	 */
	public static function deletealltempassetfilesAction(array $params) {
		TodoyuAssetsAssetManager::deleteAllTemporaryAssets();
	}

}

?>