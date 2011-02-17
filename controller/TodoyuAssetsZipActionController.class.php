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
 * Asset zip download action controller
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsZipActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params) {
		restrict('assets', 'general:use');
	}



	/**
	 * Download multiple assets in a zip archive
	 *
	 * @param	Array		$params
	 */
	public function downloadAction(array $params) {
		$idTask		= intval($params['task']);
		$assetIDs	= TodoyuArray::intExplode(',', $params['assets'], true, true);

		if( sizeof($assetIDs) > 0 ) {
			foreach( $assetIDs as $idAsset)	{
				if( ! TodoyuAssetRights::isSeeAllowed( $idAsset )) {
					TodoyuAssetRights::restrictSee($idAsset);
				}
			}

			TodoyuAssetManager::downloadAssetsZipped($idTask, $assetIDs);
		} else {
			die("NO ASSETS SELECTED");
		}
	}

}

?>