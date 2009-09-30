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
 * Renderer for assets
 *
 * @package		Todoyu
 * @subpackage	Assets
 */

class TodoyuAssetRenderer {

	/**
	 * Render content for the task tab
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTabContent($idTask) {
		$idTask	= intval($idTask);

		$content	= self::renderListControll($idTask);
		$content	.=self::renderList($idTask);

		return $content;
	}



	/**
	 * Render asset list view
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderList($idTask) {
		$idTask	= intval($idTask);

		$data	= array(
			'idTask'	=> $idTask,
			'assets'	=> TodoyuAssetManager::getTaskAssets($idTask)
		);

		return render('ext/assets/view/list.tmpl', $data);
	}



	/**
	 * Render list controll elements
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderListControll($idTask) {
		$idTask	= intval($idTask);

		$data	= array(
			'idTask' => $idTask,
			'labels'	=> array(
				'newfile'			=> 'LLL:assets.upload.new',
				'downloadselection'	=> 'LLL:assets.download.selection'
			)
		);

		return render('ext/assets/view/list-controll.tmpl', $data);
	}



	/**
	 * Render upload form
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderUploadForm($idTask) {
		$idTask		= intval($idTask);

			// Construct form object
		$xmlPath	= 'ext/assets/config/form/upload.xml';
		$form		= new TodoyuForm($xmlPath);
		$form		= TodoyuFormHook::callBuildForm($xmlPath, $form, $idTask);

			// Get form data
		$formData	= array(
			'id_task'	=> $idTask
		);
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $idTask);

			// Set form data
		$form->setFormData($formData);
		$form->setRecordID($idTask);

			// Render
		$data	= array(
			'idTask'	=> $idTask,
			'formhtml'	=> $form->render()
		);

		return render('ext/assets/view/uploadform.tmpl', $data);
	}



	/**
	 * Render upload-frame content
	 *
	 * @param	Integer		$idTask
	 * @param	String		$fileName
	 * @return	String
	 */
	public static function renderUploadframeContent($idTask, $fileName) {
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> '<script type="text/javascript">window.parent.Todoyu.Ext.assets.Upload.uploadFinished(' . $idTask . ', \'' . $fileName . '\');</script>'
		);

		return render('core/view/htmldoc.tmpl', $data);
	}

}

?>