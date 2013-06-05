<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * Asset info action controller
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsPreviewActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params) {
		Todoyu::restrict('assets', 'general:use');
	}



	/**
	 * Default action
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		return $this->getAction($params);
	}



	/**
	 * @param	Array	$params
	 * @return	String
	 */
	public function getAction(array $params) {
		$idAsset	= intval($params['asset']);

		return TodoyuAssetsAssetRenderer::renderPreview($idAsset);
	}

}

?>