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
 * Asset action controller
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsAssetActionController extends TodoyuActionController {

	/**
	 * Asset download request
	 * Send file headers and binary data to the browser
	 * This action can't be called over ajax
	 *
	 * @param	Array		$params
	 */
	public function downloadAction(array $params) {
		$idAsset	= intval($params['asset']);
		$asset		= TodoyuAssetManager::getAsset($idAsset);

			// If asset is not uploaded by current user, he needs download rights
		if( $asset->getUserID() != userid() ) {
			restrict('assets', 'asset:download');

				// If asset if not public, user need the right so see also not public assets
			if( ! $asset->isPublic() ) {
				restrict('assets', 'asset:seeAll');
			}
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

			// If asset is not uploaded by current user, he needs delete rights
		if( $asset->getUserID() != userid() ) {
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
	 * Toggle
	 *
	 * @param array $params
	 */
	public function togglevisibilityAction(array $params) {
		$idAsset	= intval($params['asset']);
		$asset		= TodoyuAssetManager::getAsset($idAsset);

			// If asset is not uploaded by current user, he needs delete rights
		if( $asset->getUserID() != userid() ) {
			restrict('assets', 'asset:makepublic');
		}

		TodoyuAssetManager::togglePublic($idAsset);
	}
}


?>