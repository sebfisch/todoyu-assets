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
 * Asset zip download action controller
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsZipActionController extends TodoyuActionController {

	/**
	 * Download multiple assets in a zip archive
	 *
	 * @param	Array		$params
	 */
	public function downloadAction(array $params) {
		$idTask		= intval($params['task']);
		$assetIDs	= TodoyuDiv::intExplode(',', $params['assets'], true, true);

		if( sizeof($assetIDs) > 0 ) {
			TodoyuAssetManager::downloadAssetsZipped($idTask, $assetIDs);
		} else {
			die("NO ASSETS SELECTED");
		}
	}

}

?>