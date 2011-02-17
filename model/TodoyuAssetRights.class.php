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
 * Asset rights functions
 *
 * @package		Todoyu
 * @subpackage	Asset
 */
class TodoyuAssetRights {

	/**
	 * Deny access
	 * Shortcut for asset
	 *
	 * @param	String		$right		Denied right
	 */
	private static function deny($right) {
		TodoyuRightsManager::deny('asset', $right);
	}



	/**
	 * Check whether a person is allowed to see the asset
	 *
	 * @param	Integer		$idAsset
	 * @return	Boolean
	 */
	public static function isSeeAllowed($idAsset) {
		$idAsset	= intval($idAsset);
		$asset		= TodoyuAssetManager::getAsset($idAsset);

		$idParent	= $asset->getParentID();
		$typeParent	= $asset->getParentType();

		switch($typeParent) {
			case ASSET_PARENTTYPE_TASK:
				if( TodoyuTaskRights::isSeeAllowed($idParent) ) {
					if( allowed('asset', 'asset:seeAll') || $asset->isPublic() ) {
						return true;
					}
				}
				break;
		}

		return false;
	}



	/**
	 * Check whether a person is allowed to delete the asset
	 *
	 * @param	Integer		$idAsset
	 * @return	Boolean
	 */
	public static function isDeleteAllowed($idAsset) {
		$idAsset	= intval($idAsset);
		$asset		= TodoyuAssetManager::getAsset($idAsset);

		$idParent	= $asset->getParentID();
		$typeParent	= $asset->getParentType();

		switch($typeParent) {
			case ASSET_PARENTTYPE_TASK:
				if( TodoyuTaskRights::isSeeAllowed($idParent) ) {
					if( self::isSeeAllowed($idAsset) ) {
						if( allowed('asset', 'asset:delete') ) {
							return true;
						}
					}
				}
				break;
		}

		return false;
	}



	/**
	 * Restrict access to person which are allowed to see the asset
	 *
	 * @todo	state deny message more precisely
	 * @param	Integer		$idAsset
	 */
	public static function restrictSee($idAsset) {
		$idAsset	= intval($idAsset);

		if( ! self::isSeeAllowed($idAsset) ) {
			self::deny('asset:see');
		}
	}



	/**
	 * Restrict access to persons which are allowed to delete assets from this task
	 *
	 * @todo	state deny message more precisely
	 * @param	Integer		$idComment
	 */
	public static function restrictDelete($idAsset) {
		if( ! self::isDeleteAllowed($idAsset) ) {
			self::deny('asset:delete');
		}
	}

}
?>