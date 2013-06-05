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
 * Asset tasktab action controller
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTasktabActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params) {
		Todoyu::restrict('assets', 'general:use');
	}



	/**
	 * Get asset list
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuAssetsAssetRenderer::renderTaskList($idTask);
	}



	/**
	 * Get tab content
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function tabAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuAssetsAssetRenderer::renderTabContent($idTask);
	}

}

?>