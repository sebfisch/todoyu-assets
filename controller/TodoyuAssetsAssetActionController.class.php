<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
 * Asset action controller
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsAssetActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params) {
		Todoyu::restrict('assets', 'general:use');
	}



	/**
	 * Asset download request
	 * Send file headers and binary data to the browser
	 * This action can't be called via AJAX
	 *
	 * @param	Array		$params
	 */
	public function downloadAction(array $params) {
		$idAsset	= intval($params['asset']);

			// If asset is not public, person need the right so see also not public assets
		if( TodoyuAssetsRights::isSeeAllowed($idAsset) ) {
			TodoyuAssetsRights::restrictSee($idAsset);
		}

		if( TodoyuAssetsAssetManager::downloadAsset($idAsset) == false ) {
				// Download failed, reload current page instead
			TodoyuHeader::location(TodoyuRequest::getReferer());
		}
	}



	/**
	 * Delete an asset
	 *
	 * @param	Array		$params
	 */
	public function deleteAction(array $params) {
		$idAsset	= intval($params['asset']);
		$asset		= TodoyuAssetsAssetManager::getAsset($idAsset);

			// If asset is not uploaded by current person, he needs delete rights
		if( !TodoyuAssetsRights::isDeleteAllowed($idAsset) ) {
			TodoyuAssetsRights::restrictDelete($idAsset);
		}

			// Delete the asset
		TodoyuAssetsAssetManager::deleteAsset($idAsset);

			/**
			 * @todo	Currently this works, but if tasks can be in different parents, it could also be a project...
			 */
		$idTask		= $asset->getParentID();

		$tabLabel	= TodoyuAssetsTaskAssetViewHelper::getTabLabel($idTask);

		TodoyuHeader::sendTodoyuHeader('idTask', $idTask);
		TodoyuHeader::sendTodoyuHeader('tabLabel', $tabLabel);
	}



	/**
	 * Toggle asset public visibilty
	 *
	 * @param	Array	$params
	 */
	public function togglevisibilityAction(array $params) {
		Todoyu::restrictInternal();

		$idAsset	= intval($params['asset']);
		TodoyuAssetsAssetManager::getAsset($idAsset);

		TodoyuAssetsAssetManager::togglePublic($idAsset);
	}

}

?>