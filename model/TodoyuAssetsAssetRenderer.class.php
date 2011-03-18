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
 * Renderer for assets
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsAssetRenderer {

	/**
	 * Render content for the task tab
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTabContent($idTask) {
		restrict('assets', 'general:use');

		$idTask		= intval($idTask);
		$numAssets	= TodoyuAssetsAssetManager::getNumTaskAssets($idTask);
		$locked		= TodoyuProjectTaskManager::isLocked($idTask);

		if( $locked ) {
			if( $numAssets === 0 ) {
				$content = self::renderLockedMessage();
			} else {
				$content = self::renderList($idTask);
			}
		} else {
			if( $numAssets === 0 ) {
				$content = self::renderUploadForm($idTask);
			} else {
				$content = self::renderListControll($idTask);
				$content .= self::renderList($idTask);
			}
		}

		return $content;
	}



	/**
	 * Render locked message
	 *
	 * @return	String
	 */
	public static function renderLockedMessage() {
		$tmpl	= 'ext/comment/view/locked.tmpl';

		return render($tmpl);
	}




	/**
	 * Render asset list view
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderList($idTask) {
		$idTask	= intval($idTask);

		$tmpl	= 'ext/asset/view/list.tmpl';
		$data	= array(
			'idTask'	=> $idTask,
			'assets'	=> TodoyuAssetsAssetManager::getTaskAssets($idTask)
		);

		return render($tmpl, $data);
	}



	/**
	 * Render list controll elements
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderListControll($idTask) {
		$idTask	= intval($idTask);

		$tmpl	= 'ext/asset/view/list-controll.tmpl';
		$data	= array(
			'idTask' => $idTask
		);

		return render($tmpl, $data);
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
		$xmlPath	= 'ext/asset/config/form/upload.xml';
		$form		= TodoyuFormManager::getForm($xmlPath, $idTask);

			// Get form data
		$formData	= array(
			'id_task'		=> $idTask,
			'MAX_FILE_SIZE'	=> intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size'])
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

		return render('ext/asset/view/uploadform.tmpl', $data);
	}



	/**
	 * Render upload-frame content
	 *
	 * @param	Integer		$idTask
	 * @param	String		$fileName
	 * @return	String
	 */
	public static function renderUploadframeContent($idTask, $fileName) {
		$idTask		= intval($idTask);
		$tabLabel	= TodoyuAssetsTaskAssetViewHelper::getTabLabel($idTask);

		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.assets.Upload.uploadFinished(' . $idTask . ', \'' . $tabLabel . '\');')
		);

		return render($tmpl, $data);
	}



	/**
	 * Render upload-frame content if upload has failed
	 *
	 * @param	Integer		$error
	 * @param	String		$fileName
	 * @return	String
	 */
	public static function renderUploadframeContentFailed($error, $fileName) {
		$error		= intval($error);
		$maxFileSize= intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size']);

		$commands	= 'window.parent.Todoyu.Ext.assets.Upload.uploadFailed(' . $error . ', \'' . $fileName . '\', ' . $maxFileSize . ');';

		return TodoyuRenderer::renderUploadIFrameJsContent($commands);
	}

}

?>