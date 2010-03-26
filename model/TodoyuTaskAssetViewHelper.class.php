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
 * Task specific asset functions
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuTaskAssetViewHelper {


	/**
	 * Get labeltext for the asset tab in the task
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function getTabLabel($idTask) {
		$idTask	= intval($idTask);

		$numAssets	= TodoyuAssetManager::getNumTaskAssets($idTask);

		if( $numAssets === 0 ) {
			$label	= Label('assets.tab.noAssets');
		} elseif( $numAssets === 1 ) {
			$label	= '1 ' . Label('assets.tab.asset');
		} else {
			$label	= $numAssets . ' ' . Label('assets.tab.assets');
		}

		return $label;
	}



	/**
	 * Get the content for the asset tab in the task
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function getTabContent($idTask) {
		$idTask		= intval($idTask);
		$content	= '';
		$numAssets	= TodoyuAssetManager::getNumTaskAssets($idTask);

		if( $numAssets === 0 ) {
			$content	= TodoyuAssetRenderer::renderUploadForm($idTask);
		} else {
			$content	= TodoyuAssetRenderer::renderTabContent($idTask);
		}

		return $content;
	}

}

?>