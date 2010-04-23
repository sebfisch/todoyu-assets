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
 * Asset upload action controller
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsUploadActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params) {
		restrict('assets', 'general:use');
	}



	/**
	 * Upload an asset
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		$idTask		= intval($params['asset']['id_task']);

		$file	= TodoyuRequest::getUploadFile('file', 'asset');

			// Render frame content. Success or error
		if( $file === false || $file['error'] !== UPLOAD_ERR_OK ) {
			Todoyu::log('File upload failed: ' . $file['name'] . ' (ERROR:' . $file['error'] . ')', LOG_LEVEL_ERROR);

			return TodoyuAssetRenderer::renderUploadframeContentFailed($file['error'], $file['name']);
		} else {
			$idAsset	= TodoyuAssetManager::addTaskAsset($idTask, $file['tmp_name'], $file['name'], $file['type']);

			return TodoyuAssetRenderer::renderUploadframeContent($idTask, $file['name']);
		}
	}

}

?>



