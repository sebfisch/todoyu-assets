<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
		restrict('assets', 'general:use');
	}



	/**
	 * Asset download request
	 * Send file headers and binary data to the browser
	 * This action can't be called over AJAX
	 *
	 * @param	Array		$params
	 */
	public function downloadAction(array $params) {
		$idAsset	= intval($params['asset']);
		$asset		= TodoyuAssetManager::getAsset($idAsset);

			// If asset if not public, person need the right so see also not public assets
		if( ! Todoyu::person()->isInternal() && ! $asset->isPublic() ) {
			restrict('assets', 'asset:seeAll');
		}

		TodoyuAssetManager::downloadAsset($idAsset);
	}



	/**
	 * Delete an asset
	 *
	 * @param	Array		$params
	 */
	public function deleteAction(array $params) {
		$idAsset	= intval($params['asset']);
		$asset		= TodoyuAssetManager::getAsset($idAsset);

			// If asset is not uploaded by current person, he needs delete rights
		if( ! $asset->isCurrentPersonCreator() ) {
			restrict('assets', 'asset:delete');
		}

			// Delete the asset
		TodoyuAssetManager::deleteAsset($idAsset);

			/**
			 * @todo	Currently this works, but if tasks can be in different parents, it could also be a project...
			 */
		$idTask		= $asset->getParentID();

		$tabLabel	= TodoyuTaskAssetViewHelper::getTabLabel($idTask);

		TodoyuHeader::sendTodoyuHeader('idTask', $idTask);
		TodoyuHeader::sendTodoyuHeader('tabLabel', $tabLabel);
	}



	/**
	 * Toggle asset public visibilty
	 *
	 * @param array $params
	 */
	public function togglevisibilityAction(array $params) {
		restrictInternal();

		$idAsset	= intval($params['asset']);
		$asset		= TodoyuAssetManager::getAsset($idAsset);

		TodoyuAssetManager::togglePublic($idAsset);
	}
}

?>